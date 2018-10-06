<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_test', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('标题');
            $table->longText('content')->comment('详情');
            $table->unsignedInteger('user_id')->comment('用户id')->index();//索引
//            $table->unsignedInteger('select')->comment('普通下拉');
            $table->text('upload');//上传的图片  用json存
            $table->text('file');//上传的文件  用json存
            $table->decimal('price',10,2)->index();// 10位,10个小数
            $table->float('float',10,10);//随机浮点 10位,10个小数
            $table->ipAddress('ip');//ip
            $table->timestamp('timestamp')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('datetime');
            $table->timestamps();//update_at update_at
//            $table->unique(['title', 'content']);//内容不得重复
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_test');
    }
}
