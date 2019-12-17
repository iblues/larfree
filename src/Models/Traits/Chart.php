<?php
/**
 * Created by PhpStorm.
 * User: lanyang
 * Date: 2019/12/16
 * Time: 6:23 PM
 */

namespace Larfree\Models\Traits;


/**
 * 统计组建
 * Trait Chart
 * @package Larfree\Models
 */
trait Chart
{
    /**
     * 基于timestamp的统计
     */
    function scopeTimeChart($query, array $y, string $xField = 'create_at', $xFormat = '%Y-%m-%d %H:%M:%S')
    {
//        $ySql="({$ySql})";//方便实现字段之见的 操作
//        $query2 = clone $query;

        $field = [DB::raw("FROM_UNIXTIME(UNIX_TIMESTAMP({$xField}),'{$xFormat}') as x")];
        $query = $query->groupBy('x')->orderBy("x", "asc");
        $countData = [];


        foreach ($y as $k => $q) {
            $queryField = $field;
            $newQuery = clone $query;
            $queryField[] = DB::raw('(' . $q['sql']['field'] . ') as y');
            $count = $newQuery->select($queryField)->whereRaw($q['sql']['where'])->get();

            foreach ($count as $v) {
                $countData[$v->x][$k] = $v->y;
            }
        }
//        $date = array_keys($countData);

//        $minDate = min($date);
//        $maxDate = date('Y-m-d');

        return $countData;

    }
}
