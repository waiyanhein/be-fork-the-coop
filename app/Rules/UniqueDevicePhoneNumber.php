<?php

namespace App\Rules;

use App\Models\ClientDevice;
use Illuminate\Contracts\Validation\Rule;

//@doc - this rule has to be applied on the phone number field
class UniqueDevicePhoneNumber implements Rule
{
    private $deviceId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($deviceId)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $device = ClientDevice::where('device_id', $this->deviceId)
            ->where('phone_number', $value)
            ->first();

        return ($device)? false: true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Phone number has already been registered on this device.';
    }
}
