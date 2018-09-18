<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Larfree\Libs\Make;

class larfreeMake extends Command
{
    protected $signature = 'larfree:make {table} {controller=y} {model=y}';

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
        $arguments = $this->arguments();
        $table = $arguments['table'];
        $controller = $arguments['controller'];
        $model = $arguments['model'];
        $controller = $this->ParmOrYN($controller, $table);
        $model = $this->ParmOrYN($model, $table);
        $make = new Make($table, $controller, $model);


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
