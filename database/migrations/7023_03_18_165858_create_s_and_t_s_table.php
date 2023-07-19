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
        Schema::create('s_and_t_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('trip_id')->nullable();
            $table->string('something')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
            $table->foreign('trip_id')->references('id')->on('trips')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('s_and_t_s');
    }
};
