<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14/014
 * Time: 17:50
 */

namespace Larfree\Components\Base;

use Larfree\Components\Components;

class Table extends Components
{

    static public function component(&$path, &$config, $model)
    {
        parent::component($path, $config, $model);
        if (isset($config['param']['api'])) {
            $api = $config['param']['api'];
        } else {
            $api = $config['config']['config']['api'];
        }
        $response = self::getJSON($api);

        if ($response['data'] && $response['code'] == '200') {
            //设置按钮
            $response['data'] = array_map(function ($array) use ($config) {
                return self::actionHtml($array, $config['config']['config']['action']);
            }, $response['data']);
        }
        $config['data'] = $response;
    }

    static protected function actionHtml($data, $config)
    {
        $actions = [];
        foreach ($config as $action) {
            $action['action'] = $action['action'] ? self::compile($action['action'], $data) : '';
            $actions[]        = $action;
//            $array['ACTION'] =
        }
        $data['ACTION'] = $actions;
        return $data;
    }
}