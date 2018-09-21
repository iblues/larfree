<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Admin\Api\System;

use App\Models\System\SystemComponent;
use Illuminate\Http\Request;
use Larfree\Controllers\ApisController as Controller;
use App\Models\Component;
use Larfree\Libs\ComponentSchemas;
use Larfree\Libs\Schemas;
class ComponentController extends Controller
{
    public function __construct(SystemComponent $model )
    {
        $this->model = $model;
        parent::__construct();
    }

    public function show($id, Request $request)
    {
        return $this->model->where('key',$id)->first();
    }

    public function module($module,$action, Request $request){
        //根据test.test|chart.line.line的格式获取参数
        $config = ComponentSchemas::getComponentConfig($module,$action);

        //如果是配置类别,特殊处理下
        if(substr($module,0,7)=='config.'){
            $module = 'config';
        }

        //替换参数中的变量
        $config = json_encode($config);
        $config = str_replace('{$COMPONENT}',$module,$config);
        $config = str_replace('{$COMPONENT_API}',str_ireplace('.','/',$module),$config);
        $config = json_decode($config,true);

        //增加url和show的url,供后端使用
        if(@$config['fields'])
            array_walk($config['fields'],[$this,'linkToUrl']);
        if(@$config['search'])
            array_walk($config['search'],[$this,'linkToUrl']);

        return $config;
    }

    public function linkToUrl(&$value){
        if(isset($value['link']) && ! @$value['link']['url']){
            $model = $value['link']['model'];
            $model = substr($model[1],stripos($model[1],'\Models\\')+8);
            $model = explode('\\',$model);
            if(@$model[1]) {
                $url = humpToLine($model[0]) . '/' . humpToLine(substr($model[1], strlen($model[0])));
                $show = humpToLine($model[0]) . '.' . humpToLine(substr($model[1], strlen($model[0])));
            }else {
                $url = humpToLine($model[0]);
                $show = humpToLine($model[0]);
            }

            $value['link']['url'] = route('admin.api.root').'/'.$url.'?pageSize=30';
            $value['link']['show'] = 'edit/'.$show.'/{{id}}';
        }
    }


}
