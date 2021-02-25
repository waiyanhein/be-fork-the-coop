<?php

namespace App\Http\Resources;

use App\Models\ReceiverDevice;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDevice extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $receiverNumbers = ReceiverDevice::where('host_device_id', $this->id)->get()->pluck('phone_number')->all();

        $data = [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'nickname' => $this->nickname,
            'verified' => $this->verified,
            'token' => $this->token,
            'phone_number' => $this->phone_number,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];

        if ($receiverNumbers) {
            $data['receiver_numbers'] = $receiverNumbers;
        } else {
            $data['receiver_numbers'] = [ ];
        }

        return $data;
    }
}
