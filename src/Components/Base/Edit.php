<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14/014
 * Time: 17:50
 */
namespace Larfree\Components\Base;
use Larfree\Components\Components;

class Edit extends Components
{

    static public function component(&$path,&$config,$model){
        parent::component($path,$config,$model);
        //如果变量里面有 就用变量里面的
        if(@$config['param']['readApi'])
            $config['config']['config']['readApi'] = $config['param']['readApi'];
        if(@$config['param']['api'])
            $config['config']['config']['api'] = $config['param']['api'];
        $readApi = $config['config']['config']['readApi'];
        $readApi = self::compile($readApi,$config['param']);

        $response = self::getJSON($readApi);
        $config['config']['config']['api'] = self::compile($config['config']['config']['api'],$config['param']);
        $config['data']=$response;
    }
}