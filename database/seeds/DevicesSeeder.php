<?php

use Illuminate\Database\Seeder;
use App\Models\ClientDevice;

class DevicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // seed devices in the east finchley
        // this can be used as host device
        factory(ClientDevice::class, 10)->create([
            'latitude' => 51.5886482,
            'longitude' => -0.1646952,
        ]);
        factory(ClientDevice::class, 15)->create([
            'latitude' => 51.5870615,
            'longitude' => -0.1645405
        ]);
    }
}
