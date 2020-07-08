<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/25
 * Time: 21:41
 */

namespace Larfree\Components\Field;
use Illuminate\Support\Arr;
use Larfree\Components\Components;

class Image extends Components
{
    /**
     * 生成缩略图
     * @param $config
     * @param $array
     */
    static public function getAttribute($config,&$array){

        if(@$config['multi']){
            if(!is_array($array[$config['key']])){
                $array[$config['key']] = json_decode($array[$config['key']],1);
            }
        }
        $value  = $array[$config['key']];
        if(is_array($array)) {
            //上面的慢慢淘汰
            $array[$config['key'].'_link']=[
                'small'=> getArrayThumb($value, '200', '200'),
                'origin'=>getArrayThumb($value,'','0',-1),
                'large'=>getArrayThumb($value,'1500','4000'),
            ];
        }else{
            //上面的慢慢淘汰
            $array[$config['key'].'_link']=[
                'small'=> getThumb($value, '200', '200'),
                'origin'=>getThumb($value,'0','0',-1),
                'large'=>getThumb($value,'1500','4000'),
            ];

        }
    }
    static public function config($config){
        if( Arr::get($config,'multi',false))
            $config['cast']='array';
        return $config;
    }
}
