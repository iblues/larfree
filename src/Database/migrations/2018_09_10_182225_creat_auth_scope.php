<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatAuthScope extends Migration
{
    /**
     * Run the migrations.
     * 范围粒度筛选
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_scope', function (Blueprint $table) {
            $table->increments('id')->comment('id');//唯一编号
            $table->string('name')->comment('范围');
            $table->string('scope')->comment('scope函数');
            $table->json('param')->comment('参数');
            $table->string('rule')->comment('其他规则');
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

        Schema::dropIfExists('auth_scope');
    }
}
