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
        Schema::create('pickup_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('term_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('trip_id');
            $table->string('status')->nullable(); //pickup or dropoff
            $table->string('location_name');
            $table->string('lat');
            $table->string('lng');
            $table->string('time')->nullable();
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
        Schema::dropIfExists('pickup_points');
    }
};
