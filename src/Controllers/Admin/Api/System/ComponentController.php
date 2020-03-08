<?php
/**
 * Larfree Api类
 * @author blues
 */

namespace Larfree\Controllers\Admin\Api\System;

use App\Models\System\SystemComponent;
use Illuminate\Http\Request;
use ApiController as Controller;
use App\Models\Component;
use Larfree\Libs\ComponentSchemas;
use Larfree\Libs\Schemas;

class ComponentController extends Controller
{
    public function __construct()
    {
//        $this->model = $model;
        parent::__construct();
    }

    public function show($id, Request $request)
    {
//        return $this->model->where('key',$id)->first();
    }

    public function module($module, $action, Request $request)
    {
        //根据test.test|chart.line.line的格式获取参数
        $config = ComponentSchemas::getComponentConfig($module, $action);
        //如果是配置类别,特殊处理下
        if (substr($module, 0, 7) == 'config.') {
            $module = 'config';
        }

        //替换参数中的变量
        $config = json_encode($config);
        $config = str_replace('{$COMPONENT}', $module, $config);
        $config = str_replace('{$COMPONENT_API}', str_ireplace('.', '/', $module), $config);
        $config = json_decode($config, true);


        //增加url和show的url,供后端使用
        if (@$config['fields'])
            array_walk($config['fields'], [$this, 'linkToUrl']);
        if (@$config['search'])
            array_walk($config['search'], [$this, 'linkToUrl']);
        if (@$config['component_fields'])
            array_walk($config['component_fields'], [$this, 'linkToUrl']);
        if (@$config['adv_search'])
            array_walk($config['adv_search'], [$this, 'linkToUrl']);
        return $config;
    }

    /**
     * 把link转换下 让前端可以识别的配置
     * @param $value
     * @return mixed
     * @throws \Larfree\Exceptions\ApiException
     * @author Blues
     */
    public function linkToUrl(&$value)
    {
        //没有link的不用管了
        if (!isset($value['link'])) {
            return $value;
        }

        //设置了link,model 但是没有设置component_param.api的
        if (isset($value['link']) && isset($value['link']['model'])) {

            $model = $value['link']['model'];
            if ($model) {
                $model = substr($model[1], stripos($model[1], '\Models\\') + 8);
                $model = explode('\\', $model);
                if (@$model[1]) {
                    $url = humpToLine($model[0]) . '/' . humpToLine(substr($model[1], strlen($model[0])));
                    $show = humpToLine($model[0]) . '.' . humpToLine(substr($model[1], strlen($model[0])));
                } else {
                    $url = humpToLine($model[0]);
                    $show = humpToLine($model[0]);
                }

                // 这个是用于前端读取后端列表用的. 基本上是必填.
                if (!isset($value['component_param']['api'])) {
                    $value['link']['url'] = '/' . $url . '?pageSize=30'; //以后 逐步废弃
                    $value['component_param']['api'] = '/' . $url . '?pageSize=30';  //代替link中的url
                }

                // 用于前端调跳转到配置用.
                if (!isset($value['component_param']['show'])) {
                    $value['component_param']['show'] = 'edit/' . $show . '/{{id}}'; //代替link
                    $value['link']['show'] = 'edit/' . $show . '/{{id}}'; //以后 逐步废弃
                }

                // 设置了select 但是没有设置name的. 先过渡下
                if (isset($value['link']['select']) && !isset($value['component_param']['name'])) {
                    $value['component_param']['key'] = $value['link']['select'][0];
                    $name = '';
                    foreach ($value['link']['select'] as $k => $t) {
                        if ($k > 0) {
                            $t .= "{{$t}} ";
                        }
                    }
                    $value['component_param']['name'] = $t;  //代替link中的url
                }


            }
        }


        if (!isset($value['component_param']['api'])) {
            apiError("当link.model为空时,schemas.{$value['key']}.component_param.api必填");
        }


    }


}
