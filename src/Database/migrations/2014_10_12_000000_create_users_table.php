<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_user', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('编号');
            $table->string('name')->comment('备注');
            $table->string('avatar')->default('')->comment('头像');
            $table->char('phone', 11)->comment('电话')->default('');
            $table->string('email')->comment('邮箱')->default('');
            $table->string('password')->comment('密码')->default('');
            $table->string('openid')->comment('微信openid')->index()->default('');
            $table->string('uniqueid')->comment('微信uniqueid')->index()->default('');
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('common_user');
    }
}
