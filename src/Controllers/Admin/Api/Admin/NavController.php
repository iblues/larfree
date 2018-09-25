<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace Larfree\Controllers\Admin\Api\Admin;

use Larfree\Models\Admin\AdminNav;
use Illuminate\Http\Request;
use Larfree\Controllers\ApisController as Controller;
class NavController extends Controller
{
    public function __construct(AdminNav $model )
    {
        $this->model = $model;
        parent::__construct();
    }

    /**
     * 获取树状结构的菜单栏
     * @return array
     */
    public function tree(){
        $nav = $this->model->where('status',1)->get();
        $nav = $nav->toArray();
        $nav = listToTree($nav, 'id', 'parent_id', 'child');
        return $nav;
    }
}