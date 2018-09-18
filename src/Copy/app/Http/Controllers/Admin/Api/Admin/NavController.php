<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Admin\Api\Admin;

use App\Models\Admin\AdminNav;
use Illuminate\Http\Request;
use Larfree\Controllers\ApisController as Controller;
class NavController extends Controller
{
    public function __construct(AdminNav $model )
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request)
    {
        if($request->tree) {
            $nav = $this->model->where('status',1)->get();
            $nav = $nav->toArray();
            $nav = listToTree($nav, 'id', 'parent_id', 'child');
            return $nav;
        }else{
            return parent::index($request);
        }
    }
}