<?php

namespace Larfree\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Larfree\Controllers\ApisController;

//use Auth;

class UploadBaseController extends ApisController
{
    /**
     * @author Blues
     * @param Request $request
     * @return array
     * @throws \Larfree\Exceptions\ApiException
     */
    public function images(Request $request)
    {
        $type = config('filesystems.file_type', 'local');
        switch ($type) {
            case 'qiniu':
                $disk = \Storage::disk('qiniu'); //使用七牛云上传
                $time = 'images/' . date('Y-m-d');//上传目录
                $filename = $disk->put($time, $request->file('file'));//上传
                break;
            case 'oss':
                $disk = \Storage::disk('oss'); //使用oss
                $time = 'file/' . date('Y-m-d');//上传目录
                $filename = $disk->put($time, $request->file('file'));//上传
                break;
            default :
                $disk = \Storage::disk('public');
                $time = 'images/' . date('Y-m-d');
                $filename = $disk->putFile($time, $request->file('file'), 'public');
                break;
        }
        if (!$filename) {
            apiError('上传失败', 500);
        }
        $small_url = getThumb($filename, '200', '200', 0);
        $big_url = getThumb($filename, '1000', '1000', 0);
        $origin_url = $disk->url($filename); //原图
        return ['small_url' => $small_url, 'name' => $filename, 'big_url' => $big_url, 'origin_url' => $origin_url];
    }

    /**
     * @author Blues
     * @param Request $request
     * @return array
     * @throws \Larfree\Exceptions\ApiException
     */
    public function files(Request $request)
    {
        $type = config('filesystems.default', 'file');
        switch ($type) {
            case 'qiniu':
                $disk = \Storage::disk('qiniu'); //使用七牛云上传
                $time = 'file/' . date('Y-m-d');//上传目录
                $filename = $disk->put($time, $request->file('file'));//上传    case 'qiniu':
                break;
            case 'oss':
                $disk = \Storage::disk('oss'); //使用oss
                $time = 'file/' . date('Y-m-d');//上传目录
                $filename = $disk->put($time, $request->file('file'));//上传
                break;
            default :
                $disk = \Storage::disk('public');
                $time = 'file/' . date('Y-m-d');
                $filename = $disk->putFile($time, $request->file('file'), 'public');
                break;
        }
        if (!$filename) {
            apiError('上传失败', 500);
        }
        $url = $disk->url($filename); //原图
        return ['url' => $url, 'name' => $filename];
    }
}
