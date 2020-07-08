<?php
/**
 * 基础larfree服务类
 * User: Blues
 * Date: 2019/9/22
 * Time: 11:54 AM
 */

namespace Larfree\Services;


class SchemaService implements BaseServiceInterface
{

    /**
     * 返回所有的配置列表
     * @author Blues
     *
     */
    public function tree()
    {
        $dir = $this->getDirList();
    }

    protected function getDirList($path = '', $preName = '')
    {
        if (!is_dir($path)) {
            return false;
        }
        $arr  = array();
        $data = scandir($path);
        foreach ($data as $value) {
            if ($value[0] != '.') {
                if (is_dir($path.'/'.$value)) {
                    $arr[$value] = $this->getDirList($path.'/'.$value, $value);
                } else {
                    $fileName                           = str_ireplace('.php', '', $value);
                    $arr[$preName.$fileName]['name']    = $preName.$fileName;
                    $arr[$preName.$fileName]['content'] = include $path.'/'.$value;
                }
            }
        }
        return $arr;
    }

    public function getList(array $toArray)
    {
        $path = schemas_path('Schemas');
        return $list = $this->getDirList($path);
    }

}
