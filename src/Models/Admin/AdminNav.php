<?php

namespace Larfree\Models\Admin;

use App\Events\Permission\FilterNavEvent;
use App\Models\Test\TestTest;
use DB;
use Event;
use Larfree\Models\Api;

//载入DB类
class AdminNav extends Api
{


    public function getNavsTree()
    {
        $where['id'] = 2;
        $res         = $this::query()->where($where)->get()->toArray();
        return $res;
    }

    /**
     * 返回后台的目录.
     * @param $model
     * @param  int  $tree  树状结构
     * @param $onlyStatus  =1 只要开启的
     * @return array
     * @author Blues
     */
    static public function getTreeNav($model, $tree = 1, $onlyStatus = 1)
    {
        if ($onlyStatus) {
            $model->where('status', 1);
        }

        $nav = $model->orderBy('ranking', 'desc')->get();

        $return = Event::dispatch('permission.filter_nav', ['nav' => $nav]);
        $nav    = $return[0] ?? $nav;
        //如果还是没有任何菜单
        if (!$nav) {
            return [];
        }
        // 纯数组化 不然listToTree报错
        $nav = json_decode(json_encode($nav), 1);
        if (!$tree) {
            return $nav;
        }
        return listToTree($nav, 'id', 'parent_id', 'children');
    }


}
