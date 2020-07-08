<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/27/027
 * Time: 18:23
 */

namespace Larfree\Libs;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Table
{

    static public function creatTable()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('标题');
            $table->longText('content')->comment('详情');
            $table->unsignedInteger('user_id')->comment('用户id')->index();//索引
            $table->unsignedInteger('select')->comment('普通下拉');
            $table->text('upload');//上传的图片  用json存
            $table->text('file');//上传的文件  用json存
            $table->decimal('price', 10, 2)->index();// 10位,10个小数
            $table->float('float', 10, 10);//随机浮点 10位,10个小数
            $table->ipAddress('ip');//ip
            $table->timestamp('timestamp')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('datetime');
            $table->timestamps();//update_at update_at
//            $table->unique(['title', 'content']);//内容不得重复
        });
    }

    static public function isTableExist($table)
    {
        return Schema::hasTable($table);
        //Schema::hasTable(self::TABLE_NAME)
    }

    /**
     * 创建中间表.
     * @param $table1
     * @param $table2
     * @return bool
     */
    static public function creatLinkTable($table1, $table2)
    {
        $tableName = self::getLinkTableName($table1, $table2);
        if (self::isTableExist($tableName)) {
            return true;
        }
        if ($table1 == $table2) {
            $table2 = $table1.'_sub';
        }
        Schema::create($tableName, function (Blueprint $table) use ($table1, $table2) {
            $table->unsignedInteger(humpToLine($table1).'_id');//索引
            $table->unsignedInteger(humpToLine($table2).'_id');
        });
    }

    /**
     * 获取中间表名
     * @param $table1
     * @param $table2
     * @return string
     */
    static public function getLinkTableName($table1, $table2)
    {
        $table = [humpToLine($table1), humpToLine($table2)];
        sort($table);//看哪个表应该在前面
        return $tableName = 'link_'.$table[0].'_'.$table[1];
    }

    /**
     * 获取数据库表的字段
     * @param $table
     */
    static public function getColumns($table)
    {
        $return = [];
        if (!config('app.debug')) {
            $return = Cache::tags(['table_column'])->get($table.'_columns');
        }

        if (!$return) {
            $return = Schema::getColumnListing($table);
            if ($return && !config('app.debug')) {
                Cache::tags(['table_column'])->put($table.'_columns', $return, 120);
            }
        }
        return $return;
    }
}
