<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatUserActionLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('user_action_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户编号')->index();
            $table->unsignedInteger('user_type')->comment('用户类型')->index();
            $table->unsignedTinyInteger('type')->comment('类型')->default(1);
            $table->string('ip',15)->comment('ip');
            $table->string('from',15)->comment('操作来源')->default('system');
            $table->string('site',5)->comment('站点')->default('010');
            $table->text('after_content')->comment('操作前');
            $table->text('before_content')->comment('操作后');
            $table->string('title')->comment('操作');
            $table->string('model')->comment('模块')->index();
            $table->string('key')->comment('target')->default('')->index();


//            $table->foreign('id')->references('id')->on('common_user');//用户外键约束

//            $table->rememberToken();
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
        Schema::dropIfExists('user_action_log');
    }
}
