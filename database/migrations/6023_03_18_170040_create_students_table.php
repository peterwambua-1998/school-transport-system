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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable(); //new
            $table->string('image')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('grade');
            $table->unsignedBigInteger('stream');
            $table->string('add_num');
            $table->string('lat')->nullable();//pickup
            $table->string('lng')->nullable();//pickup
            $table->string('lat_drop')->nullable();//dropoff
            $table->string('lng_drop')->nullable();//dropoff
            $table->boolean('pick_up')->default(1);
            $table->boolean('confirm_pickup_parent')->default(0);
            $table->boolean('confirm_pickup_driver')->default(0);
            $table->boolean('pickup_changed')->default(0);
            $table->boolean('bus_assigned')->default(0)->nullable();
            $table->string('gender');
            $table->unsignedBigInteger('pickup_id')->nullable();
            $table->unsignedBigInteger('dropoff_id')->nullable(); //references pickup point
            $table->boolean('transport')->nullable(); // 1 - transport, 0 - own
            $table->tinyInteger('trip_type')->nullable(); //1 - pickup, 2 - drop-off, 3 - both
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('parent_two')->nullable();
            $table->unsignedBigInteger('other')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->nullOnDelete();
            $table->foreign('parent_id')->references('id')->on('users')->nullOnDelete();

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
