<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_nav', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('标题');
            $table->text('url')->comment('链接');
            $table->string('class')->default('')->comment('图标class');
            $table->string('module')->default('')->comment('是否使用module');
            $table->integer('parent_id')->commonet('上级')->default(0)->index();//父id
            $table->integer('cat_id')->default(0)->comment('分类');//父id
            $table->tinyInteger('status')->comment('状态')->default(1);//父id
            $table->integer('ranking')->default(50)->comment('排序');
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
        Schema::dropIfExists('admin_nav');
    }
}
