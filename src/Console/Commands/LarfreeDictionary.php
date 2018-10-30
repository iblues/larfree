<?php

namespace Larfree\Console\Commands;

use App\Models\Admin\AdminNav;
use App\Models\Common\CommonUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Larfree\Libs\Make;
use Larfree\Libs\Schemas;
use Larfree\Models\System\SystemDictionary;

class LarfreeDictionary extends Command
{
    protected $signature = 'larfree:dictionary';

    /**
     * The console command description.
     *
     * @var string
     **/
    protected $description = '初始化字段资源数据库';

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
        $schemas = Schemas::getAllSchemasConfig();
        array_walk($schemas,array($this,'model'));
    }

    public function model($model){
        $modelName  = lcfirst(lineToHump( $model['key'] ) );
        foreach ($model['detail'] as $k=>$field){
            $insert = [];
            $insert['key']=$k;
            $insert['model']=$modelName;
            $name = $field['name'];
            if(@$field['tip'] )
                $name .= ' | '.$field['tip'];
            if(@$field['option'] )
                $name .= ' | '.json_encode($field['option'],JSON_PRETTY_PRINT);

            $insert['value'] = $name;
            SystemDictionary::firstOrCreate($insert);
        }
    }
}
