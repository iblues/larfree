<?php
/**
 * API用的
 */

namespace Larfree\Controllers;


use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth;
use Larfree\Libs\ApiSchemas;
use Larfree\Libs\ComponentSchemas;
use Larfree\Libs\Schemas;
use Larfree\Repositories\LarfreeRepository;
use Larfree\Resources\ApiResource;
use Illuminate\Support\Facades\DB;
use Crypt;
use Prettus\Repository\Eloquent\BaseRepository;

class ApisController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $model;
    /**
     * @var LarfreeRepository
     */
    public $repository;
    public $service;
    public $uid;
    protected $log = false;
    protected $msg = '';
    protected $additional = '';
    public $in;
    protected $link = true;

    public function __construct()
    {
        $name = explode('\\', get_class($this));
        $this->modelName = substr(array_pop($name), 0, -10);
        $this->modelName = array_pop($name) . '.' . $this->modelName;
    }

    /**
     * 获取当前调用的方法名
     * @return mixed
     */
    protected function method()
    {
        $action = \Route::current();
        if (!$action)
            return '';
        $action = $action->getActionName();
        list($class, $method) = explode('@', $action);
        return $method;
    }

    /**
     * 列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //处理统计相关
        if ($request['@columns'])
            $this->repository->field($request['@columns']);

        if ($this->link)
            $this->repository->link();

        $this->repository = $this->parseRequest($request, $this->repository);//解析请求,处理where ordery等

        $pageSize = isset($request->pageSize) ? $request->pageSize : 10;

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

        $data = $this->repository->paginate($pageSize);
        return $data;
    }


    /**
     * 保存
     * @author Blues
     * @param Request $request
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        $model = $this->model;
        //参数验证
        $data = $request->all();

        $row = $this->repository->create($data);

        //hook
        $hook = $this->after_store($row, $request, __FUNCTION__);
        if ($hook)
            return $hook;

        if ($this->link)
            $this->repository->link();

        return $this->repository->find($row['id']);
    }

    /**
     * store的回调
     * @param $data
     * @param $request
     */
    public function after_store($data, $request)
    {
    }

    /**
     * 详情
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if ($request['@columns'])
            $this->repository->field($request['@columns']);

        if ($this->link)
            $this->repository->link();
        $return = $this->repository->find($id);
        return $return;
    }

    /**
     * 编辑
     * @author Blues
     * @param Request $request
     * @param $id
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(Request $request, $id)
    {
        //参数验证
        $data = $request->all();
        $row = $this->repository->update($data, $id);

        //hook
        $hook = $this->after_update($row, $request);
        if ($hook)
            return $hook;

        return $this->repository->link()->find($id);
    }

    /**
     * store的回调
     * @param $data
     * @param $request
     */
    public function after_update($data, $request)
    {
    }

    /**
     * 删除
     * @author Blues
     * @param $id
     * @param Request $request
     * @return string|void
     * @throws \Larfree\Exceptions\ApiException
     */
    public function destroy($id, Request $request)
    {
        $return = $this->repository->delete($id);
        $hook = $this->after_destory($return, $request);
        if ($hook)
            return $hook;
        if ($return) {
            return '删除成功';
        } else {
            apiError('删除失败');
        }
    }

    /**
     * 删除回调
     * @param $return
     * @param $request
     */
    public function after_destory($return, $request)
    {
    }


    /**
     *
     * 同一个字段及多个字段组合查询
     * 示例: http://laravel.dev/api/min?id=1&search_id=2&gt_key=2&egt_key=2&lt_key=2&elt_key=3
     * @param $request
     * @return array
     */
    public function parseRequest($request, BaseRepository $repository)
    {

        $query = $request->all();

        /**
         * 后期需要完全独立到repository
         * @author Blues
         */
        $repository->scopeQuery(function ($model) use ($query) {

            if (!$query)
                return $model;
//        $columns = $this->model->getColumns();

//        DB::enableQueryLog();
            foreach ($query as $key => $val) {

                //如果存在点.说明是链表的
//            if(stripos($val,'.')){
//                //链表
//            }
                //新模式
                $model->AdvWhere($key, $val);
            }

            if (@$query['@sort']) {
                $sort = explode('.', @$query['@sort']);
                $model->orderBy($sort[0], $sort[1]);
            } else {
                $model->orderBy('id', 'desc');
            }

            return $model;
        });

        return $repository;

    }

    public function setMsg(string $title)
    {
        $this->msg = $title;
    }


    /**
     * 进行输入验证
     */
    public function getValidation($method, $httpMethod = 'POST')
    {
        $ext = isset($this->in[$method]) ? $this->in[$method] : ['*'];
        $validate = ApiSchemas::getValidate($this->modelName, 'in', $ext);
        //PUT修改的,不一定是所有字段都有,所以自动加上sometimes
        if ($httpMethod == 'PUT' && $method == 'update') {
            array_walk($validate['rules'], function (&$value) {
                if (stripos($value, 'sometimes') === false) {
                    $value = 'sometimes|' . $value;
                }
            });

        }
        return $validate;
    }

    /**
     * 获取输入变量的定义
     */
    public function getParamDefine($method, $group = 'in')
    {
        $ext = isset($this->in[$method]) ? $this->in[$method] : ['*'];
        $validate = ApiSchemas::getApiAllowField($this->modelName, $group, $method, $ext);
        return $validate;
    }

    /**
     * 对输入字段过滤 , in字段为字段明细
     * @author Blues
     * @param $request
     * @param $group
     * @param $method
     * @throws \Exception
     */
    protected function filterInput(&$request, $group, $method)
    {
        $ext = isset($this->in[$method]) ? $this->in[$method] : [];
        //字段过滤
        $fields = ApiSchemas::getApiAllowField($this->modelName, 'in', $method, $ext);
        if ($fields != false) {
            $data = array_diff_key($request->all(), array_flip(array_keys($fields)));
            foreach ($data as $k => $v) {
                $request->offsetUnset($k);
            }
        }
    }


    /**
     * 所有的请求的回调
     * @param $method
     * @param $parameters
     * @return \Illuminate\Support\Collection|mixed
     * @throws \Larfree\Exceptions\ApiException
     */
    public function callAction($method, $parameters)
    {

        /**
         * 进行输入参数的验证和过滤
         * //当参数存在,并且他是Request 而且不是Get. get就不做参数验证了
         */
//        dd($parameters[0]);
        if (isset($parameters[0]) && $parameters[0] instanceof Request && ($parameters[0]->getMethod() != 'GET' && $parameters[0]->getMethod() != 'DELETE')) {
            $this->filterInput($parameters[0], 'in', $method);
            $validate = $this->getValidation($method, $parameters[0]->getMethod());
            $this->validate($parameters[0], $validate['rules'], $validate['msg']);
        }

        //日志记录参数,方便debug
        if (function_exists('clock') && isset($parameters[0])) {
            clock($parameters[0]);
        }

        //执行真实的函数
        $return = call_user_func_array([$this, $method], $parameters);

        //如果已经有Response 也不管了
        if ($return instanceof Response) {
            return $return;
        }
        //如果已经有Resource 就不管了
        if ($return instanceof Resource) {
            return $return;
        }

        //如果是单文字
        if (is_string($return)) {
            return (new ApiResource(collect([])))
                ->additional(['msg' => $return]);
        }

        //进入默认的格式处理
        //如果不是collect类型
        if (!is_object($return)) {
            $return = collect($return);
        }
        return (new ApiResource($return))
            ->additional(['msg' => $this->msg]);

    }

}
