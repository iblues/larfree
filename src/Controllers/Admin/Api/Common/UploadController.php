<?php

namespace Larfree\Controllers\Admin\Api\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Auth;

class UploadController extends Controller
{
    public function images(Request $request){
        $type = env('UPLOAD_TYPE');
        switch ($type){
            case 'file':
                $disk = \Storage::disk('public');
                $time ='images/'.date('Y-m-d');
                $filename =  $disk->putFile($time, $request->file('file'),'public');
                break;
            default:
                $disk = \Storage::disk('qiniu'); //使用七牛云上传
                $time = 'images/'.date('Y-m-d');//上传目录
                $filename = $disk->put($time, $request->file('file'));//上传
        }
        if(!$filename) {
            return response('上传失败',500);
        }
        $small_url  = getThumb($filename,'200','200',0);
        $big_url  = getThumb($filename,'1000','1000',0);
//        $img_url = $disk->url($filename); //原图
        return response(['small_url'=>$small_url,'name'=>$filename,'big_url'=>$big_url],200);
    }

    public function files(Request $request){
        $disk = \Storage::disk('qiniu'); //使用七牛云上传
        $time = 'images/'.date('Y-m-d');//上传目录
        $filename = $disk->put($time, $request->file('file'));//上传
        if(!$filename) {
            return response('上传失败',500);
        }
        $url = $disk->url($filename,'imageView2/0/w/200/h/200');//裁剪
//        $big_url = $disk->imagePreviewUrl($filename,'imageView2/0/w/800/h/800');
//        $img_url = $disk->url($filename); //原图
        return response(['url'=>$url,'name'=>$filename],200);
    }
}