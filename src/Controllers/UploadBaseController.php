<?php

namespace Larfree\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//use Auth;

class UploadBaseController extends ApisController
{
    /**
     * @param  Request  $request
     * @return array
     * @throws \Larfree\Exceptions\ApiException
     * @author Blues
     */
    public function images(Request $request)
    {
        $type   = config('filesystems.file_type', 'local');
        $module = $request->get('module', 'images');
        switch ($type) {
            case 'qiniu':
                $disk     = \Storage::disk('qiniu'); //使用七牛云上传
                $time     = $module.'/'.date('Y-m-d');//上传目录
                $filename = $disk->put($time, $request->file('file'));//上传
                break;
            case 'oss':
                $disk     = \Storage::disk('oss'); //使用oss
                $time     = $module.'/'.date('Y-m-d');//上传目录
                $filename = $disk->put($time, $request->file('file'));//上传
                break;
            default :
                $disk     = \Storage::disk('public');
                $time     = $module.'/'.date('Y-m-d');
                $filename = $disk->putFile($time, $request->file('file'), 'public');
                break;
        }
        if (!$filename) {
            apiError('上传失败', 500);
        }
        $small_url  = getThumb($filename, '200', '200', 0);
        $big_url    = getThumb($filename, '1000', '1000', 0);
        $origin_url = $disk->url($filename); //原图
        return ['small_url' => $small_url, 'name' => $filename, 'big_url' => $big_url, 'origin_url' => $origin_url];
    }

    /**
     * @param  Request  $request
     * @return array
     * @throws \Larfree\Exceptions\ApiException
     * @author Blues
     */
    public function files(Request $request)
    {
        $type   = config('filesystems.file_type', 'local');
        $module = $request->get('module', 'files');
        switch ($type) {
            case 'qiniu':
                $disk     = \Storage::disk('qiniu'); //使用七牛云上传
                $time     = $module.'/'.date('Y-m-d');//上传目录
                $file     = $request->file('file');
                $name     = time().'.'.$file->getClientOriginalExtension();
                $filename = $disk->putFileAs($time, $file, $name);//上传
                break;
            case 'oss':
                $disk     = \Storage::disk('oss'); //使用oss
                $time     = $module.'/'.date('Y-m-d');//上传目录
                $file     = $request->file('file');
                $name     = time().'.'.$file->getClientOriginalExtension();
                $filename = $disk->putFileAs($time, $file, $name);//上传
                break;
            default :
                $disk     = \Storage::disk('public');
                $time     = $module.'/'.date('Y-m-d');//上传目录
                $file     = $request->file('file');
                $name     = time().'.'.$file->getClientOriginalExtension();
                $filename = $disk->putFileAs($time, $file, $name);//上传
                break;
        }
        if (!$filename) {
            apiError('上传失败', 500);
        }
        $url = $disk->url($filename); //原图
        return ['url' => $url, 'name' => $filename];
    }
}
