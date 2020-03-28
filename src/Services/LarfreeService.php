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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Larfree\Exports\LarfreeExport;
use Larfree\Imports\LarfreeImport;
use Larfree\Libs\ComponentSchemas;
use Larfree\Repositories\LarfreeRepository;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Files\Disk;

class LarfreeService implements BaseServiceInterface
{

    /**
     * @var LarfreeRepository
     */
    protected $repository;
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
    public function setAdmin($flag = true)
    {
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
     * @param array $request
     * @param array|null $field
     * @param int $pageSize
     * @throws $e
     * @return mixed
     */
    public function paginate(array $request, array $field = null, $pageSize = 10)
    {
        try {
            if ($field)
                $this->repository->field($field);

            $this->repository->link($this->link);

            return $this->repository->parseRequest($request)->paginate($pageSize);

        } catch (\Exception $e) {
            throw  $e;
        }
    }


    /**
     * 标准详情
     * 通用接口在使用
     * @author Blues
     * @param $id
     * @param array $request
     * @param array|null $field
     * @throws \Exception
     * @return model
     */
    public function detail($id, $request, array $field = null)
    {
        try {
            if ($field)
                $this->repository->field($field);
            if($id === 'latest'){
                return $this->repository->link($this->link)->latest()->first();
            }elseif($id ==='oldest'){
                return $this->repository->link($this->link)->oldest()->first();
            }else{
                return $this->repository->link($this->link)->find($id);
            }
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
            DB::rollBack();
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
            if($id === 'latest'){
                $row = $this->repository->link($this->link)->latest()->first();
                $id = $row->getAttribute('id', 0);
            }elseif($id ==='oldest'){
                $row = $this->repository->link($this->link)->oldest()->first();
                $id = $row->getAttribute('id', 0);
            }
            if ($id == 0) {
                apiError('Not Found Record',null,404);
            }
            $this->repository->update($data, $id);
            //返回带完整格式的
            return $this->repository->link($this->link)->find($id);
        } catch (\Exception $e) {
            DB::rollBack();
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
    public function delete($id)
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            throw $e;
        }

    }


    /**
     * @author Blues
     * @return $this
     * @deprecated
     */
    static function new()
    {
        return app(static::class);
    }

    /**
     * @author Blues
     * @return $this
     */
    static function make(){
        return app(static::class);
    }


    public function chart($chart, $request)
    {

        $this->repository->link($this->link);
        //处理筛选条件,排序重置为空
        $request['@sort'] = '';
        $this->repository->parseRequest($request);

        list($schemas, $action) = explode('|', $chart);
        $config = ComponentSchemas::getComponentConfig($schemas, $action);
        return $this->repository->timeChart($config['y'], $config['x']['field'], $config['x']['format']);
    }

    /**
     * 基于配置的导出
     * @author Blues
     * @param $model = test.test_detail
     * @param $module = export
     * @param $request
     * @throws \Exception
     * @return string;
     */
    public function export($model, $module = 'export', $request = [])
    {
        $schemas = ComponentSchemas::getComponentConfig($model, $module);
        $list = $this->repository->parseRequest($request)->limit(2000)->get();
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
     * @author Blues
     * @param $model = test.test
     * @param string $module
     * @param $urlFile http://xxxx 远程文件
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Larfree\Exceptions\ApiException
     */
    public function import($model, $module = 'import', $urlFile)
    {
        $schemas = ComponentSchemas::getComponentConfig($model, $module);
        $client = new Client();
        try {
            $res = $client->request('GET', $urlFile);
            $fileName = 'tmp/' . time() . '.' . substr($urlFile, strrpos($urlFile, '.') + 1);
            if (Storage::put($fileName, $res->getBody())) {
                Excel::import(new LarfreeImport($schemas, $this->repository), $fileName);
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
