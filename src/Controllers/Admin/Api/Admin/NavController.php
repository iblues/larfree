<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace Larfree\Controllers\Admin\Api\Admin;

use App\Repositories\Admin\AdminNavRepository;
use Larfree\Controllers\AdminApisController;
use Illuminate\Http\Request;
use ApiController as Controller;
class NavController extends AdminApisController
{
    public function __construct(AdminNavRepository $repository )
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
