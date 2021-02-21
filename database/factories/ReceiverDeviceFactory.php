<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ReceiverDevice;
use Faker\Generator as Faker;

$factory->define(ReceiverDevice::class, function (Faker $faker) {
    return [
        'phone_number' => $faker->phoneNumber,
        'host_device_id' => function () use ($faker) {
            return factory(\App\Models\ClientDevice::class)->create();
        }
    ];
});
