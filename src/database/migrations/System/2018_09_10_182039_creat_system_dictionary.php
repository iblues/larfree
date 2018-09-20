<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatSystemDictionary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_dictionary', function (Blueprint $table) {
            $table->increments('id')->comment('id');//唯一编号
            $table->string('key')->comment('key');
            $table->string('model')->comment('model');
            $table->string('value')->comment('值');
//            $table->unsignedMediumInteger('site')->comment('城市')->default(1);
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
