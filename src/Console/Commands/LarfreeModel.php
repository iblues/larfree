<?php

namespace Larfree\Console\Commands;

use App\Models\Admin\AdminNav;
use App\Models\Common\CommonUser;
use Illuminate\Console\Command;
use Larfree\Libs\RelationGenerator;

class LarfreeModel extends Command
{
    protected $signature = 'larfree:model {modelName=all}';

    /**
     * The console command description.
     *
     * @var string
     **/
    protected $description = '在model文件中生成函数,方便调用';

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
        $parm = $this->arguments();
        if ($parm['modelName'] == 'all') {
            $classes = $this->getAllModel();
            foreach ($classes as $class){
                $generator = new RelationGenerator(new $class());
                $generator->generator(1);
            }
        } else {
            $modelName = explode('.', $parm['modelName']);
            $this->generatorSingle($modelName);
        }
    }

    protected function getAllModel()
    {
        $return = [];
        $models = $this->getDir(app_path('Models'));
        //读取所有的model 并赋值
        $classes = get_declared_classes();
        foreach ($classes as $class) {
            if (stripos($class, 'App\\Models\\') === 0) {
                $return[] = $class;
            }
        }
        return $return;
    }

    protected function getDir($path)
    {
        $classes = [];
        $lists   = scandir($path);
        foreach ($lists as $name) {
            if ($name[0] == '.') {
                continue;
            }

            if (is_dir($path.'/'.$name)) {
                $classes[] = $this->getDir($path.'/'.$name);
            } else {
                $fileName  = $path.'/'.$name;
                $classes[] = $fileName;
                include $fileName;
            }
        }
        return $classes;
    }

    protected function generatorSingle($modelName)
    {
        if (!isset($modelName[1])) {
            throw  new  \Exception('model名错误. 应如test.testDetail = test\testDetail');
        }
        $model = 'App\\Models\\'.ucfirst($modelName[0]).'\\'.ucfirst($modelName[0]).ucfirst($modelName[1]);
        if (!class_exists($model)) {
            throw  new  \Exception($model.'不存在');
        }
        $generator = new RelationGenerator(new $model());
        $generator->generator(1);
    }
}
