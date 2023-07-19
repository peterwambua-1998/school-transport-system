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
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warranty_id');
            $table->string('comment');
            $table->string('date');
            $table->string('mileage');
            $table->unsignedBigInteger('recorded_by');
            $table->timestamps();

            $table->foreign('warranty_id')->references('id')->on('warranties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warranty_claims');
    }
};
