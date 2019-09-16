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
     * 加载link相关的参数
     * 会自动加载配置中所有的带有的link的参数
     * @author Blues
     */
    public function link($field=[]){
        $this->model=$this->model->link($field);
        return $this;
    }


    public function field($field=''){
        $this->model=$this->model->field($field);
        return $this;
    }
}
