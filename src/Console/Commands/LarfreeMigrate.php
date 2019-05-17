<?php

namespace Larfree\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class LarfreeMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larfree:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '迁移文件夹中的结构';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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

        $dir = scandir(base_path('database/migrations/'));
        echo "Which folder will you want to do?\r\n";
        foreach($dir as $key => $path){
            if($key == 0){
                echo $key," : Cancel\r\n";
            }elseif($key == 1){
                echo $key," : All\r\n";
            }else{
                echo $key,' : ',$path."\r\n";
            }

        }
        $number = $this->ask('Please enter a number');

        //遍历指定文件夹
        if($number>1){
            echo $path = '/database/migrations/'.$dir[$number],"\r\n";
            $this->call('migrate', [
                '--path' =>$path,
            ]);
        }

//        Cache::
        //清理缓存的表
        Cache::tags(['table_column'])->flush();

        //遍历全部
        if($number==1){
            foreach($dir as $key => $path){
                if($key>1){
                    echo $path = '/database/migrations/'.$dir[$key],"\r\n";
                    $this->call('migrate', [
                        '--path' =>$path,
                    ]);
                }
            }
        }


    }
}
