<?php

namespace Tests\Feature\Controllers;

use App\Models\ClientDevice;
use App\Models\ReceiverDevice;
use Illuminate\Http\Response;
use Tests\TestCase;

class RegisterDeviceControllerTest extends TestCase
{
    /** @test */
    public function clientCanRegisterDevice()
    {
        $body = [
            'nickname' => $this->faker->name,
            'device_id' => $this->faker->md5,
            'os' => $this->faker->randomElement(array_keys(ClientDevice::getSupportedOsList())),
            'phone_number' => $this->faker->phoneNumber,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude
        ];

        $this->json('POST', route('api.device.register'), $body)->assertSuccessful();

        $this->assertEquals(1, ClientDevice::count());
        $device = ClientDevice::first();
        $this->assertEquals($body['nickname'], $device->nickname);
        $this->assertEquals(false, $device->veritifed);
        $this->assertNotEmpty($device->token);
        $this->assertEquals($body['phone_number'], $device->phone_number);
        $this->assertEquals($body['latitude'], $device->latitude);
        $this->assertEquals($body['longitude'], $device->longitude);
    }

    /** @test */
    public function registeringSameNumberWithSameDeviceWillRefreshToken()
    {
        $existingDevice = factory(ClientDevice::class)->create();
        $oldToken = $existingDevice->token;

        $body = [
            'nickname' => $this->faker->name,
            'device_id' => $existingDevice->device_id,
            'os' => $this->faker->randomElement(array_keys(ClientDevice::getSupportedOsList())),
            'phone_number' => $existingDevice->phone_number,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude
        ];

        $this->json('POST', route('api.device.register'), $body)->assertSuccessful();

        $existingDevice->refresh();
        $this->assertNotEquals($oldToken, $existingDevice->token);
    }

    //this also test to include one distant device
    /** @test */
    public function whenDeviceIsRegisteredNearestDevicesWillBeRegisteredAsReceivers()
    {
        //nearest devices
        $nearestReceiverDevices = factory(ClientDevice::class, 10)->create([
            'latitude' => 51.5870615,
            'longitude' => -0.1645405
        ]);
        // this will not be registered as receiver cause not within the radius. One device might be taken as distant one
        factory(ClientDevice::class, 10)->create([
            'latitude' => 71.5870615,
            'longitude' => -2.1645405
        ]);
        // this wil not be registered as receiver even though it is within the radius - reason -> already registered x times.
        $invalidRecipient = factory(ClientDevice::class)->create([
            'latitude' => 51.5870615,
            'longitude' => -0.1645405
        ]);
        factory(ReceiverDevice::class, 10)->create([
           'host_device_id'=> $invalidRecipient->id,
           'phone_number' => $invalidRecipient->phone_number
        ]);
        // already registered x times. - will not be registered as receiver too
        $anotherInvalidRecipient = factory(ClientDevice::class)->create([
            'latitude' => 71.5870615,
            'longitude' => -1.1645405
        ]);
        factory(ReceiverDevice::class, 10)->create([
            'host_device_id'=> $anotherInvalidRecipient->id,
            'phone_number' => $anotherInvalidRecipient->phone_number
        ]);

        $body = [
            'nickname' => $this->faker->name,
            'device_id' => $this->faker->md5,
            'os' => $this->faker->randomElement(array_keys(ClientDevice::getSupportedOsList())),
            'phone_number' => $this->faker->phoneNumber,
            'latitude' => 51.5886482,
            'longitude' => -0.1646952
        ];
        $this->json('POST', route('api.device.register'), $body)->assertSuccessful();

        $clientDevice = ClientDevice::where('nickname', $body['nickname'])
            ->where('device_id', $body['device_id'])
            ->where('phone_number', $body['phone_number'])
            ->first();

        $this->assertEquals(11, $clientDevice->receiverDevices()->count());
        foreach($nearestReceiverDevices as $nearestReceiverDevice) {
            $this->assertDatabaseHas('receiver_devices', [
               'phone_number' => $nearestReceiverDevice->phone_number,
               'host_device_id' => $clientDevice->id,
            ]);
        }
    }

    /** @test */
    public function registerDeviceDoesNotRegisterSameNumbersAsReceivers()
    {
        //nearest devices - same number but with different device ID
        factory(ClientDevice::class, 10)->create([
            'latitude' => 51.5870615,
            'longitude' => -0.1645405,
            'phone_number' => 4567891011 // same number but only one should be selected
        ]);

        $body = [
            'nickname' => $this->faker->name,
            'device_id' => $this->faker->md5,
            'os' => $this->faker->randomElement(array_keys(ClientDevice::getSupportedOsList())),
            'phone_number' => $this->faker->phoneNumber,
            'latitude' => 51.5886482,
            'longitude' => -0.1646952
        ];
        $this->json('POST', route('api.device.register'), $body)->assertSuccessful();

        $clientDevice = ClientDevice::where('nickname', $body['nickname'])
            ->where('device_id', $body['device_id'])
            ->where('phone_number', $body['phone_number'])
            ->first();

        $this->assertEquals(1, $clientDevice->receiverDevices()->count());
    }

    // this is just for testing if the auth.device middleware is working
    /** @test */
    public function clientCannotUpdateLocationIfDeviceIsNotLoggedIn()
    {
        $body = [
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude
        ];
        $this->put(route('api.device.updateLocation'), $body)
            ->assertUnauthorized();
    }

    /** @test */
    public function clientCanUpdateLocation()
    {
        $body = [
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude
        ];
        $this->json('PUT', route('api.device.updateLocation'), $body, $this->getAuthDeviceHeader())
            ->assertSuccessful();

        $authDevice = $this->getAuthDevice();
        $authDevice->refresh();
        $this->assertEquals(number_format($body['latitude'], 4), number_format($authDevice->latitude, 4));
        $this->assertEquals(number_format($body['longitude'], 4), number_format($authDevice->longitude, 4));
    }

    /** @test */
    public function clientCanRegisterReceivers()
    {
        $authDevice = $this->getAuthDevice();
        $authDevice->latitude = 51.5886482;
        $authDevice->longitude = -0.1646952;
        $authDevice->save();
        //nearest devices
        $nearestReceiverDevices = factory(ClientDevice::class, 10)->create([
            'latitude' => 51.5870615,
            'longitude' => -0.1645405
        ]);

        $this->json('POST', route('api.device.registerReceivers'), [ ], $this->getAuthDeviceHeader())
            ->assertSuccessful();

        $authDevice->refresh();
        $receivers = $authDevice->receiverDevices()->get();

        $this->assertEquals(10, count($receivers));
        foreach ($nearestReceiverDevices as $nearestReceiverDevice) {
            $this->assertDatabaseHas('receiver_devices', [
                'host_device_id' => $authDevice->id,
                'phone_number' => $nearestReceiverDevice->phone_number
            ]);
        }
    }

    /** @test */
    public function itDoesNotUpdateExistingReceiversWhenNewReceiversCountIsLessThanCurrentOne()
    {
        $authDevice = $this->getAuthDevice();
        $authDevice->latitude = 51.5886482;
        $authDevice->longitude = -0.1646952;
        $authDevice->save();

        for ($i = 0; $i < 10; $i++) {
            factory(ReceiverDevice::class)->create([
                'host_device_id' => $authDevice->id,
                'phone_number' => $this->faker->phoneNumber
            ]);
        }
        //nearest devices
        factory(ClientDevice::class, 5)->create([
            'latitude' => 51.5870615,
            'longitude' => -0.1645405
        ]);

        $this->json('POST', route('api.device.registerReceivers'), [ ], [
            ClientDevice::AUTH_HEADER_TOKEN => $authDevice->token,
            ClientDevice::AUTH_HEADER_PHONE_NUMBER => $authDevice->phone_number,
            ClientDevice::AUTH_HEADER_DEVICE_ID => $authDevice->device_id,
        ])
            ->assertSuccessful();

        $authDevice->refresh();
        $this->assertEquals(10, $authDevice->receiverDevices()->count());
    }

    /** @test */
    public function itUpdatesExistingReceivers()
    {
        $authDevice = $this->getAuthDevice();
        $authDevice->latitude = 51.5886482;
        $authDevice->longitude = -0.1646952;
        $authDevice->save();

        for ($i = 0; $i < 5; $i++) {
            factory(ReceiverDevice::class)->create([
                'host_device_id' => $authDevice->id,
                'phone_number' => $this->faker->phoneNumber
            ]);
        }
        //nearest devices
        factory(ClientDevice::class, 10)->create([
            'latitude' => 51.5870615,
            'longitude' => -0.1645405
        ]);

        $this->json('POST', route('api.device.registerReceivers'), [ ], [
            ClientDevice::AUTH_HEADER_TOKEN => $authDevice->token,
            ClientDevice::AUTH_HEADER_PHONE_NUMBER => $authDevice->phone_number,
            ClientDevice::AUTH_HEADER_DEVICE_ID => $authDevice->device_id,
        ])
            ->assertSuccessful();

        $authDevice->refresh();
        $this->assertEquals(10, $authDevice->receiverDevices()->count());
    }

    /** @test */
    public function registerReceiverReturnsRightJsonResponse()
    {
        $authDevice = $this->getAuthDevice();
        $authDevice->latitude = 51.5886482;
        $authDevice->longitude = -0.1646952;
        $authDevice->save();
        //nearest devices
        factory(ClientDevice::class, 10)->create([
            'latitude' => 51.5870615,
            'longitude' => -0.1645405
        ]);

        $request = $this->json('POST', route('api.device.registerReceivers'), [ ], $this->getAuthDeviceHeader());
        $authDevice->refresh();
        $expectedJson = [
            'device_id' => $authDevice->device_id,
            'id' => $authDevice->id,
            'latitude' => (string)$authDevice->latitude,
            'longitude' => (string)$authDevice->longitude,
            'nickname' => $authDevice->nickname,
            'phone_number' => $authDevice->phone_number,
            'token' => $authDevice->token,
            'receiver_numbers' => $authDevice->receiverDevices()->get()->pluck('phone_number')->all()
        ];

        $request->assertJsonFragment($expectedJson);
    }
}
