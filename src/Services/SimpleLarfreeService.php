<?php
/**
 * 基础larfree服务类
 * User: Blues
 * Date: 2019/9/22
 * Time: 11:54 AM
 */

namespace Larfree\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Larfree\Exports\LarfreeExport;
use Larfree\Imports\LarfreeImport;
use Larfree\Libs\ComponentSchemas;
use Maatwebsite\Excel\Facades\Excel;

class SimpleLarfreeService implements BaseServiceInterface
{

    /**
     * @var Model
     */
    protected $model;
    protected $admin = false;
    protected $link = [];

    public function __construct()
    {
    }

    /**
     * 后台模式
     * @param  bool  $flag
     * @return LarfreeService;
     * @author Blues
     */
    public function setAdmin($flag = true)
    {
        $this->admin = $flag;
        return $this;
    }

    /**
     * 整个模型是不是待link关联
     * @param  array  $link
     * @return $this
     * @author Blues
     */
    public function link($link = [])
    {
        $this->link = $link;
        return $this;
    }

    /**
     * 获取标准模型的分页.
     * 通用接口在使用
     * @param  array  $request
     * @param  array|null  $field
     * @param  int  $pageSize
     * @return mixed
     * @throws $e
     * @author Blues
     */
    public function paginate(array $request, array $field = null, $pageSize = 10)
    {
        try {
            if ($field) {
                $this->model = $this->model->field($field);
            }

            return $this->model->link($this->link)->parseRequest($request)->paginate($pageSize);
        } catch (\Exception $e) {
            throw  $e;
        }
    }


    /**
     * 标准详情
     * 通用接口在使用
     * @param $id  = 0 的时候 最倒叙第一条
     * @param  array  $request
     * @param  array|null  $field
     * @return model
     * @throws \Exception
     * @author Blues
     */
    public function detail($id, $request, array $field = null)
    {
        try {
            if ($field) {
                $this->model = $this->model->field($field);
            }

            if ($id === 'latest') {
                return $this->model->link($this->link)->latest()->first();
            } elseif ($id === 'oldest') {
                return $this->model->link($this->link)->oldest()->first();
            } else {
                return $this->model->link($this->link)->find($id);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * 标准新增
     * 通用接口在使用
     * @param $data
     * @return mixed
     * @throws \Exception
     * @author Blues
     */
    public function addOne($data)
    {
        try {
            $row = $this->model;
            foreach ($data as $key => $val) {
                $row->setAttribute($key, $val);
            }
            $row->save();
            return $row->link($this->link)->find($row->id);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * 标准更新
     * 通用接口在使用
     * @param $data
     * @param $id
     * @return mixed
     * @throws \Exception
     * @author Blues
     */
    public function updateOne($data, $id)
    {
        try {
            //如果id为0 就取最新的一条.
            if ($id === 'latest') {
                $row = $this->model->link($this->link)->latest()->first();
                $id  = $row->getAttribute('id', 0);
            } elseif ($id === 'oldest') {
                $row = $this->model->link($this->link)->oldest()->first();
                $id  = $row->getAttribute('id', 0);
            }
            if ($id == 0) {
                apiError('Not Found Record', null, 404);
            }


            //update不会触发一些函数. 用save代替
            $row = $this->model->link($this->link)->where('id', $id)->first();
            if (!$row) {
                apiError('记录不存在');
            }
            foreach ($data as $key => $val) {
                $row->setAttribute($key, $val);
            }
            $flag = $row->save();

            if (!$flag) {
                apiError('保存失败', null, 500);
            }
            //返回带完整格式的
            return $this->model->link($this->link)->find($id);
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * 标准删除
     * 通用接口在使用
     * 如果$id是数组,那么支持批量
     * @param $id
     * @return mixed
     * @throws \Exception
     * @author Blues
     */
    public function delete($id)
    {
        try {
            return $this->model->where('id', $id)->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * @return $this
     * @author Blues
     * @deprecated
     */
    static function new()
    {
        return app(static::class);
    }

    /**
     * @return $this
     * @author Blues
     */
    static function make()
    {
        return app(static::class);
    }


    public function chart($chart, $request)
    {
        //处理筛选条件,排序重置为空
        $request['@sort'] = '';
        $this->model->parseRequest($request);

        list($schemas, $action) = explode('|', $chart);
        $config = ComponentSchemas::getComponentConfig($schemas, $action);
        return $this->repository->timeChart($config['y'], $config['x']['field'], $config['x']['format']);
    }

    /**
     * 基于配置的导出
     * @param $model  = test.test_detail
     * @param $module  = export
     * @param $request
     * @return string;
     * @throws \Exception
     * @author Blues
     */
    public function export($model, $module = 'export', $request = [])
    {
        $schemas = ComponentSchemas::getComponentConfig($model, $module);
        $list    = $this->model->link($this->link)->parseRequest($request)->limit(2000)->get();
        return Excel::download(new LarfreeExport($list, $schemas), 'users.xlsx');
//        $file = (new FastExcel($list))->download('export.xlsx', function ($data) use ($schemas) {
//            $excel = [];
//            foreach ($schemas['component_fields'] as $schema) {
//                $excel[$schema['name']] = $data[$schema['key']];
//            }
//            return $excel;
//        });
        return $file;
    }

    /**
     * 基于配置的导入
     * @param $model  = test.test
     * @param  string  $module
     * @param $urlFile  http://xxxx 远程文件
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Larfree\Exceptions\ApiException
     * @author Blues
     */
    public function import($model, $module = 'import', $urlFile)
    {
        $schemas = ComponentSchemas::getComponentConfig($model, $module);
        $client  = new Client();
        try {
            $res      = $client->request('GET', $urlFile);
            $fileName = 'tmp/'.time().'.'.substr($urlFile, strrpos($urlFile, '.') + 1);
            if (Storage::put($fileName, $res->getBody())) {
                Excel::import(new LarfreeImport($schemas, $this->model->link($this->link)), $fileName);
            }
            //导入完成后,删除本地缓存
            Storage::delete($fileName);
        } catch (ClientException $e) {
            apiError('下载文件失败');
        } catch (\Exception $e) {
            //导入失败,删除本地缓存
            Storage::delete($fileName);
            apiError('导入失败');
        }
    }
}
