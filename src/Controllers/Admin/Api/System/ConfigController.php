<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Admin\Api\System;

use Illuminate\Http\Request;
use Larfree\Controllers\ApisController as Controller;
use App\Models\System\SystemConfig;
use Larfree\Libs\Schemas;
class ConfigController extends Controller
{
    public function __construct(SystemConfig $model )
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request)
    {
        return $list = Schemas::getAllConfig();
    }

    public function show($cat, Request $request)
    {
        $data = Schemas::getSchemas('Config.'.$cat);

        $configDatas=[];
        $datas = $this->model->link()->where('cat',$cat)->get();
        $datas->map(function($v) use(&$configDatas){
            $configDatas[$v->key] = $v->value;
        });

        foreach($data as $schema){
            $configDatas[$schema['key']] = isset($configDatas[$schema['key']])?$configDatas[$schema['key']]:'';
        }

        return $configDatas;
    }

    /**
     * 更新
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $cat)
    {
        $data = $request->all();
        $model = $this->model;
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