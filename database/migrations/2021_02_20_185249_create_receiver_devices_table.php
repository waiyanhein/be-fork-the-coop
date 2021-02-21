<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiverDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiver_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('host_device_id');
            $table->string('phone_number');
            $table->timestamps();

            $table->foreign('host_device_id')->references('id')->on('client_devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receiver_devices');
    }
}
