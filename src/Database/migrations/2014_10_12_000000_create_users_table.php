<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

            $table->unsignedBigInteger('belong_id')->comment('从属企业')->default(0);//子账号
//            $table->string('type',10)->comment('用户类型')->default('customer');

            $table->string('name')->comment('备注');
            $table->string('api_token', 64)->unique();
            $table->string('first_site')->comment('首次注册城市')->default(1);
            $table->char('ip',15)->comment('ip地址')->index()->default('');


            $table->string('phone',11)->comment('电话')->default('');
            $table->string('email')->comment('邮箱')->default('');
            $table->string('password')->comment('密码')->default('');


            $table->string('openid')->comment('微信openid')->index()->default('');
            $table->string('uniqueid')->comment('微信uniqueid')->index()->default('');


            $table->rememberToken();
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
