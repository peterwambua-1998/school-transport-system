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
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('type'); //
            $table->string('ins_num');
            $table->string('ins_company');
            $table->date('issue_date');
            $table->date('renew_date');
            $table->integer('validity');
            $table->date('exp_date');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('insurances');
    }
};
