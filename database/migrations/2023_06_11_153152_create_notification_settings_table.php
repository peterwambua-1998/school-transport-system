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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('insurance_send_at')->nullable(); //used to tell system day to send notifaction
            $table->string('inspection_send_at')->nullable();
            $table->string('dl_send_at')->nullable();
            $table->string('warranty_send_at')->nullable();

            $table->string('insurance_send_at_two')->nullable();
            $table->string('inspection_send_at_two')->nullable();
            $table->string('dl_send_at_two')->nullable();
            //to be used as place holders
            $table->string('insurance_unit')->nullable();
            $table->string('license_unit')->nullable();
            $table->string('inspection_unit')->nullable();

            //for pick-up and drop-off notification
            $table->string('value')->nullable();
            $table->string('unit_of_measure')->nullable();

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
        Schema::dropIfExists('notification_settings');
    }
};
