<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//
//这个不能有验证
Route::any('common/pay/notify/{type}', 'Api\Common\PayController@notify');


//系统预定义的组建
Route::post('/common/session',$path.'Common\\AdminController@register')->name('register');

//登录,退出等操作
Route::resource('/common/session',$path.'Common\\SeesionController');
//上传相关
Route::any('/upload/images',$path.'Common\\UploadController@images')->name('upload.images');
Route::any('/upload/files',$path.'Common\\UploadController@files')->name('upload.files');

//配置接口
Route::resource('config', $path.'System\ConfigController');
//component获取
Route::any('/system/component/{key}/{action}', $path.'System\ComponentController@module');
//系统预定义的组建 end




//图片压缩
Route::get('images/{date}/{img}','System\\Api\\ImgController@images');

Route::prefix('manager')->name('admin.api.')->group(function () {
    $_ENV['ADMIN']=true;
    $path = 'Admin\\Api\\';

    //声明首页
    Route::redirect('/', '/manager/', 302)->name('root');

    /**
     * 系统组件end
     */

    //其他路由
    include 'apiAdminResource.php';

});