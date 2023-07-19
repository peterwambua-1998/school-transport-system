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
        Schema::create('term_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('term_id');
            $table->string('name');
            $table->date('start');
            $table->date('ends');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('year');
            $table->string('location');
            $table->string('transport')->nullable();
            $table->string('within'); //within school days
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('term_id')->references('id')->on('school_term_dates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_events');
    }
};
