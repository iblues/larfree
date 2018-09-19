<?php
/**
 * Created by PhpStorm.
 * User: lanyang
 * Date: 2018/9/14
 * Time: 下午5:20
 */

namespace Iblues\Test;

use Iblues\Test\Model\Test;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Test::class, function () {
            return new Test(123);
        });

        $this->app->alias(Test::class, 'test');
    }

    public function boot()
    {

//        $this->publishes([
//            __DIR__.'/path/to/config/courier.php' => config_path('courier.php'),
//        ]);
//        $this->publishes([
//            __DIR__.'/path/to/config/' => config_path('courier'),
//        ]);
//        $this->loadRoutesFrom(__DIR__ . '/routes.php');
//        $this->loadMigrationsFrom(__DIR__.'/path/to/migrations');
//        if ($this->app->runningInConsole()) {
//            $this->commands([
//                FooCommand::class,
//                BarCommand::class,
//            ]);
//        }
    }

    public function provides()
    {
        return [Test::class, 'test'];
    }
}
