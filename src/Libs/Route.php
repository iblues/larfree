<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/5
 * Time: 21:15
 */
namespace Larfree\Libs;
class Route
{
    static function resource(){
        Route::resource('article', 'ArticleController');
    }
}