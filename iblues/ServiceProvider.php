<?php
/**
 * Created by PhpStorm.
 * User: lanyang
 * Date: 2018/9/14
 * Time: 下午5:20
 */

namespace Iblues\Larfree;


use Larfree\Console\Commands\AddressMake;
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

//        $this->app->alias(Test::class, 'test');
    }

    public function boot()
    {

        $path = dirname(__DIR__).'/src';
//        $this->publishes([
//            __DIR__.'/path/to/config/courier.php' => config_path('courier.php'),
//        ]);
        $this->publishes([
            $path.'/Copy/app/Models' => app_path('Models/'),
        ],'larfree');
        $this->publishes([
            $path.'/Copy/app/Http' => app_path('http/'),
        ],'larfree');
        $this->publishes([
            $path.'/Copy/config/Schemas' => config_path('Schemas'),
        ],'larfree');
        $this->publishes([
            $path.'/Copy/routes/' => dirname(app_path('')).'/routes/',
        ],'larfree');

        $this->loadRoutesFrom($path . '/routes/api.php');

        //数据库
        $this->loadMigrationsFrom(dirname(__DIR__).'/src/Database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                AddressMake::class,
                LarfreeMake::class,
                LarfreeInstall::class,
                LarfreeMigrate::class,
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
