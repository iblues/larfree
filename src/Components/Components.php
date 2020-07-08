<?php

namespace Larfree\Components;

use Curl;

class  Components
{
    static public function component(&$path, &$config, $model)
    {
    }

    /**
     * 替换变量
     * @param $template
     * @param  string  $param
     * @return mixed
     */
    static public function compile($template, $param = '')
    {
        // if逻辑
        $template = preg_replace_callback('/(\{\$([^,:\}]+)\})/i', function ($data) use ($param) {
            return @$param[$data[2]];
        }, $template);
        return $template;
    }


    /**
     * 请求远程接口,如果没有http 就自动加上本域名
     * @param $api
     * @return mixed
     */
    static public function getJSON($api)
    {
        if (substr($api, 0, 4) != 'http') {
            $api = route('home').$api;
        }
        return $response = Curl::to($api)->asJson(1)->get();
    }
}