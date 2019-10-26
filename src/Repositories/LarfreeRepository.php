<?php

namespace Larfree\Repositories;

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
     * @author Blues
     * @param string $field
     * @return $this
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
     * @param array $query
     * @return array
     */
    public function parseRequest($query = [])
    {

        $advFieldSearch = $this->advFieldSearch;
        $model = $this->model;

        if ($advFieldSearch === false) {
            return $this;
        }
        if (!$query)
            return $this;


        foreach ($query as $key => $val) {
            //高级搜索模式
            if (array_get($advFieldSearch, 0, '*') == '*' || in_array($key, $advFieldSearch))
                $model->AdvWhere($key, $val);
        }

        if (@$query['@sort']) {
            $sort = explode('.', @$query['@sort']);
            $model->orderBy($sort[0], $sort[1]);
        } else {
            $model->orderBy('id', 'desc');
        }

        $this->model = $model;

        return $this;

    }

    /**
     * @author Blues
     * @return $this
     */
    static function new(){
        return app(static::class);
    }
}
