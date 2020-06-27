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

    protected function getDirList($path='')
    {
        $path = schemas_path($path);
        if (!is_dir($path)) {
            return false;
        }
        $arr = array();
        $data = scandir($path);
        foreach ($data as $value) {
            if ($value != '.' && $value != '..') {
                $arr[] = $value;
            }
        }
        return $arr;
    }

}
