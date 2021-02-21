<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterDeviceRequest;
use App\Http\Requests\UpdateDeviceLocation;
use App\Http\Resources\ClientDevice as ClientDeviceResource;
use App\Models\ReceiverDevice;
use Illuminate\Support\Facades\DB;

class RegisterDeviceController extends Controller
{
    public function store(RegisterDeviceRequest $request)
    {
        $request->validated();
        $device = $request->persist();

        return new ClientDeviceResource($device);
    }

    public function updateLocation(UpdateDeviceLocation $request)
    {
        $request->validated();
        $device = $request->persist();

        return new ClientDeviceResource($device);
    }

    public function registerReceivers()
    {
        $device = auth_device();
        DB::beginTransaction();
        try {
            $receiverNumbers = $device->findNearestEligibleReceivers();
            if (count($receiverNumbers) > 0 && count($receiverNumbers) >= $device->receiverDevices()->count()) {
                $device->receiverDevices()->delete();
                foreach ($receiverNumbers as $receiverNumber) {
                    $receiver = new ReceiverDevice();
                    $receiver->host_device_id = $device->id;
                    $receiver->phone_number = $receiverNumber;
                    $receiver->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return new ClientDeviceResource($device);
    }
}
