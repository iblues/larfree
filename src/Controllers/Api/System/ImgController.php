<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6
 * Time: 9:59
 */

namespace App\Http\Controllers\Api\System;


use Illuminate\Http\Request;
use Image;
use App\Http\Controllers\Controller;
use App\Models\Record;

class ImgController extends Controller
{
    /**
     * @param $url 图片地址
     * @param $num 裁剪的方式 0.等比例缩放 1.等比例裁剪 5.居中裁剪
     * @width 长度
     * @height 宽度
     * storage_path 获取storage文件的绝对路径 $url,$num,$width,$height
     */
    public function images(Request $request,$date,$img){
        $param  = current(array_keys($request->all()));
        $array = explode('/',$param);
        $width = $array[3];
        $height = $array[5];
        $mode = $array[1];
        $urlImg = storage_path().'/app/public/images/'.$date.'/'.$img;//原文件
        $fileName = MD5($date.$img.$param).'.jpeg';
        $url = 'images/thumb/'.$width.'x'.$height;
        $dir = storage_path()."/app/public/".$url;
        //判断该目录下的文件是否存在
        //如果不存在则创建
        if(!file_exists($dir)){
            mkdir($dir);//创建文件夹
        }
        $last = storage_path().'/app/public/'.$url.'/'.$fileName;//最终的文件
        if(!is_file('/storage/'.$last)){
            $img = Image::make($urlImg);
            switch ($mode){
                case 0:
                case 2:
                case 3:
                case 4:
                    $backGround = $img->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    break;

                case 1:
                case 5:
                    $backGround =$img->resize($width, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $backGround = $backGround->resizeCanvas($width, $height);
                    break;
            }
            //$backGround = Image::make($urlImg)->resize($array[3],$array[5]);//生成缩略图
            $backGround->save($last);//保存
            header('Location: /storage/'.$url.'/'.$fileName);
            exit();
        }else{
            header('Location: /storage/'.$url.'/'.$fileName);
            exit();
        }
    }
}