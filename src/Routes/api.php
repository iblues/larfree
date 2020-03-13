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
//微信验证.
Route::group(['middleware' => ['web', 'wechat.oauth:snsapi_userinfo']], function () {
    Route::any('/wechat/login', 'Larfree\\Controllers\\WeChatController@weChatLogin');
});

Route::group(['prefix' => 'swagger'], function () {
    Route::get('json', 'Larfree\\Controllers\\SwaggerController@getJSON');
    Route::get('my-data', 'Larfree\\Controllers\\SwaggerController@getMyData');
});


Route::group(['middleware' => ['api.auth', 'api'], 'prefix' => 'api'], function () {

    //图片压缩
//    Route::get('images/{date}/{img}', 'System\\Api\\ImgController@images');

    Route::prefix('admin')->name('admin.api.')->group(function () {
//            $_ENV['ADMIN']=true;
        $path = 'Larfree\\Controllers\\Admin\\Api\\';

        //声明首页
        Route::redirect('/', '/admin/', 302)->name('root');


        //上传相关
        Route::post('/upload/images', $path . 'Common\\UploadController@images')->name('upload.images');
        Route::post('/upload/files', $path . 'Common\\UploadController@files')->name('upload.files');

        //配置接口
//        Route::resource('config', $path . 'System\ConfigController');
        //component获取
        Route::get('/system/component/{key}/{action}', $path . 'System\ComponentController@module');

        //后台菜单导航
        Route::get('admin/nav/tree', $path . 'Admin\NavController@tree');//树桩导航
        Route::apiResource('admin/nav', $path . 'Admin\NavController', ['adv' => true]);//导航管理
        Route::apiResource('system/config', $path . 'System\ConfigController', ['adv' => true]);//导航管理

        //系统预定义的组建 end

    });
});

