<?php

namespace Larfree\Models\Admin;

use App\Models\Test\TestTest;
use Larfree\Models\Api;
use DB;//载入DB类
class AdminNav extends Api{


    public function getNavsTree(){
        $where['id'] = 2;
        $res = $this::query()->where($where)->get()->toArray();
        return $res;
    }

    /**
     * 返回后台的目录.
     * @author Blues
     * @param $model
     * @param int $tree 树状结构
     * @param $onlyStatus=1 只要开启的
     * @return array
     */
    static public function getTreeNav($model,$tree=1,$onlyStatus=1){

        if($onlyStatus)
            $model->where('status',1);

        $nav = $model->orderBy('ranking','desc')->get();

        $nav = $nav->toArray();
//        $return  = event(new FilterNavEvent($nav,$model::class));
        if(!$tree)
            return $nav;
        return  listToTree($nav, 'id', 'parent_id', 'children');

    }



}
