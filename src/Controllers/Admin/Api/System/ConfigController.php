<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace Larfree\Controllers\Admin\Api\System;

use Larfree\Repositories\SystemConfigRepository;
use Illuminate\Http\Request;
use ApiController as Controller;
use App\Models\System\SystemConfig;
use Larfree\Libs\Schemas;
class ConfigController extends Controller
{
    public $repository;
    public function __construct(SystemConfig $model,SystemConfigRepository $repository )
    {
        $this->model = $model;
        $this->repository = $repository;
        parent::__construct();
    }

    public function index(Request $request)
    {
        return $list = Schemas::getAllConfig();
    }

    /**
     * 获取配置 按 分类
     * @author Blues
     * @param $cat
     * @param Request $request
     * @return mixed
     */
    public function show($cat, Request $request)
    {
        return $this->repository->getAllByCat($request->cat);
    }

    /**
     * 更新
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $cat 分类名
     * @return mixed
     */
    public function update(Request $request, $cat)
    {
        $data = $request->all();
        foreach($data as $k=>$v){
            if($v) {
                $this->model->updateOrCreate(
                    ['key' => $k, 'cat' => $cat],
                    ['value' => $v]
                );
            }
        }
    }

}
