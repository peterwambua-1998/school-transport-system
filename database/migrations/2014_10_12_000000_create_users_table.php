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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('user_type')->default('staff'); //attendant or driver
            $table->string('phone_num')->nullable();
            $table->string('staff_num')->nullable();
            $table->string('id_num')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('password_changed')->default(0);
            $table->boolean('using_app')->default(0);
            $table->string('grade')->nullable(); //use this only for non stream schools
            $table->string('stream')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->string('rand_number')->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->boolean('is_parent_two')->default(0);
            $table->boolean('is_other')->default(0);
            $table->unsignedBigInteger('linked_to')->nullable();
            $table->string('image')->nullable();
            $table->string('gender')->nullable();
            $table->string('relationship')->nullable();
            $table->boolean('status')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
