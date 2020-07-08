<?php
/**
 * Larfree Api类
 * @author blues
 */

namespace Larfree\Controllers\Admin\Api\Admin;

use Iblues\AnnotationTestUnit\Annotation as ATU;
use Larfree\Controllers\AdminApisController as Controller;
use Larfree\Services\Admin\AdminNavService;

class NavController extends Controller
{
    /**
     * @var AdminNavService
     */
    public $service;

    public function __construct(AdminNavService $service)
    {
        $this->service = $service;
        $this->service->setAdmin();
        parent::__construct();
    }


    /**
     * 获取树状结构的菜单栏
     * @return array
     * @ATU\Api(
     *     @ATU\Login(1),
     *     @ATU\Response({"data":{{"id":true}} })
     * )
     */
    public function tree()
    {
        $nav = $this->service->getTreeNav();
        return $nav;
    }

}
