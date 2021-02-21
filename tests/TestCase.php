<?php

namespace Tests;

use App\Models\ClientDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    private $authDevice = null;

    protected function getAuthDevice()
    {
        if ($this->authDevice) {
            return $this->authDevice;
        }

        $this->authDevice = factory(ClientDevice::class)->create();

        return $this->authDevice;
    }

    protected function getAuthDeviceHeader()
    {
        $authDevice = $this->getAuthDevice();

        return [
          ClientDevice::AUTH_HEADER_DEVICE_ID => $authDevice->device_id,
          ClientDevice::AUTH_HEADER_PHONE_NUMBER => $authDevice->phone_number,
          ClientDevice::AUTH_HEADER_TOKEN => $authDevice->token
        ];
    }
}
