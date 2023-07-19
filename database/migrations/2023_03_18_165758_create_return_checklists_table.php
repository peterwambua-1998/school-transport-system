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
        Schema::create('return_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schooltrip_id');
            $table->unsignedBigInteger('student_id');
            $table->string('attendance'); // present or absent
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
        Schema::dropIfExists('return_checklists');
    }
};
