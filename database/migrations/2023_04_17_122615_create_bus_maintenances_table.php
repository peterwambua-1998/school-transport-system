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
        Schema::create('bus_maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('garage')->nullable();
            $table->string('shift')->nullable();
            $table->longText('description')->nullable();
            $table->string('status'); // daily, routine, off routine
            $table->string('lat')->nullable(); //if its off routine 
            $table->string('lng')->nullable();
            $table->string('place_name')->nullable();
            $table->string('current_km')->nullable();
            $table->string('last_service')->nullable();
            $table->string('next_service')->nullable();
            $table->string('comment')->nullable();
            $table->string('amount')->nullable();
            $table->string('video')->nullable();
            $table->string('video_duration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bus_maintenances');
    }
};
