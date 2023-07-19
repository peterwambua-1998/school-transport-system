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
        Schema::create('school_trip_payment_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schooltrip_id');
            $table->unsignedBigInteger('student_id');
            $table->boolean('marked')->default(0); //used to show students who have not been added to cheklist
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
        Schema::dropIfExists('school_trip_payment_tables');
    }
};
