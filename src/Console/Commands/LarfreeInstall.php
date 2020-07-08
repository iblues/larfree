<?php

namespace Larfree\Console\Commands;

use App\Models\Common\CommonUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Larfree\Models\Admin\AdminNav;
use LarfreePermission\Models\User\UserAdmin;

class LarfreeInstall extends Command
{
    protected $signature = 'larfree:install {replace=y}';

    /**
     * The console command description.
     *
     * @var string
     **/
    protected $description = '初始化数据库和其他配置';

    /**
     * Create a new command instance.
     *
     * @return void
     **/
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();
        $replace   = $arguments['replace'];
        $this->createAdmin();
        $this->createNav();

        //广播事件. 方便插件
        Event::dispatch('larfree.install');
        echo 'success!';
    }

    private function createNav()
    {
        $nav = [
            'name' => '测试',
            'url' => '/curd/test.test/',
            'class' => '',
            'module' => '',
            'status' => 1,
        ];
        AdminNav::firstOrCreate(['url' => $nav['url']], $nav);
        $nav = [
            'name' => '用户管理',
            'url' => '/curd/common.user/',
            'class' => '',
            'module' => '',
            'status' => 1,
        ];
        AdminNav::firstOrCreate(['url' => $nav['url']], $nav);
    }

    private function createAdmin()
    {
        $user = (new CommonUser)->firstOrCreate(
            ['email' => 'i@Iblues.name'],
            [
                'name' => 'admin',
                'phone' => '13888888888',
                'email' => 'i@Iblues.name',
                'password' => '123',
            ]);


        Event::dispatch('larfree.install.admin', ['user' => $user]);
    }

}
