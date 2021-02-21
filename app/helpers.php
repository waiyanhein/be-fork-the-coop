<?php

if (! function_exists('auth_device')) {
    function auth_device(): ?\App\Models\ClientDevice
    {
        $deviceId = request()->header(\App\Models\ClientDevice::AUTH_HEADER_DEVICE_ID);
        $token = request()->header(\App\Models\ClientDevice::AUTH_HEADER_TOKEN);
        $phoneNumber = request()->header(\App\Models\ClientDevice::AUTH_HEADER_PHONE_NUMBER);

        if (empty($deviceId) || empty($token) || empty($phoneNumber)) {
            //if any of the field is missing, it will return null;
            return null;
        }

        return \App\Models\ClientDevice::where('device_id', $deviceId)
            ->where('token', $token)
            ->where('phone_number', $phoneNumber)
            ->first();
    }
}
