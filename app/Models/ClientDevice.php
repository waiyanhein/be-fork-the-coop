<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClientDevice extends Model
{
    const AUTH_HEADER_DEVICE_ID = "X-Device-Id";
    const AUTH_HEADER_TOKEN = "X-Token";
    const AUTH_HEADER_PHONE_NUMBER = "X-Phone-Number";

    const OS_ANDROID = 1;
    const OS_IOS = 2;

    protected $fillable = [
        'nickname',
        'os',
        'device_id',
        'phone_number',
        'latitude',
        'longitude'
    ];

    public function receiverDevices()
    {
        return $this->hasMany(ReceiverDevice::class, 'host_device_id', 'id');
    }

    public static function getSupportedOsList()
    {
        return [
          static::OS_ANDROID => 'Android',
          static::OS_IOS => 'IOS',
        ];
    }

    public static function generateDeviceToken()
    {
        $random = Str::random(12);
        $uuid = Str::uuid();
        $timeHash = md5(time());

        return $uuid . $random . $timeHash;
    }

    public function findNearestEligibleReceivers(): array
    {
        // $devices that are already registered as receivers for more than x times
        $invalidNumbersForReceivers = ReceiverDevice::havingRaw('COUNT(phone_number) > ' . ((int) config('app.receivers_eligible_limit')))
            ->having('phone_number', '!=', $this->phone_number)
            ->groupBy('phone_number')->get()->pluck('phone_number')->all();

        // get the nearest numbers where the number is not the current device's number and not registered as receiver more than x times
        $nearestDevicesQuery = ClientDevice::selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude ) ) ) ) AS distance', [$this->latitude, $this->longitude, $this->latitude])
            ->where('client_devices.phone_number', '!=', $this->phone_number);
        if ($invalidNumbersForReceivers) {
            $nearestDevicesQuery = $nearestDevicesQuery->whereNotIn('phone_number', $invalidNumbersForReceivers);
        }
        $nearestDevicesQuery = $nearestDevicesQuery->having('distance', '<', config('app.nearest_devices_radius'))//km
        ->orderBy('distance');

        $nearestDevices = $nearestDevicesQuery->take(100)->get(); // 100 should be enough

        $uniqueReceiverNumbers = [ ];
        // get the unique x numbers
        foreach ($nearestDevices as $nearestDevice) {
            if (count($uniqueReceiverNumbers) == (int)config('app.receivers_per_device')) {
                break;
            }
            if (in_array($nearestDevice->phone_number, $uniqueReceiverNumbers)) {
                continue;
            }
            $uniqueReceiverNumbers[] = $nearestDevice->phone_number;
        }

        return $uniqueReceiverNumbers;
    }
}
