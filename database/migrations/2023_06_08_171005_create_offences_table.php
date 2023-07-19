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
        Schema::create('offences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); //driver or attendant
            $table->unsignedBigInteger('trip_id')->nullable();
            $table->string('type'); //ie driver or attendant
            $table->string('offence_type');
            $table->longText('description');
            $table->string('disciplinary_action');
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
        Schema::dropIfExists('offences');
    }
};
