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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('inv_num');
            $table->string('amount');
            //$table->string('paid_amount');
            $table->string('status');
            $table->date('date_paid')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
