<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VkData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vk_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vk_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('sex');
            $table->string('nickname');
            $table->string('screen_name');
            $table->string('bdate');
            $table->string('city');
            $table->string('status');
            $table->integer('followers_count');
            $table->string('home_town');
            $table->string('activities');
            $table->text('personal');			
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
        Schema::drop('vk_data');
    }
}
