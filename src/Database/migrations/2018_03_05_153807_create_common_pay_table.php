<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonPayTable extends Migration
{
    /**
     * 用来进行统一支付的
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_pay', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model')->comment('Model地址');
            $table->string('price')->comment('金额');
            $table->string('order_id')->comment('订单号');
            $table->string('pay_id')->comment('流水号')->default(0);
            $table->string('title')->comment('支付简介');
            $table->integer('user_id')->comment('用户Id')->default(0);
            $table->integer('pay_type')->comment('支付方式')->default(0);
            $table->tinyInteger('status')->comment('状态')->index()->default(0);
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
        Schema::dropIfExists('common_pay');
    }
}
