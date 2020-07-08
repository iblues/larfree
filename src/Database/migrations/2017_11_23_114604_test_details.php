<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TestDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_test_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('test_id')->comment('所属项目');
            $table->string('uid')->commnet('所属用户');
            $table->text('content')->comment('内容');
            $table->text('thumb')->comment('缩略图');
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
        Schema::dropIfExists('test_test_detail');
    }
}
