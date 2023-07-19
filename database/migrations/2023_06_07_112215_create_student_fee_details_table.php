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
        Schema::create('student_fee_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_fees_id');
            $table->unsignedBigInteger('fee_id')->nullable();
            $table->string('detail');
            $table->string('detail_amount');
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
        Schema::dropIfExists('student_fee_details');
    }
};
