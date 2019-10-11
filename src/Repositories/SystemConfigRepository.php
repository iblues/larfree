<?php

namespace Larfree\Repositories;

use Larfree\Libs\Schemas;
use Larfree\Models\System\SystemConfig;
use Larfree\Repositories\LarfreeRepository;

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

    public function getAllByCat($category){
        //获取对应的配置文件 , 还需要进一步处理
//        $data = Schemas::getSchemas('Config.'.$category);

        $data = $this->model->link()->where('cat',$category)->get();
        return $data->plick('key','value');
    }

    /**
     * 批量赋值
     * @author Blues
     * @param array $data
     * @param $cat
     */
    public function updateConfigByCat(array $data, $cat)
    {
        foreach($data as $k=>$v){
            if($v) {
                $this->model->updateOrCreate(
                    ['key' => $k, 'cat' => $cat],
                    ['value' => $v]
                );
            }
        }
    }

}
