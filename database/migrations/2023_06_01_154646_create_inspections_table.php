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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->date('last_inspection')->nullable();
            $table->date('next_inspection')->nullable();
            $table->string('location_name');
            $table->string('lat');
            $table->string('lng');
            $table->longText('comment')->nullable();
            $table->longText('office_comment')->nullable();
            $table->string('report')->nullable();
            $table->boolean('status')->nullable(); //In-Service/Out-of-Service

            $table->boolean('notification_send')->default(0);
            $table->boolean('notification_send_two')->default(0);
            
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
        Schema::dropIfExists('inspections');
    }
};
