<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatAuthAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_action', function (Blueprint $table) {
            $table->increments('id')->comment('id');//唯一编号
            $table->string('name')->comment('角色名');
            $table->string('resource')->comment('资源');
            $table->string('action')->comment('操作');
            $table->string('param')->comment('参数');
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

        Schema::dropIfExists('auth_action');
    }
}
