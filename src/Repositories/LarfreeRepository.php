<?php

namespace Larfree\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class TestRepositoryEloquent.
 *
 * @package namespace App\Repositories\Test;
 */
abstract class LarfreeRepository extends BaseRepository
{


    /**
     * @var 能使用搜索的字段列
     * []代表所有,null不允许
     */
    public $advFieldSearch = ['*'];
    protected $model;

    /**
     * 加载link相关的参数
     * 会自动加载配置中所有的带有的link的参数
     * @author Blueste
     */
    public function link($field = [])
    {
        if ($field !== false) {
            $this->model = $this->model->link($field);
        }
        return $this;
    }


    /**
     * 筛选字段
     * @param  string  $field
     * @return $this
     * @author Blues
     */
    public function field($field = '')
    {
        $this->model = $this->model->field($field);
        return $this;
    }


    /**
     *
     * 同一个字段及多个字段组合查询
     * 示例: http://laravel.dev/api/min?id=1&search_id=2&gt_key=2&egt_key=2&lt_key=2&elt_key=3
     * @param  array  $query
     * @return array
     */
    public function parseRequest($query = [])
    {
        $advFieldSearch = $this->advFieldSearch;
        $model          = $this->model;

        if ($advFieldSearch === false) {
            return $this;
        }
        if (!$query) {
            return $this;
        }


        foreach ($query as $key => $val) {
            //高级搜索模式
            if (Arr::get($advFieldSearch, 0, '*') == '*' || in_array($key, $advFieldSearch)) {
                $model->AdvWhere($key, $val);
            }
        }

        if (isset($query['@sort'])) {
            if ($query['@sort']) {
                $sort = explode('.', @$query['@sort']);
                $model->orderBy($sort[0], $sort[1]);
            }
        } else {
            $model->orderBy('id', 'desc');
        }

        $this->model = $model;

        return $this;
    }

    /**
     * @return $this
     * @author Blues
     * @deprecated
     */
    static function new()
    {
        return app(static::class);
    }

    /**
     * @return $this
     * @author Blues
     */
    static function make()
    {
        return app(static::class);
    }

    /**
     * 基于时间的线状统计
     * @param $query
     * @param  array  $y  配置中的y结构 参考chart.line
     * @param  string  $xField
     * @param  string  $xFormat
     * @return array
     * @author Blues
     */
    function TimeChart(array $y, string $xField = 'create_at', $xFormat = '%Y-%m-%d %H:%M:%S')
    {
//        $ySql="({$ySql})";//方便实现字段之见的 操作
//        $query2 = clone $query;

        $field     = [DB::raw("FROM_UNIXTIME(UNIX_TIMESTAMP({$xField}),'{$xFormat}') as x")];
        $model     = $this->model->groupBy('x')->orderBy("x", "asc");
        $countData = [];

        foreach ($y as $k => $q) {
            $queryField   = $field;
            $newQuery     = clone $model;
            $queryField[] = DB::raw('('.$q['sql']['field'].') as y');
            $count        = $newQuery->select($queryField)->whereRaw($q['sql']['where'])->get();
            foreach ($count as $v) {
                $countData[$v->x][$k] = $v->y;
            }
        }
        $this->resetModel();
//        $date = array_keys($countData);

//        $minDate = min($date);
//        $maxDate = date('Y-m-d');

        return $countData;
    }
}
