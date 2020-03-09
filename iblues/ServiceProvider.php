<?php
/**
 * Created by PhpStorm.
 * User: lanyang
 * Date: 2018/9/14
 * Time: 下午5:20
 */

namespace Iblues\Larfree;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Route;
use Larfree\Console\Commands\AddressMake;
use Larfree\Console\Commands\LarfreeDictionary;
use Larfree\Console\Commands\LarfreeInstall;
use Larfree\Console\Commands\LarfreeMake;
use Larfree\Console\Commands\LarfreeMigrate;
use Illuminate\Support\Facades\Response;
use Larfree\Resources\ApiResource;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
//    protected $defer = true;

    public function register()
    {
//        $this->app->singleton(Test::class, function () {
//            return new Test(123);
//        });

//        $this->app->alias(ApisController::class, 'ApiController');

//        $this->app->alias(AdminApisController::class, 'AdminApiController');
    }

    public function boot()
    {

        // 解决sync会删除其他的问题
        BelongsToMany::macro('advSync',function ($ids){
            $return = $this->select('id')->get();
            $this->detach($return->pluck('id')->toArray());
            return $this->syncWithoutDetaching($ids);
        });

        // 扩展apiResource
        Route::macro('apiResource',function ($prefix, $controller,$option=[]){
            if(isset($option['larfree']) && $option['larfree']) {
                Route::post($prefix . '/import{module?}', $controller . '@import')->name($prefix . 'import');
                Route::get($prefix . '/export{module?}', $controller . '@export')->name($prefix . 'export');
                Route::get($prefix . '/chart/{module?}', $controller . '@chart')->name($prefix . 'chart');
            }
            Route::apiResource($prefix, $controller);
        });

        $path = dirname(__DIR__).'/src';
//        $this->publishes([
//            __DIR__.'/path/to/config/courier.php' => config_path('courier.php'),
//        ]);
        $this->publishes([
            $path.'/Copy/app/Scopes' => app_path('Scopes/'),
        ],'larfree');
        $this->publishes([
            $path.'/Copy/app/Models' => app_path('Models/'),
        ],'larfree');
        $this->publishes([
            $path.'/Copy/app/Http' => app_path('http/'),
        ],'larfree');
        $this->publishes([
            $path.'/Copy/app/Event' => app_path('Event/'),
        ],'larfree_event');
        $this->publishes([
            $path.'/Copy/app/Listeners' => app_path('Listeners/'),
        ],'larfree_event');
        $this->publishes([
            $path.'/Copy/Schemas' => schemas_path(),
        ],'larfree');
        $this->publishes([
            $path.'/Copy/routes/' => dirname(app_path('')).'/routes/',
        ],'larfree');
        $this->publishes([
            $path.'/Copy/tests/' => dirname(app_path('')).'/tests/',
        ],'larfree');

        $this->loadRoutesFrom($path . '/Routes/api.php');

        //数据库
        $this->loadMigrationsFrom(dirname(__DIR__).'/src/Database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                AddressMake::class,
                LarfreeMake::class,
                LarfreeInstall::class,
                LarfreeMigrate::class,
                LarfreeDictionary::class,
            ]);
        }


        Response::macro('error', function ($value = [],$status = '400') {
            return Response::make($value,$status);
        });


//        return Response()->success(123,'成功',['sda'=>123]);
        Response::macro('success', function ($value = [],string $msg = '成功',array $ext=[]) {
            $ext['msg']=$msg;
            if(!is_object($value)) {
                $value = collect($value);
            }
            return (new ApiResource($value))->additional($ext);
        });

    }

//    public function provides()
//    {
//        return [Larfree::class, 'test'];
//    }
}
