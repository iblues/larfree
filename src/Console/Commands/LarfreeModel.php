<?php

namespace Larfree\Console\Commands;

use App\Models\Admin\AdminNav;
use App\Models\Common\CommonUser;
use Illuminate\Console\Command;
use Larfree\Libs\RelationGenerator;
use Larfree\Libs\Schemas;
use Larfree\Models\System\SystemDictionary;

class LarfreeModel extends Command
{
    protected $signature = 'larfree:model {modelName}';

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
        $modelName = explode('.',$parm['modelName']);

        $model = 'App\\Models\\'.ucfirst($modelName[0]).'\\'.ucfirst($modelName[0]).ucfirst($modelName[1]);
        $generator = new RelationGenerator( new $model() );
        $generator->generator();

    }
}
