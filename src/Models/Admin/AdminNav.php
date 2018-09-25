<?php

namespace Larfree\Models\Admin;

use Larfree\Models\Api;
use DB;//载入DB类
class AdminNav extends Api{

    public function getNavsTree(){
        $where['id'] = 2;
        $res = $this::query()->where($where)->get()->toArray();
        return $res;
    }

    /**
     * 以数组的方式获取所有菜单
     * @param int $catID 菜单分类id
     * @param int $parent_id 菜单id
     * @return mixed
     */
    public function getAllMenu($parent_id = 0,$catID = '') {
        $where['status'] = 1;
        $where['parent_id'] = $parent_id;
        if($catID)
            $where['catid'] = $catID;
        $menus = $this->where($where)->orderBy('ranking' ,'desc')->get()->toArray();
        if (!empty($menus)) {
            foreach ($menus as $key => $menu) {
                $menu_child = $this->getAllMenu($menu['id']);
                if (!empty($menu_child)) {
                    //子菜单不为空放在 child 数组中
                    $menus[$key]['child']= $menu_child;
                }else{
                    $menus[$key]['child']= "";
                }
            }
        }
        return $menus;
    }



}
