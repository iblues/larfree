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

    public function provides()
    {
        return [Test::class, 'test'];
    }
}
