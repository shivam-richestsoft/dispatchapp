<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable()->comment('Super Admin');
            $table->string('last_name')->nullable()->comment('Super Admin');
            $table->string('name')->nullable()->comment('agency');
            $table->string('email');
            $table->string('phone_number');
            $table->string('password');
            $table->integer('status')->default(1);
            $table->integer('role')->default(0);
            $table->string('remember_token')->nullable();
            $table->integer('created_by_id');
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
        Schema::dropIfExists('admin');
    }
}
