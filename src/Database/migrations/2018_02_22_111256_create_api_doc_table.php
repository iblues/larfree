<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiDocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_api_doc', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->index()->comment('url');
            $table->string('show_url')->index()->comment('展示url');
            $table->string('method')->default('get')->comment('请求方式')->index();
            $table->string('title')->comment('api标题')->index();
            $table->text('content')->comment('描述');
            $table->boolean('auth')->comment('登录');
            $table->string('group')->default('default')->comment('分组')->index();
            $table->text('cache')->comment('上次数据');
            $table->integer('status')->comment('是否启用')->index();

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
        Schema::dropIfExists('system_api_doc');
    }
}
