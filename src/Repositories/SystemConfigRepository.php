<?php

namespace Larfree\Repositories;

use Larfree\Libs\Schemas;
use Larfree\Models\System\SystemConfig;

/**
 * Class TestRepositoryEloquent.
 *
 * @package namespace App\Repositories\Test;
 */
class SystemConfigRepository extends LarfreeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SystemConfig::Class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
//        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getAllByCat($category, $key = '')
    {
        //获取对应的配置文件 , 还需要进一步处理
//        $data = Schemas::getSchemas('Config.'.$category);
        if ($key) {
            $data = $this->model->where('key', $key)->first();
            if (!$data) {
                apiError('配置文件不存在');
            }
            return $data->value;

        } else {
            $data = $this->model->link()->where('cat', $category)->get();
            if (!$data) {
                apiError('配置文件不存在');
            }
            return $data->pluck('value', 'key');
        }

    }

    /**
     * 批量赋值
     * @param array $data
     * @param $cat
     * @author Blues
     */
    public function updateConfigByCat(array $data, $cat)
    {
        foreach ($data as $k => $v) {

            if ($v) {
                $this->model->updateOrCreate(
                    ['key' => $k, 'cat' => $cat],
                    ['value' => $v, 'type' => 'json']
                );
            }
        }
    }

}
