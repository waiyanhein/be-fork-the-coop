<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ClientDevice;
use Faker\Generator as Faker;

$factory->define(ClientDevice::class, function (Faker $faker) {
    return [
        'nickname' => $faker->name,
        'verified' => $faker->randomElement([ true, false ]),
        'token' => ClientDevice::generateDeviceToken(),
        'os' => $faker->randomElement(array_keys(ClientDevice::getSupportedOsList())),
        'device_id' => $faker->md5,
        'phone_number' => $faker->unique()->phoneNumber,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
    ];
});
