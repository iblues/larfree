<?php

namespace Larfree\Console\Commands;

use Illuminate\Console\Command;
use Larfree\Libs\Make;

class LarfreeMake extends Command
{
    /**
     *
     * @var string
     */
    protected $signature = 'larfree:make {table} {mode=false}';

    /**
     * The console command description.
     *
     * @var string
     **/
    protected $description = '扫描数据库表,自动创建配置';

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
        $arguments  = $this->arguments();
        $table      = $arguments['table'];
        $mode       = $arguments['mode'];
        $modeChoice = [
            'cancel',
            'all:(Schemas,Model,Service,Router,Controller,AdminNav)',
            'Schemas',
            'Schemas,Model',
            'Schemas,Model,Service',
            'Schemas,Model,Service,前台Api:Controller,前台路由:Router',
            'Schemas,Model,Service,后台Api:Controller,后台路由:Router,AdminNav',
        ];

        if ($mode == 'false' || !key_exists($mode, $modeChoice)) {
            $choice = $this->choice('选择哪种生成模式?', $modeChoice, 0);
            $mode   = @array_flip($modeChoice)[$choice];
        }

        $make = new Make($table);
        switch ($mode) {
            case 1:
                $make->makeConfig();
                $make->makeController();
                $make->makeModel();
                $make->makeService();
                $make->makeRoute();
                $make->makeAdminMenu();
                break;
            case 2;
                $make->makeConfig();
                break;
            case 3;
                $make->makeConfig();
                $make->makeModel();
                break;
            case 4;
                $make->makeConfig();
                $make->makeModel();
                $make->makeService();
                break;
            case 5;
                $make->makeConfig();
                $make->makeController('home');
                $make->makeModel();
                $make->makeService();
                $make->makeRoute('home');
                break;
            case 6;
                $make->makeConfig();
                $make->makeController('admin');
                $make->makeModel();
                $make->makeService();
                $make->makeRoute('admin');
                $make->makeAdminMenu();
                break;
        }
//        $make = new Make($table, $controller, $model);


    }


    protected function ParmOrYN($parm, $def)
    {
        if ($parm == 'y') {
            $parm = $def;
        } elseif ($parm == 'n') {
            $parm = 'false';
        }
        return $parm;
    }
}
