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
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insurance_id');
            $table->unsignedBigInteger('reported_by');
            $table->string('claim_number');
            $table->string('claim_mileage');
            $table->date('claim_date');
            $table->date('claim_approve_date')->nullable();
            $table->string('report')->nullable(); //file
            $table->string('statement')->nullable(); //file
            $table->string('claim_garage')->nullable();
            $table->string('claim_garage_lat')->nullable(); 
            $table->string('claim_garage_lng')->nullable();
            $table->longText('comment')->nullable();
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
        Schema::dropIfExists('insurance_claims');
    }
};
