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
        Schema::create('standin_buses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_vehicle'); // eg veh 1
            $table->unsignedBigInteger('stand_in_vehicle'); // eg veh 2
            $table->date('date_from');
            $table->date('date_to');
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('standin_buses');
    }
};
