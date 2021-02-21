<?php

namespace App\Http\Requests;

use App\Models\ClientDevice;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceLocation extends FormRequest
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
        return [
            'latitude' => [ 'required', 'numeric' ],
            'longitude' => [ 'required', 'numeric' ]
        ];
    }

    public function persist(): ClientDevice
    {
        $device = auth_device();
        $device->latitude = $this->get('latitude');
        $device->longitude = $this->get('longitude');
        $device->save();

        return $device;
    }
}
