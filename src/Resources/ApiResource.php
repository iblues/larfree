<?php

namespace Larfree\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Arr;
use Illuminate\Pagination\AbstractPaginator;


/**
 * 基础的api结果处理器
 * Class ApiResource
 * @package Larfree\Resources
 */
class ApiResource extends Resource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
//        'links' => $this->paginationLinks($paginated),
//            'meta' => $this->meta($paginated),
//        return parent::toArray($request);
//

        //如果是分页类
        if( $this->resource instanceof AbstractPaginator){
//            $request = $this->getDoc($request);
            $return = parent::toArray($request);


            $link = $this->paginationLinks($return);
            $mate = $this->meta($return);
            $this->additional['link']=$link;
            $mate['per_page'] = $mate['per_page']*1;//转成数字
            $this->additional['mate']=$mate;
            return $return = $return['data'];
        }else {
//            $request = $this->getDoc($request);
            $data = parent::toArray($request);
            //解决一个奇怪的问题,如果有data 整体会上提一层
            if(isset($data['data']) || @is_null($data['data']) ){
                return ['data'=>$data];
            }else{
                return $data;
            }
        }

    }
    public function getDoc($return)
    {
        $data = $this->resource;
        $this->pareseDoc($data);
    }

    public function pareseDoc($data,$model='')
    {
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
            $result = $data->toArray();
            $schemas = $data->getSchemas();
            $rows = [];
            foreach ($result as $key => $item) {
                if (isset($schemas[$key])) {
                    $rows[$key] = $this->group($key, $item, $schemas[$key]['name'], $schemas[$key]['tip'], $schemas,$data);
                } else {
                    //Schemas中没有的字段
                    $rows[$key] = $this->group($key, $item);
                }
            }
        }

        if(is_array($data)){
            $data2['type'] = gettype($data);
            //dump(array_keys($data)[0]);

//            $rows[$key] = $this->group($key, $item, $schemas[$key]['name'], $schemas[$key]['tip'], $schemas,$data);

            if(is_numeric(array_keys($data)[0])){
                $data2['items'] = [
                    'type' => gettype($data),
                    "description" => '',
                    "tip" => $data,
                    'example' =>array_first($data),
                ];
            }else{
                foreach ($data as $key=>$value){
                    //dump($key);
                    $data2[$key] = $data3['items'] = [
                        'type' => gettype($value),
                        "description" => $key,
                        "tip" => '',
                        'example' =>$value,
                    ];

                }
            }
            dump($data2);
        }

    }

    /**
     * @param $example //$result的值
     * @param string $description  name
     * @param string $tip  tip
     * @param string $schemas  判断下拉
     * @return array
     */
    protected function group($key,$example,$description='',$tip='',$schemas='',$relations=''){
        $type = gettype($example);
        if($type=='array'){

//            $relations = $relations->getRelations();
//            dump($relations);
//            if(count($relations)>0){
//                return $this->pareseDoc($example,$relations);
//            }else{
            return $this->pareseDoc($example);
            //}
        }else{
            if($example == null) //判断值是否为空
                $data['type']='string';//类型;
            else
                $data['type']=$type;//值的类型;
            $data['description']=$description;//名称name
            $data['tip']=$tip;//tip
            if($example)//判断值是否存在,存在就赋值
                $data['example']=$example;//值
            if(isset($schemas[$key]['option']) && $example>0)//下拉判断
                $data['enum']=$this->option($schemas[$key]['option']);
            return $data;
        }
    }

//    public function getDoc($return)
//    {
//        $data = $this->resource;
//        $this->pareseDoc($data);
//    }
//
//    public function pareseDoc($data,$model='')
//    {
//        //如果是分页类,那就只取第一个
//        if ($data instanceof AbstractPaginator) {
//            $data = $data[0];
//        }
//        //如果是集合 也只需要第一个就行了
//        if ($data instanceof Collection) {
//            $data = $data[0];
//        }
//        /**
//         * model 提取数据结构
//         */
//        if ($data instanceof Model) {
//            $result = $data->toArray();
//            $schemas = $data->getSchemas();
//            $rows = [];
//            foreach ($result as $key => $item) {
//                if (isset($schemas[$key])) {
//                    $rows[$key] = $this->group($key, $item, $schemas[$key]['name'], $schemas[$key]['tip'], $schemas,$data);
//                } else {
//                    //Schemas中没有的字段
//                    $rows[$key] = $this->group($key, $item);
//                }
//            }
//            //return $rows;
////            dump($data);
////            dump($result);
////            dump($rows);
////            exit();
//
//        }
//        if(is_array($data)){
//            //dump($data);
//            $data2['type'] = gettype($data);
//            if(is_numeric(array_keys($data)[0])){
//                $data2['items'] = [
//                    'type' => gettype($data),
//                    "description" => '',
//                    "tip" => '',
//                    'example' =>array_first($data),
//                ];
//            }else{
//                foreach ($data as $key=>$value){
//                    $data2[$key] = $data3['items'] = [
//                        'type' => gettype($value),
//                        "description" => $key,
//                        "tip" =>'',
//                        'example' =>$value,
//                    ];
//
//                }
////                dump($data2);
////                exit();
//            }
//            //dump($data2);
//            exit();
//        }
////        exit();
//    }
//
//    /**
//     * @param $example //$result的值
//     * @param string $description  name
//     * @param string $tip  tip
//     * @param string $schemas  判断下拉
//     * @return array
//     */
//    protected function group($key,$example,$description='',$tip='',$schemas='',$data=""){
//        $type = gettype($example);
//        if($type=='array'){
////                $relations = $data->getRelations();
////                if(count($relations)>0){
////                    return $this->pareseDoc($example,$relations);
////                }
//            //dump($example);
//            return $this->pareseDoc($example);
//        }else{
//            //dump($example);
//            if($example == null) //判断值是否为空
//                $data['type']='string';//类型;
//            else
//                $data['type']=$type;//值的类型;
//                $data['description']=$description;//名称name
//                $data['tip']=$tip;//tip
//                if($example)//判断值是否存在,存在就赋值
//                    $data['example']=$example;//值
//                if(isset($schemas[$key]['option']) && $example>0)//下拉判断
//                    $data['enum']=$this->option($schemas[$key]['option']);
//            return $data;
//        }
//    }

    /**
     * @param $option
     * @return array
     * 修改下拉的样式
     */
    public function option($option){
        $enum = [];
        foreach ($option as $key=>$value){
            $enum[] = $key.':'.$value;
        }
        return $enum;
    }

    /**
     * 返回应该和资源一起返回的其他数据数组。
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        $return=[];
        if(!isset($this->additional['status'])){
            $return ['status']=1;
        }
        if(!isset($this->additional['code'])){
            $return ['code']=200;
        }
        return $return;
    }


    /**
     * Get the pagination links for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function paginationLinks($paginated)
    {
        return [
            'first' => $paginated['first_page_url'] ?? null,
            'last' => $paginated['last_page_url'] ?? null,
            'prev' => $paginated['prev_page_url'] ?? null,
            'next' => $paginated['next_page_url'] ?? null,
        ];
    }

    /**
     * Gather the meta data for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function meta($paginated)
    {
        return Arr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
    }

}
