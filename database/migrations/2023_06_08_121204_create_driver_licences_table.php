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
        Schema::create('driver_licences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->string('dl_number');
            $table->string('dl_class');
            $table->date('date_issued');
            $table->date('date_renewed');
            $table->integer('validity');//in years
            $table->date('exp_date');
            $table->boolean('status');

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
        Schema::dropIfExists('driver_licences');
    }
};
