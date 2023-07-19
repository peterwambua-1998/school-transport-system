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
        Schema::create('school_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grade'); //year term school fees 
            $table->string('amount');
            $table->string('year');
            $table->unsignedBigInteger('term');
            $table->boolean('status')->default(0); //paid (1) or unpaid (0)
            $table->string('invoice_num');
            $table->boolean('active')->default(1);
            $table->timestamps();
            //(student) (school fees id) (amt paid) (date paid) (payment method) (receipt_number)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_fees');
    }
};
