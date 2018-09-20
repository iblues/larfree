<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatAuthArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice_router', function (Blueprint $table) {
            $table->increments('id')->comment('id');//唯一编号
            $table->string('name')->comment('范围');
            $table->string('rule')->comment('规则');
            $table->string('param')->comment('参数');
            $table->boolean('status')->comment('有效')->default(0);
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
