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
use Larfree\Models\Admin\AdminNav;

class Make
{
    protected $tableName = '';

    function __construct($tableName)
    {
        $this->tableName = $tableName;
        //先读取数据库 生成配置
//        $this->makeConfig($tableName);
//        if ($option['c']) {
//            $this->makeControoler($tableName);
//        }
//        if ($option['m']) {
//            $this->makeModel($tableName);
//        }
////        $this->makeRepository($model);
//        if ($option['s']) {
//            $this->makeService($tableName);
//        }
////        $this->makeConfig($tableName);
//
//        if ($option['r']) {
//            $this->makeRoute($tableName);
//        }
//        if ($option['a']) {
//            $this->makeAdminMenu($tableName);
//        }
//        $this->makeTest($tableName);
    }

    /**
     * 处理用于获取文件的文件名  下划线转驼峰  点转/
     * @param $file
     * @return string
     */
    protected function formatName($file)
    {
        if (stripos($file, '.')) {
            $file = str_ireplace('.', '/', $file);
        }
        if (stripos($file, '/')) {
            $fullname = ucfirst(lineToHump(dirname($file))) . '/' . ucfirst(lineToHump(basename($file)));
            $name = lineToHump(basename($file));
            $modelName = ucfirst(lineToHump(dirname($file))) . ucfirst(lineToHump(basename($file)));
        } else {
            $fullname = ucfirst(lineToHump(basename($file)));
            $name = lineToHump(basename($file));
            $modelName = ucfirst(lineToHump(basename($file)));
        }
        return [$fullname, $name, $modelName];
    }

    /**
     * 插入后台NAV
     * @author Blues
     *
     */
    function makeAdminMenu()
    {
        $name = $this->tableName;
        list($fullName, $name, $modelName) = $this->formatName($name);
//        $fullName = strtolower($fullName);
        $fullName = strtr($fullName, '/', '.');
        $fullName = humpToLine($fullName);
        $fullName = str_replace('._', '.', $fullName);
        AdminNav::firstOrCreate([
            'name' => $fullName,
            'url' => '/curd/' . $fullName . '/',
        ]);
    }

    /**
     * 生成控制起
     * @param string $type = all
     * @author Blues
     */
    function makeController($type = 'all')
    {
        $name = $this->tableName;
        $fullNames = [];
        $name = lineToHump($name);
        list($fullName, $name) = $this->formatName($name);
        $fullNames = explode('/', $fullName);
        $folder = $fullNames[0];
        $fullName = $fullNames[1];
        $nameSpace = str_ireplace('/', '\\', $fullName);
        $adminApi = <<<MODEL
<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Admin\\{$folder};

use Illuminate\Http\Request;
use Larfree\Controllers\AdminApisController as Controller;
use App\Services\\{$folder}\\{$folder}{$nameSpace}Service;
class {$name}Controller extends Controller
{
    /**
     * @var{$folder}{$nameSpace}Service
     */
    public \$service;
    public function __construct({$folder}{$nameSpace}Service \$service )
    {
        \$this->service = \$service;
        \$this->service->setAdmin();
        parent::__construct();
    }
}
MODEL;
        $api = <<<MODEL
<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Api\\{$folder};
use Illuminate\Http\Request;
use Larfree\Controllers\ApisController as Controller;
use App\Services\\{$folder}\\{$folder}{$nameSpace}Service;
class {$name}Controller extends Controller
{
    /**
     * @var{$folder}{$nameSpace}Service
     */
    public \$service;
    public function __construct({$folder}{$nameSpace}Service \$service)
    {
        \$this->service = \$service;
        parent::__construct();
    }
}
MODEL;
        $apiPath = base_path() . '/app/Http/Controllers/Api/' . $folder . '/' . $fullName . 'Controller.php';
        $adminApiPath = base_path() . '/app/Http/Controllers/Admin/' . $folder . '/' . $fullName . 'Controller.php';

        if ($type == 'home' || $type == 'all') {
            if (file_exists($apiPath)) {
                echo $apiPath . "已经存在.\r\n";
            } else {
                $this->file_force_contents($apiPath, $api);
                echo $apiPath . "生成.\r\n";
            }
        }
        if ($type == 'admin' || $type == 'all') {
            if (file_exists($adminApiPath)) {
                echo $adminApiPath . "已经存在.\r\n";
            } else {
                $this->file_force_contents($adminApiPath, $adminApi);
                echo $adminApiPath . "生成.\r\n";
            }
        }
    }


    /**
     * 生成仓库
     * @param $name
     * @deprecated
     * @author Blues
     */
    function makeRepository()
    {
        $name = $this->tableName;
        $name = lineToHump($name);
        list($fullName, $name, $modelName) = $this->formatName($name);
        $nameSpace = dirname($fullName);
        $nameSpace = str_ireplace('/', '\\', $nameSpace);
        if ($nameSpace)
            $nameSpace = '\\' . $nameSpace;

        $Name = ucfirst($name);
        $Name = lineToHump($Name);

        $tmp = explode('/', $fullName);
        if (@$tmp[1]) {
            $fullName = $tmp[0] . '/' . $tmp[0] . $tmp[1];
        }
        $content = <<<MODEL
<?php
/**
 * 仓库类. 所有数据交互通过此模式
 * @author blues
 */
namespace App\Repositories{$nameSpace};
use Larfree\Repositories\LarfreeRepository;
use App\Models{$nameSpace}\\{$modelName};
class {$modelName}Repository extends LarfreeRepository
{
    /**
     * @var {$modelName}
     */
    protected \$model;

    /**
     * 可以指定为其他的model
     * @return string
     */
    public function model()
    {
        return {$modelName}::Class;
    }
}
MODEL;
        $path = base_path() . '/app/Repositories/' . $fullName . 'Repository.php';
        if (file_exists($path)) {
            echo $path . "已经存在.\r\n";
        } else {
            $this->file_force_contents($path, $content);
            echo $path . "生成.\r\n";
        }
    }

    /**
     * 生成服务
     * @author Blues
     */
    function makeService()
    {
        $name = $this->tableName;
        $name = lineToHump($name);
        list($fullName, $name, $modelName) = $this->formatName($name);
        $nameSpace = dirname($fullName);
        $nameSpace = str_ireplace('/', '\\', $nameSpace);
        if ($nameSpace)
            $nameSpace = '\\' . $nameSpace;

        $Name = ucfirst($name);
        $Name = lineToHump($Name);

        $tmp = explode('/', $fullName);
        if (@$tmp[1]) {
            $fullName = $tmp[0] . '/' . $tmp[0] . $tmp[1];
        }
        $content = <<<MODEL
<?php
/**
 * 仓库类. 所有数据交互通过此模式
 * @author blues
 */
namespace App\Services{$nameSpace};
use Larfree\Services\SimpleLarfreeService;
use App\Models{$nameSpace}\\{$modelName};
class {$modelName}Service extends SimpleLarfreeService
{
    /**
     * @var {$modelName}
     */
    protected \$model;
    public function __construct({$modelName} \$model )
    {
        \$this->model = \$model;
        parent::__construct();
    }
}
MODEL;
        $path = base_path() . '/app/Services/' . $fullName . 'Service.php';
        if (file_exists($path)) {
            echo $path . "已经存在.\r\n";
        } else {
            $this->file_force_contents($path, $content);
            echo $path . "生成.\r\n";
        }
    }

    function makeConfig()
    {
        $table = $this->tableName;
        $this->makeSchemas($table);
        $this->makeComponent($table);
//        $this->makeAPi($table);
    }

    protected function makeSchemas($table)
    {


        list($fullName, $name, $modelName) = $this->formatName($table);

        $tableName = humpToLine($modelName);
        $columns = DB::select("SHOW FULL COLUMNS FROM `{$tableName}`");
        $table = lineToHump($table);
//        $cotnent=<<<CONTENT
//    'detail'=>[
//    ],
//CONTENT;
        $fields = '';
        foreach ($columns as $k => $column) {
            $column = get_object_vars($column);
            $name = $column['Comment'] ? $column['Comment'] : $column['Field'];

            if (!$column['Comment']) {
                switch ($k) {
                    case 'created_at':
                        $name = '创建日期';
                        break;
                    case 'updated_at':
                        $name = '修改日期';
                        break;
                    case 'deleted_at':
                        $name = '删除日期';
                        break;
                }
            }

            $type = $this->fieldType($column['Type']);
            $fields .= "
            '{$column['Field']}' => [
                'name' => '{$name}',
                'tip' => '',
                'type' => '{$type}',
                'sql_type' => '{$column['Type']}',
            ],";
        }

        $content = <<<CONTENT
<?php
return [
    'detail' => [
        {$fields}
    ],
];
CONTENT;

        $path = schemas_path() . '/Schemas/' . $fullName . '.php';
        if (file_exists($path)) {
            echo $path . "已经存在.\r\n";
        } else {
            $this->file_force_contents($path, $content);
            echo $path . "生成.\r\n";
        }
    }

    protected function fieldType($type)
    {
        if (stripos($type, 'int')) {
            return 'number';
        }
        if (stripos($type, 'decimal')) {
            return 'number';
        }
        if (stripos($type, 'text')) {
            return 'textarea';
        }
        if (stripos($type, 'char')) {
            return 'text';
        }
        if ($type == 'datetime') {
            return 'datetime';
        }
        if (stripos($type, 'date')) {
            return 'date';
        }
        if ($type == 'timestamp') {
            return 'timestamp';
        }
        //默认
        return 'text';
    }

    protected function makeComponent($table)
    {


        list($fullName, $name, $modelName) = $this->formatName($table);
        $tableName = humpToLine($modelName);

        $columns = Schema::getColumnListing($tableName);

        //去掉主键和updated_at  created_at
        $actionFields = $this->delByValue($columns, 'id');
        $actionFields = $this->delByValue($actionFields, 'updated_at');
        $actionFields = $this->delByValue($actionFields, 'created_at');
        $actionFields = $this->delByValue($actionFields, 'deleted_at');

        $actionFields = implode("',\r\n                '", $actionFields);
        $actionFields = "'" . $actionFields . "'";

        //所有字段
        $fields = implode("',\r\n                '", $columns);
        $fields = "'" . $fields . "'";


        $content = <<<CONTENT
<?php
/**
 * 其他可以用组建默认的参数
 * 也可以自己指定
 */
return [
    'detail' => [
        'table' => [
            'fields' => [
                {$fields}
            ],
        ],
        'add' => [
            'fields' => [
                {$actionFields}
            ],
        ],
        'edit' => [
            'fields' => [
                {$actionFields}
            ],
        ],
        'detail' => [
            'fields' => [
                {$fields}
            ],
        ],
    ],
];
CONTENT;

        $path = schemas_path() . '/Components/' . $fullName . '.php';
        if (file_exists($path)) {
            echo $path . "已经存在.\r\n";
        } else {
            $this->file_force_contents($path, $content);
            echo $path . "生成.\r\n";
        }
    }

    /**
     * 生成路由
     * @param string $type = all
     * @author Blues
     *
     */
    public function makeRoute($type = 'all')
    {
        $table = $this->tableName;
        list($fullName, $name) = $this->formatName($table);
        $tableName = humpToLine($name);
        $apiPath = humpToLine($fullName);
        $apiPath = str_ireplace('/_', '/', $apiPath);
        $fullName = str_ireplace('/', '\\', $fullName);


        $path = base_path() . '/routes/api.php';
        $adminPath = base_path() . '/routes/apiAdmin.php';
        $route = "\r\nRoute::apiResource('{$apiPath}', 'Api\\{$fullName}Controller');//自动添加-API";
        $adminRoute = "\r\nRoute::apiResource('admin/{$apiPath}', 'Admin\\{$fullName}Controller',['adv'=>true]);//自动添加-ADMIN";


        if ($type == 'home' || $type == 'all') {
            $pathContent = file_get_contents($path);

            echo "Router:";
            if (!stripos($pathContent, $route)) {
                file_put_contents($path, $route, 8);
                echo $route . " 写入成功.\r\n";
            }else{
                echo $route . " 已经存在.\r\n";
            }
        }

        if ($type == 'admin' || $type == 'all') {
            //如果apiAdmin存在. 就写入这个
            if(!file_exists($adminPath)){
                $adminPath = $path;
            }
            $pathContent = file_get_contents($adminPath);
            echo "router:";
            if (!stripos($pathContent, $adminRoute)) {
                file_put_contents($adminPath, $adminRoute, 8);
                echo $adminRoute . " 写入成功.\r\n";
            }else{
                echo $adminRoute . " 已经存在.\r\n";
            }
        }

    }


    /**
     * @author Blues
     *
     */
    public function makeModel()
    {
        $name = $this->tableName;
        $name = lineToHump($name);
        list($fullName, $name, $modelName) = $this->formatName($name);
        $nameSpace = dirname($fullName);
        $nameSpace = str_ireplace('/', '\\', $nameSpace);
        if ($nameSpace)
            $nameSpace = '\\' . $nameSpace;

        $Name = ucfirst($name);
        $Name = lineToHump($Name);

        $tmp = explode('/', $fullName);
        if (@$tmp[1]) {
            $fullName = $tmp[0] . '/' . $tmp[0] . $tmp[1];
        }
        $content = <<<MODEL
<?php
/**
 * 没有任何逻辑的Model类
 * @author blues
 */
namespace App\Models{$nameSpace};
use Larfree\Models\Api;
class {$modelName} extends Api
{
}
MODEL;
        $path = base_path() . '/app/Models/' . $fullName . '.php';
        if (file_exists($path)) {
            echo $path . "已经存在.\r\n";
        } else {
            $this->file_force_contents($path, $content);
            echo $path . "生成.\r\n";
        }
    }


    protected function delByValue($arr, $value)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        foreach ($arr as $k => $v) {
            if ($v == $value) {
                unset($arr[$k]);
            }
        }
        return $arr;
    }


    /**
     * 强制生成
     * @param $dir
     * @param $contents
     * @return false|int
     * @author Blues
     *
     */
    protected static function file_force_contents($dir, $contents)
    {
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';

        foreach ($parts as $part) {
            if (!is_dir($dir .= "{$part}/")) mkdir($dir);
        }

        return file_put_contents("{$dir}{$file}", $contents);
    }
}
