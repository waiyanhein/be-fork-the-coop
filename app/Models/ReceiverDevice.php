<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiverDevice extends Model
{
    protected $with = [ 'hostDevice' ];

    public function hostDevice()
    {
        return $this->belongsTo(ClientDevice::class, 'host_device_id', 'id');
    }
}
