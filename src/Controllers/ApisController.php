<?php
/**
 * API用的
 */

namespace Larfree\Controllers;


use Iblues\AnnotationTestUnit\Annotation as ATU;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth;
use Larfree\Libs\ApiSchemas;
use Larfree\Resources\ApiResource;
use Illuminate\Support\Facades\DB;
use Crypt;
use Larfree\Services\SimpleLarfreeService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApisController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $model;
    /**
     * @var SimpleLarfreeService
     */
    protected $service;
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
     * 分页列表
     * @author Blues
     * @param Request $request
     * @return mixed
     * @throws \Exception
     * @ATU\Api(
     *     @ATU\RouteIgnore()
     * )
     */
    public function index(Request $request)
    {
        return $this->service->link()->paginate($request->toArray(), $request->get('@columns'), $request->get('pageSize'));
    }

    /**
     * 添加
     * @param Request $request
     * @return mixed
     * @throws \Exception
     * @author Blues
     */
    public function store(Request $request)
    {
        return $this->service->addOne($request->all());
    }


    /**
     * 详情
     * @param $id
     * @param Request $request
     * @return \Larfree\Services\model
     * @throws \Exception
     * @author Blues
     * @ATU\Api(
     *     path=0,
     *     @ATU\RouteIgnore()
     * )
     */
    public function show($id, Request $request)
    {
        return $this->service->link()->detail($id, $request->toArray(), $request->get('@columns'));
    }

    /**
     * 更新
     * @author Blues
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        return $this->service->link()->updateOne($request->all(),$id);
    }


    /**
     * 删除
     * @author Blues
     * @param $id
     * @param Request $request
     * @return string|void
     * @throws \Exception
     */
    public function destroy($id, Request $request)
    {
        return $this->service->delete($id);
    }


    /**
     * 设置msg
     * @author Blues
     * @param string $title
     */
    protected function setMsg(string $title)
    {
        $this->msg = $title;
    }


    /**
     * 获取验证规则. put的时候 会自动加上sometimes
     * 接口文档那边要用 所以暂时设置为public
     * @author Blues
     * @param $method 函数名
     * @param string $httpMethod http方法
     * @return array
     * @throws \Exception
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
     * $in的定义
     * 接口文档可能会用,所以public
     * @author Blues
     * @param $method
     * @param string $group
     * @return array
     * @throws \Exception
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
     * 魔术回调
     * @author Blues
     * @param $method
     * @param $parameters
     * @return \Illuminate\Support\Collection|ApiResource|mixed
     * @throws \Exception
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
        if ($return instanceof BinaryFileResponse) {
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
