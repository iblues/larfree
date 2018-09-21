<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiDocReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_api_doc_return', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id')->index();
            $table->string('title')->comment('标题');
            $table->text('return')->comment('返回结果');
            $table->text('return_model')->comment('返回的模型');
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
        Schema::dropIfExists('system_api_doc_return');
    }
}
