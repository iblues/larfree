<?php
/**
 * 基础larfree服务类
 * User: Blues
 * Date: 2019/9/22
 * Time: 11:54 AM
 */

namespace Larfree\Services;

use Illuminate\Http\Request;

class LarfreeService
{

    public $repository;
    protected $admin = false;
    protected $link = false;

    public function __construct()
    {

    }

    /**
     * 后台模式
     * @author Blues
     * @param bool $flag
     * @return LarfreeService;
     */
    public function setAdmin($flag=true){
        $this->admin = $flag;
        return $this;
    }

    /**
     * 整个模型是不是待link关联
     * @author Blues
     * @param array $link
     * @return $this
     */
    public function link($link = [])
    {
        $this->link = $link;
        return $this;
    }

    /**
     * 获取标准模型的分页.
     * 通用接口在使用
     * @author Blues
     * @param Request $request
     * @param array|null $field
     * @param int $pageSize
     * @throws $e
     * @return mixed
     */
    public function paginate(Request $request, array $field = null, $pageSize = 10)
    {
        try {
            if ($field)
                $this->repository->field($field);

            $this->repository->link($this->link);

            return $this->repository->parseRequest($request->all())->paginate($pageSize);

            //改查询为统计
//        $chart = $request->get('@chart');
//        if($chart){
//            list( $schemas,$action) = explode('|',$chart);
//            $config = ComponentSchemas::getComponentConfig($schemas,$action);
//            return $data = $this->model->timeChart($config['y'],$config['x']['field'],$config['x']['format']);
//        }

            //批量导出
//        if($request->get('@export')){
//            list( $schemas,$action) = explode('|',$request->get('@export'));
//            $schemas = ComponentSchemas::getComponentConfig($schemas,$action);
//            $file =  (new FastExcel($model->take(5000)->get()))->download('file.xlsx',function ($data)use($schemas) {
//                $excel =[];
//                foreach ($schemas['component_fields'] as $schema){
//                    $excel[$schema['name']] = $data[$schema['key']];
//                }
//                return $excel;
//            });
//        }

        } catch (\Exception $e) {
            throw  $e;
        }
    }


    /**
     * 标准详情
     * 通用接口在使用
     * @author Blues
     * @param $id
     * @param Request $request
     * @param array|null $field
     * @throws \Exception
     * @return model
     */
    public function detail($id, Request $request, array $field = null)
    {
        try {
            if ($field)
                $this->repository->field($field);
            return $this->repository->link($this->link)->find($id);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * 标准新增
     * 通用接口在使用
     * @author Blues
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function addOne($data)
    {
        try {
            $row = $this->repository->create($data);
            //返回带完整格式的
            return $this->repository->link($this->link)->find($row['id']);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 标准更新
     * 通用接口在使用
     * @author Blues
     * @param $data
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function updateOne($data, $id)
    {
        try {
            $this->repository->update($data, $id);
            //返回带完整格式的
            return $this->repository->link($this->link)->find($id);
        } catch (\Exception $e) {
            throw $e;
        }

    }

    /**
     * 标准删除
     * 通用接口在使用
     * 如果$id是数组,那么支持批量
     * @author Blues
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function delete($id){
        try{
            return $this->repository->delete($id);
        }catch (\Exception $e){
            throw $e;
        }

    }
}
