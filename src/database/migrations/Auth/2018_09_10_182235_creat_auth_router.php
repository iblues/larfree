<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatAuthRouter extends Migration
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
            $table->string('name')->comment('角色名');
            $table->string('resource')->comment('资源');
            $table->string('action')->comment('操作');
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
