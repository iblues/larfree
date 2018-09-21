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
            $path.'/Copy/app/Http/Controllers' => app_path('http/Controllers'),
        ]);
        $this->publishes([
            $path.'/Copy/config/Schemas' => config_path('Schemas'),
        ]);
//        $this->loadRoutesFrom(__DIR__ . '/routes.php');

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
    }

//    public function provides()
//    {
//        return [Larfree::class, 'test'];
//    }
}
