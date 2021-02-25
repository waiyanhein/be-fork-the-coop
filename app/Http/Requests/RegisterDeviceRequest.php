<?php

namespace App\Http\Requests;

use App\Models\ClientDevice;
use App\Models\ReceiverDevice;
use App\Rules\UniqueDevicePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //@TODO: validate for same device and the same number
        return [
            'nickname' => [  ],
            'device_id' => [ 'required' ],
            'os' => [ 'required', Rule::in(array_keys(ClientDevice::getSupportedOsList())) ],
            'phone_number' => [ 'required', ],// @TODO: remove this. If already registered, login
            'latitude' => [ 'required', 'numeric' ],
            'longitude' => [ 'required', 'numeric' ]
        ];
    }

    public function persist(): ClientDevice
    {
        // login if already registered
        $device = ClientDevice::where('device_id', $this->get('device_id'))
            ->where('phone_number', $this->get('phone_number'))->first();
        if ($device) {
            // refresh the token
            $device->token = ClientDevice::generateDeviceToken();
            $device->save();

            return $device;
        }
        //@TODO: for now it is generating the token straight away
        //@TODO: in the future generate the token when the device or number has been verified.
        $device = new ClientDevice();
        $device->fill(request([
            'nickname',
            'device_id',
            'os',
            'phone_number',
            'latitude',
            'longitude'
        ]));
        $device->token = ClientDevice::generateDeviceToken();
        $device->save();

        //@TODO: run thiwebs in the background Q
        // after registering the device. Configure the receiver devices
        $this->registerReceiversFor($device);

        return $device;
    }

    public function registerReceiversFor(ClientDevice $device)
    {
        $receiverNumbers = $device->findNearestEligibleReceivers();

        //@todo: use batch  insert to improve the performance
        foreach ($receiverNumbers as $receiverNumber) {
            $receiverDevice = new ReceiverDevice();
            $receiverDevice->phone_number = $receiverNumber;
            $receiverDevice->host_device_id = $device->id;
            $receiverDevice->save();
        }
    }
}
