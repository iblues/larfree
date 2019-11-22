<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/25
 * Time: 21:41
 */

namespace Larfree\Components\Field;
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
//            $array[$config['key'] . '_small'] = getArrayThumb($value, '200', '200');
//            $array[$config['key'] . '_big'] = getArrayThumb($value, '1000', '1000');
//            $array[$config['key'].'_origin'] = getArrayThumb($value,'0','0',-1);
            //上面的慢慢淘汰
            $array[$config['key'].'_link']=[
                'small'=> getArrayThumb($value, '200', '200'),
                'origin'=>getArrayThumb($value,'','0',-1),
                'large'=>getArrayThumb($value,'1500','1500'),
            ];
        }else{
//            $array[$config['key'] . '_small'] = getThumb($value, '200', '200');
//            $array[$config['key'].'_big'] = getThumb($value,'1000','1000');
//            $array[$config['key'].'_origin'] = getThumb($value,'0','0',-1);
            //上面的慢慢淘汰
            $array[$config['key'].'_link']=[
                'small'=> getThumb($value, '200', '200'),
                'origin'=>getThumb($value,'0','0',-1),
                'large'=>getThumb($value,'1500','1500'),
            ];

        }
    }
    static public function config($config){
        if(array_get($config,'multi',false))
            $config['cast']='array';
        return $config;
    }
}
