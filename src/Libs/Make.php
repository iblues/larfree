<?php
/**
 * 生成控制器 model等
 * User: blues
 * Date: 2017/11/3/003
 * Time: 11:33
 */

namespace Larfree\Libs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin\AdminNav;
class Make
{
    function __construct($tableName,$controller,$model){
        //先读取数据库 生成配置
        $this->makeConfig($tableName);

        if($controller){
            $this->makeControoler($controller);
        }
        if($model){
            $this->makeModel($model);
        }
//        $this->makeConfig($tableName);
        $this->makeRoute($tableName);
        $this->makeAdminMenu($tableName);
    }

    /**
     * 处理用于获取文件的文件名  下划线转驼峰  点转/
     * @param $file
     * @return string
     */
    static protected function fomartName($file){
        if(stripos($file,'.')){
            $file =str_ireplace('.','/',$file);
        }
        if(stripos($file,'/')) {
            $fullname =  ucfirst(lineToHump(dirname($file))) .'/'. ucfirst(lineToHump(basename($file)));
            $name = lineToHump(basename($file));
            $modelName = ucfirst(lineToHump(dirname($file))) . ucfirst(lineToHump(basename($file)));
        }else{
            $fullname = ucfirst(lineToHump(basename($file)));
            $name = lineToHump(basename($file));
            $modelName = ucfirst(lineToHump(basename($file)));
        }
        return [$fullname,$name,$modelName];
    }

    function makeAdminMenu($name){
        list($fullName,$name,$modelName) = $this->fomartName($name);
//        $fullName = strtolower($fullName);
        $fullName = strtr($fullName,'/','.');
        $fullName = humpToLine($fullName);
        $fullName = str_replace('._','.',$fullName);
        AdminNav::firstOrCreate([
            'name'=>$fullName,
            'url'=>'/curd/'.$fullName.'/',
        ]);
    }

    function makeControoler($name){
        $fullNames = [];
        $name = lineToHump($name);
        list($fullName,$name) = $this->fomartName($name);
        $fullNames = explode('/',$fullName);
        $folder = $fullNames[0];
        $fullName = $fullNames[1];
        $nameSpace = str_ireplace('/','\\',$fullName);
        $adminApi=<<<MODEL
<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Admin\{$folder};

use Illuminate\Http\Request;
use Larfree\Controllers\AdminApisController as Controller;
use App\Models\\{$folder}\\{$folder}{$nameSpace};
class {$name}Controller extends Controller
{
    public function __construct({$folder}{$nameSpace} \$model )
    {
        \$this->model = \$model;
        parent::__construct();
    }
}
MODEL;
        $api=<<<MODEL
<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Api\\{$folder};
use Illuminate\Http\Request;
use Larfree\Controllers\ApisController as Controller;
use App\Models\\{$folder}\\{$folder}{$nameSpace};
class {$name}Controller extends Controller
{
    public function __construct({$folder}{$nameSpace} \$model)
    {
        \$this->model = \$model;
        parent::__construct();
    }
}
MODEL;
        $apiPath= base_path().'/app/Http/Controllers/Api/'.$folder.'/'.$fullName.'Controller.php';
        $adminApiPath = base_path().'/app/Http/Controllers/Admin/Api/'.$folder.'/'.$fullName.'Controller.php';
        if(file_exists($apiPath)) {
            echo $apiPath."已经存在.\r\n";
        }else{
            $this->file_force_contents($apiPath, $api);
            echo $apiPath."生成.\r\n";
        }
        if(file_exists($adminApiPath)) {
            echo $adminApiPath."已经存在.\r\n";
        }else{
            $this->file_force_contents($adminApiPath, $adminApi);
            echo $adminApiPath."生成.\r\n";
        }
    }

    function makeModel($name){

        $name = lineToHump($name);
        list($fullName,$name,$modelName) = $this->fomartName($name);
        $nameSpace = dirname($fullName);
        $nameSpace = str_ireplace('/','\\',$nameSpace);
        if($nameSpace)
            $nameSpace='\\'.$nameSpace;

        $Name = ucfirst($name);
        $Name = lineToHump($Name);

        $tmp = explode('/',$fullName);
        if(@$tmp[1]){
            $fullName=$tmp[0].'/'.$tmp[0].$tmp[1];
        }
        $content =<<<MODEL
<?php
/**
 * 没有任何逻辑的Model类
 * @author blues
 */
namespace App\Models{$nameSpace};
use Larfree\Models\Api;
use App\Scopes{$nameSpace}\\{$modelName}Scope;
class {$modelName} extends Api
{
    use {$modelName}Scope;
}
MODEL;
        $path= base_path().'/app/Models/'.$fullName.'.php';
        if(file_exists($path)) {
            echo $path."已经存在.\r\n";
        }else{
            $this->file_force_contents($path, $content);
            echo $path."生成.\r\n";
        }



        $content =<<<MODEL
<?php
/**
 * 没有任何逻辑的Model类
 * @author blues
 */
namespace App\Scopes{$nameSpace};
trait {$modelName}Scope
{

}
MODEL;
        $path= base_path().'/app/Scopes/'.$fullName.'.php';
        if(file_exists($path)) {
            echo $path."已经存在.\r\n";
        }else{
            $this->file_force_contents($path, $content);
            echo $path."生成.\r\n";
        }
    }

    function makeConfig($table){
        $this->makeSchemas($table);
        $this->makeComponent($table);
//        $this->makeAPi($table);
    }
    protected function makeSchemas($table){


        list($fullName,$name,$modelName) = $this->fomartName($table);

        $tableName = humpToLine($modelName);
        $columns = DB::select("SHOW FULL COLUMNS FROM `{$tableName}`");
        $table = lineToHump($table);
//        $cotnent=<<<CONTENT
//    'detail'=>[
//    ],
//CONTENT;
        $fields='';
        foreach ($columns as $k=>$column){
            $column = get_object_vars($column);
            $name = $column['Comment']?$column['Comment']:$column['Field'];
            $type = $this->fieldType($column['Type']);
            $fields.="
            '{$column['Field']}'=>[
                'name'=>'{$name}',
                'tip'=>'',
                'type'=>'{$type}',
            ],";
        }

        $content=<<<CONTENT
<?php
return [
    'detail'=>[
        {$fields}
    ],
];
CONTENT;

        $path= base_path().'/config/Schemas/Schemas/'.$fullName.'.php';
        if(file_exists($path)) {
            echo $path."已经存在.\r\n";
        }else{
            $this->file_force_contents($path, $content);
            echo $path."生成.\r\n";
        }
    }

    protected function fieldType($type){
        if(stripos($type,'int')){
            return 'number';
        }
        if(stripos($type,'decimal')){
            return 'number';
        }
        if(stripos($type,'text')){
            return 'textarea';
        }
        if(stripos($type,'char')){
            return 'text';
        }
        if($type=='datetime'){
            return 'datetime';
        }
        if(stripos($type,'date')){
            return 'date';
        }
        if($type=='timestamp'){
            return 'timestamp';
        }
        //默认
        return 'text';
    }
    protected function makeComponent($table){


        list($fullName,$name,$modelName) = $this->fomartName($table);
        $tableName = humpToLine($modelName);

        $columns = Schema::getColumnListing($tableName);

        //去掉主键和updated_at  created_at
        $actionFields = $this->delByValue($columns,'id');
        $actionFields = $this->delByValue($actionFields,'updated_at');
        $actionFields = $this->delByValue($actionFields,'created_at');

        $actionFields  =  implode("',\r\n            '",$actionFields);
        $actionFields  = "'".$actionFields."'";

        //所有字段
        $fields  =  implode("',\r\n            '",$columns);
        $fields  = "'".$fields."'";


        $content =<<<CONTENT
<?php
/**
 * 其他可以用组建默认的参数
 * 也可以自己指定
 */
return [
    'detail'=>[
        'table'=>[
            'fields'=>[
                {$fields}
             ],
        ],
        'add'=>[
            'fields'=>[
                {$actionFields}
             ],
        ],
        'edit'=>[
            'fields'=>[
                {$actionFields}
            ],
        ],
        'detail'=>[
            'fields'=>[
                {$fields}
            ],
        ],
    ],
];
CONTENT;

        $path= base_path().'/config/Schemas/Components/'.$fullName.'.php';
        if(file_exists($path)) {
            echo $path."已经存在.\r\n";
        }else{
            $this->file_force_contents($path, $content);
            echo $path."生成.\r\n";
        }
    }

    protected function makeRoute($table){
        list($fullName,$name) = $this->fomartName($table);
        $tableName = humpToLine($name);
        $apiPath =  humpToLine($fullName);
        $apiPath = str_ireplace('/_','/',$apiPath);
        $fullName = str_ireplace('/','\\',$fullName);


        $path= base_path().'/routes/api.php';
        $adminPath= base_path().'/routes/api.php';
        $route = "\r\nRoute::resource('{$apiPath}', 'Api\\{$fullName}Controller');//自动添加-API";
        $adminRoute = "\r\nRoute::resource('admin/{$apiPath}', 'Admin\\{$fullName}Controller');//自动添加-ADMIN";


        $pathContent = file_get_contents($path);
        if(!stripos($pathContent,$route)){
            file_put_contents($path, $route,8);
        }

        $pathContent = file_get_contents($adminPath);
        if(!stripos($pathContent,$adminRoute)){
            file_put_contents($adminPath, $adminRoute,8);
        }

    }

    protected function delByValue($arr, $value){
        if(!is_array($arr)){
            return $arr;
        }
        foreach($arr as $k=>$v){
            if($v == $value){
                unset($arr[$k]);
            }
        }
        return $arr;
    }


    protected static function file_force_contents($dir, $contents){
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';

        foreach($parts as $part) {
            if (! is_dir($dir .= "{$part}/")) mkdir($dir);
        }

        return file_put_contents("{$dir}{$file}", $contents);
    }
}