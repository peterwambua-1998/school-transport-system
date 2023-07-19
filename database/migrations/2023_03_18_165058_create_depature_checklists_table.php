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
        Schema::create('depature_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schooltrip_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('grade');
            $table->unsignedBigInteger('stream')->nullable();
            $table->string('attendance'); // present or absent
            $table->date('date');
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
        Schema::dropIfExists('depature_checklists');
    }
};
