<?php
/**
 * Created by PhpStorm.
 * User: lanyang
 * Date: 2019/11/25
 * Time: 6:09 PM
 */

namespace Larfree\Imports;


use Illuminate\Database\Eloquent\Model;
use Larfree\Repositories\LarfreeRepository;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class LarfreeImport implements ToModel, WithHeadingRow
{

    public $schema;
    /**
     * @var Model
     */
    public $model;

    public function __construct($schema, Model $model)
    {
        $this->model = $model;
        $this->schema = $schema;
        //去掉过滤器, 才能识别中文
        HeadingRowFormatter::default('none');
    }

    public function model(array $data)
    {
        $row = [];
        $pk = false;
        foreach ($this->schema['fields'] as $key => $field) {
            //获取主键
            if ($key == 'id' || isset($field['pk'])) {
                $pk = $key;
            }
            //链表的暂时不处理,后面有空在处理
            if(isset($field['link']) && $field['link']['model'][0]!='belongsTo' ){
                continue;
            }
            $row[$key] = $data[$field['name']] ?? '';
        }
        if($pk){
            $this->model->updateOrCreate([$pk=>$row[$pk]],$row);
        }else{
            $this->model->create($row);
        }


    }

    public function format()
    {
        HeadingRowFormatter::default('custom');
        return $this;
    }

}
