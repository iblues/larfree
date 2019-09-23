<?php

namespace Larfree\Console\Commands;

use App\Models\Admin\AdminNav;
use App\Models\Common\CommonUser;
use Illuminate\Console\Command;
use Larfree\Libs\Make;

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
        $replace = $arguments['replace'];
        $this->createAdmin();
        $this->createNav();
    }

    private function createNav(){
        $nav=[
            'name'=>'测试',
            'url'=>'/curd/test.test/',
            'class'=>'',
            'module'=>'',
            'status'=>1,
        ];
        CommonUser::insert($nav);
        $nav=[
            'name'=>'用户管理',
            'url'=>'/curd/common.user/',
            'class'=>'',
            'module'=>'',
            'status'=>1,
        ];
        CommonUser::insert($nav);
    }

    private function createAdmin(){
        CommonUser::insert(['name'=>'admin',
            'phone'=>'18008010521',
            'email'=>'i@iblues.name',
            'api_token'=>str_random(30),
            'password'=>'123',
        ]);
    }

}
