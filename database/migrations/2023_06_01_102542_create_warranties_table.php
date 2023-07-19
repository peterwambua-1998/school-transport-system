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
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('waranty_value'); //if km put km if years put years
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->string('dealer')->nullable();
            $table->longText('warranty_parts')->nullable();
            $table->string('measurement')->nullable();
            $table->boolean('notification_send')->default(0);
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
        Schema::dropIfExists('warranties');
    }
};
