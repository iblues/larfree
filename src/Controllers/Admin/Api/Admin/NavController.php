<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace Larfree\Controllers\Admin\Api\Admin;

use App\Repositories\Admin\NavRepository;
use Larfree\Controllers\AdminApisController;
use Larfree\Models\Admin\AdminNav;
use Illuminate\Http\Request;
use ApiController as Controller;
class NavController extends AdminApisController
{
    public function __construct(NavRepository $repository )
    {
        $this->repository = $repository;
        parent::__construct();
    }

    /**
     * 获取树状结构的菜单栏
     * @return array
     */
    public function tree(){
        $nav = $this->repository->getAdminNav();
        return $nav;
    }
}
