<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     * 用户地址表
     * @return void
     */
    public function up()
    {
        Schema::create('address', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户');
            $table->string('name')->comment('收货人名称');
            $table->integer('phone')->comment('收货人手机');
            $table->integer('province_id')->comment('省');
            $table->integer('city_id')->comment('市');
            $table->integer('area_id')->comment('区');
            $table->string('user_address')->comment('详细地址');
            $table->string('code')->comment('邮编');
            $table->integer('is_default')->comment('是否默认');
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
        Schema::dropIfExists('address');
    }
}
