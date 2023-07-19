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
        Schema::create('geofences', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('vehicle_id')->nullable();
            
            $table->string('arrone_first')->nullable();$table->string('arrone_second')->nullable();
            $table->string('arrtwo_first')->nullable();$table->string('arrtwo_second')->nullable();
            $table->string('arrthree_first')->nullable();$table->string('arrthree_second')->nullable();
            $table->string('arrfour_first')->nullable();$table->string('arrfour_second')->nullable();
            $table->string('arrfive_first')->nullable();$table->string('arrfive_second')->nullable();
            $table->string('arrsix_first')->nullable();$table->string('arrsix_second')->nullable();
            $table->string('arrseven_first')->nullable();$table->string('arrseven_second')->nullable();
            $table->string('arreight_first')->nullable();$table->string('arreight_second')->nullable();
            $table->string('arrnine_first')->nullable();$table->string('arrnine_second')->nullable();


            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geofences');
    }
};
