<?php
/**
 * Larfree Api类
 * @author blues
 */

namespace Larfree\Controllers;

use Iblues\AnnotationTestUnit\Annotation as ATU;
use Larfree\Repositories\SystemConfigRepository;
use Illuminate\Http\Request;
use ApiController as Controller;
use App\Models\System\SystemConfig;
use Larfree\Libs\Schemas;
use Larfree\Services\SystemConfigService;

class ConfigBaseController extends Controller
{
    public $repository;
    public $service;

    public function __construct(SystemConfigService $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    public function index(Request $request)
    {
        return $list = Schemas::getAllConfig();
    }

    /**
     * 获取配置 按 分类
     * @param $cat
     * @param Request $request
     * @return mixed
     * @ATU\Api(
     *     path={"plane","home"},
     * )
     * @author Blues
     */
    public function show($cat, Request $request, $key='')
    {
        return $this->service->getAllByCat($request->cat, $key);
    }

    /**
     * 更新
     * @param \Illuminate\Http\Request $request
     * @param string $cat 分类名
     * @return mixed
     * @ATU\Api(
     *     path="plane",
     *     @ATU\Request({"home":{"key":1}})
     * )
     */
    public function update(Request $request, $cat)
    {
        return $this->service->updateConfigByCat($request->all(), $cat);
    }

    public function store(Request $request)
    {
        ApiError('Forbidden!', [], '403');
    }

    public function deleted($id, Request $request)
    {
        ApiError('Forbidden!', [], '403');
    }

}
