<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/22/022
 * Time: 10:31
 */

namespace Larfree\Libs;

use App\Models\ApiDoc;
use Route;

class Swagger
{
    public function getRoutes($type)
    {
        $routes    = Route::getRoutes();
        $apiRoutes = [];
        foreach ($routes as $route) {
            if (substr($route->uri, 0, 4) == 'api/') {
                //只要前端api
                if ($type == 'home' && @substr($route->uri, 0, 11) != 'api/manager') {
                    $apiRoutes[] = $route;
                } else {
//                    $apiRoutes[] = $route;
                }
            }
        }

        $apiRoutes = $this->PareseRoutes($apiRoutes);
    }

    public function PareseRoutes($apiRoutes)
    {
        $data = [];
        foreach ($apiRoutes as $route) {
            $url    = $route->uri;
            $method = $route->methods[0];
            $as     = @$route->action['controller'];
            $data[] = ['as' => $as, 'method' => $method, 'url' => $url];
        }
        print_r($data);
    }

    public function getAllJson($type = "home")
    {
        $routes = $this->getRoutes($type);
        if ($type == 'home') {
        }
    }

    public function saveReturn()
    {
    }

    public function parseReturn()
    {
    }


    public function getDoc($return)
    {
        $data = $this->resource;
        return $this->pareseDoc($data);
    }

    public function pareseDoc($data, $model = '')
    {
        // 你可以将API的`Swagger Annotation`写在实现API的代码旁，从而方便维护，
        // `swagger-php`会扫描你定义的目录，自动合并所有定义。这里我们直接用`Controller/`
        //$swagger = \OpenApi\scan(app_path('Http/Controllers/Admin'));
        //$doc = json_decode(json_encode($swagger),true);

        //$doc['paths']['/admin/nav/{id}']['put']['responses'][200]['description']='test';

        $type = gettype($data);
        $tmp  = [
            'type' => $type,
            'properties' => [],
        ];
        //如果是分页类,那就只取第一个
        if ($data instanceof AbstractPaginator) {
            $data = $data[0];
        }
        //如果是集合 也只需要第一个就行了
        if ($data instanceof Collection) {
            $data = $data[0];
        }
        /**
         * model 提取数据结构
         */
        if ($data instanceof Model) {
            $result  = $data->toArray();
            $schemas = $data->getSchemas();
            $rows    = [];
            foreach ($result as $key => $item) {
                if (isset($schemas[$key])) {
                    $rows[$key] = $this->group($key, $item, $schemas[$key]['name'], $schemas[$key]['tip'], $schemas);
                } else {
                    //Schemas中没有的字段
                    $rows[$key] = $this->group($key, $item);
                }
            }
            $tmp['properties'] = $rows;
        }
        $temp = [];
        if (is_array($data)) {
            $temp['type'] = gettype($data);
            if (is_numeric(array_keys($data)[0])) {
                foreach ($data[0] as $key => $value) {
                    if (!is_array($value)) {
                        $temp[$key] = [
                            'type' => gettype($value),
                            "description" => $key,
                            "tip" => '',
                            'example' => $value,
                        ];
                    }
                }
            } else {
                foreach ($data as $key => $value) {
                    $temp[$key] = [
                        'type' => gettype($value),
                        "description" => $key,
                        "tip" => '',
                        'example' => $value,
                    ];
                }
            }
            $tmp['properties'] = $temp;
        }
        return $tmp;
    }

    /**
     * @param $example  //$result的值
     * @param  string  $description  name
     * @param  string  $tip  tip
     * @param  string  $schemas  判断下拉
     * @return array
     */
    protected function group($key, $example = '', $description = '', $tip = '', $schemas = '')
    {
        $type = gettype($example);
        $data = [];
        //dump($schemas);
        if ($type == 'array') {
//            dump($key);
//            dump($example);
            $array = $this->pareseDoc($example);
            //dump($array);
            return $array;
        } else {
            $data['type']        = $type;//类型
            $data['description'] = $description;//名称
            $data['example']     = $example;//值
            $data['tip']         = $tip;//备注
            if (isset($schemas[$key]['option']) && $example > 0)//下拉判断
            {
                $data['enum'] = $this->option($schemas[$key]['option']);
            }
            return $data;
        }
    }


    /**
     * @param $option
     * @return array
     * 修改下拉的样式
     */
    public function option($option)
    {
        $enum = [];
        foreach ($option as $key => $value) {
            $enum[] = $key.':'.$value;
        }
        return $enum;
    }
}