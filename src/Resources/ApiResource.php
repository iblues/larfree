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
        //如果是分页类
        if( $this->resource instanceof AbstractPaginator){
//            $request = $this->getDoc($request);//自动文档
            $return = parent::toArray($request);
            $link = $this->paginationLinks($return);
            $mate = $this->meta($return);
            $this->additional['link']=$link;
            $mate['per_page'] = $mate['per_page']*1;//转成数字
            $this->additional['meta']=$mate;
            return $return = $return['data'];
        }else {
//            $request = $this->getDoc($request);//自动文档
            $data = parent::toArray($request);
            //解决一个奇怪的问题,如果有data 整体会上提一层
            if(isset($data['data']) || @is_null($data['data']) ){
                return ['data'=>$data];
            }else{
                return $data;
            }
        }

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
