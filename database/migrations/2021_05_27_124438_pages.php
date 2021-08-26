<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    
        {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->longText('description');
                $table->integer('status')->default(1);
                $table->integer('type')->default(0)->comment("Ex. About us");
                $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');
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
        //
    }
}
