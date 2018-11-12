<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/25
 * Time: 21:41
 */

namespace Larfree\Components\Field;
use Larfree\Components\Components;

class Timestamp extends Components
{
    /**
     * 生成缩略图
     * @param $config
     * @param $array
     */
    static public function getAttribute($config,&$array){
        $value  = $array[$config['key']];
        $time = strtotime($value);
        $array[$config['key'] . '_timestamp'] = $time ? $time: 0;
    }
}