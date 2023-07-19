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
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number');
            $table->unsignedBigInteger('school_fees_id'); //student school fees
            $table->unsignedBigInteger('student');
            $table->unsignedBigInteger('parent')->nullable();
            $table->string('amount_paid');
            $table->string('payment_method');
            $table->string('balance')->nullable();
            $table->date('date_paid');
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
        Schema::dropIfExists('fee_payments');
    }
};
