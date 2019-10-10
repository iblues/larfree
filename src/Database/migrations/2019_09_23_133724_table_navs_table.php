<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableNavsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_nav', function (Blueprint $table) {
            $table->string('component')->default('')->comment('vue对应的key');
            $table->text('param')->comment('额外参数');
            $table->boolean('visible')->default(1)->comment('icon');
            $table->string('icon')->default('')->comment('是否可见');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
