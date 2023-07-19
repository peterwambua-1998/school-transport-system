<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('attendant_id')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->string('title');
            $table->string('plate_num');
            $table->string('num_of_seats');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('head')->nullable();
            $table->string('speed')->nullable();
            $table->string('image')->nullable();
            $table->string('service_interval')->nullable();
            $table->boolean('active')->nullable();
            $table->string('mileage')->nullable();
            $table->string('last_service')->nullable();
            $table->string('next_service')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
            
            $table->foreign('driver_id')->references('id')->on('users');
            $table->foreign('attendant_id')->references('id')->on('users');
            $table->foreign('route_id')->references('id')->on('routes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
};
