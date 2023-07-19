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
        Schema::create('route_polylines', function (Blueprint $table) {
            $table->id();
            //$table->string('route_time');
            $table->unsignedBigInteger('route_id')->nullable();
            $table->string('origin');
            $table->string('destination');
            $table->string('way_point_1')->nullable();
            $table->string('way_point_2')->nullable();
            $table->string('way_point_3')->nullable();
            $table->string('way_point_4')->nullable();
            $table->string('way_point_5')->nullable();
            $table->string('way_point_6')->nullable();
            $table->string('way_point_7')->nullable();
            $table->string('way_point_8')->nullable();
            $table->timestamps();


            $table->foreign('route_id')->references('id')->on('routes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_polylines');
    }
};
