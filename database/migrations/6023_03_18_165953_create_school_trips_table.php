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
        Schema::create('school_trips', function (Blueprint $table) {
            $table->id();
            $table->string('trip_name');
            //$table->unsignedBigInteger('teacher_id');
            //$table->unsignedBigInteger('vehicle_id');
            $table->string('destination_name')->nullable();
            $table->string('trip_route')->nullable(); //source
            $table->string('destination')->nullable();
            $table->string('dest_app')->nullable(); //used for single dest trips for app to use
            $table->string('status'); //paid or unpaid
            //$table->string('general_grade'); //either general or specific grade
            $table->string('grade')->nullable();
            $table->string('price')->nullable();
            $table->date('trip_date');
            $table->time('departure_time');
            $table->time('return_time');
            $table->boolean('route_changed')->default(0); // yes or no 
            $table->boolean('approved')->default(0); // yes or no
            $table->unsignedBigInteger('term_id');
            $table->boolean('has_more_destinations')->default(0);
            $table->boolean('active')->default(1);

            $table->string('payment_method')->nullable(); //kama ni bank, mpesa 
            $table->string('payment_method_value')->nullable(); //account number, till number

            $table->timestamps();
            //way points
            $table->string('waypont_one')->nullable();
            $table->string('waypont_two')->nullable();
            $table->string('waypont_three')->nullable();
            $table->string('waypont_four')->nullable();
            $table->string('waypont_five')->nullable();
            $table->string('waypont_six')->nullable();
            $table->string('waypont_seven')->nullable();
            $table->string('waypont_eight')->nullable();
            $table->boolean('status_active')->default(1);



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
        Schema::dropIfExists('school_trips');
    }
};
