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
            $table->increments('id')->comment('编号');
            $table->string('type',10)->comment('用户类型')->default('customer');
            $table->string('name')->comment('备注');
            $table->string('api_token', 64)->unique();
            $table->string('first_site')->comment('首次注册城市')->default(1);
            $table->char('ip',15)->comment('ip地址')->index();


            $table->string('phone',11)->comment('电话');
            $table->string('mail')->comment('邮箱');
            $table->string('password')->comment('密码');


            $table->string('openid')->comment('openid')->index();
            $table->string('uniqueid')->comment('uniqueid')->index();


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
        Schema::dropIfExists('common_user');
    }
}
