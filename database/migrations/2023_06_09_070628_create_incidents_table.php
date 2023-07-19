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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('source'); //parent or bus
            $table->string('trip');
            $table->string('date');
            $table->string('type'); //Misbehaviour, Emergency
            $table->string('caused_by'); // driver parent student
            $table->string('video')->nullable();
            $table->longText('description');
            $table->unsignedBigInteger('user_assulter')->nullable(); //this is the person who mis behaved
            $table->unsignedBigInteger('student_assulter')->nullable();
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
        Schema::dropIfExists('incidents');
    }
};
