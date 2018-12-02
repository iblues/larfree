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
use Larfree\Resources\ApiResource;
use Illuminate\Support\Facades\DB;
use Crypt;

class ApisController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $model;
    public $uid;
    protected $log=false;
    protected $msg='';
    protected $additional='';
    public $in;
    protected $link=true;

    public function __construct()
    {
        $name  = explode( '\\',get_class($this) );
        $this->modelName= substr(array_pop($name),0,-10);
        $this->modelName = array_pop($name).'.'.$this->modelName;
    }


    /**
     * 回调接口
     * @param $request
     * @param $model
     */
//    protected function before_hook(&$request,&$model,$method){
////        $method = $this->method();
//        $method = 'before_'.$method;
//        if(method_exists($this,$method)){
//            $this->$method($request,$model);
//        }
//    }

    /**
     * 回调接口
     * @param $request
     * @param $model
     */
//    protected function after_hook(&$data,Request $request,$method){
////        $method = $this->method();
//        $method = 'after_'.$method;
//        if(method_exists($this,$method)){
//            return $this->$method($data,$request);
//        }
//    }

    /**
     * 获取当前调用的方法名
     * @return mixed
     */
    protected function method(){
        $action = \Route::current();
        if(!$action)
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
        $chart = $request->get('@chart');
        //处理统计相关
        $model = $this->model->field($request['@columns']);
        if($this->link)
            $model = $this->model->link();
        $model = $this->parseRequest($request,$model);//解析请求,处理where ordery等
        $pageSize = isset($request->pageSize)?$request->pageSize:10;

        //改查询为统计
        if($chart){
            list( $schemas,$action) = explode('|',$chart);
            $config = ComponentSchemas::getComponentConfig($schemas,$action);
            return $data = $this->model->timeChart($config['y'],$config['x']['field'],$config['x']['format']);
        }

        //批量导出
        if($request->get('@export')){
            list( $schemas,$action) = explode('|',$request->get('@export'));
            $schemas = ComponentSchemas::getComponentConfig($schemas,$action);
            $file =  (new FastExcel($model->take(5000)->get()))->download('file.xlsx',function ($data)use($schemas) {
                $excel =[];
                foreach ($schemas['component_fields'] as $schema){
                    $excel[$schema['name']] = $data[$schema['key']];
                }
                return $excel;
            });
        }

        $data = $model->paginate($pageSize);
        return $data;
    }


    /**
     * 添加 post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = $this->model;
        //参数验证
        $data = $request->all();

        //开启日志系统
        if($this->log &&  method_exists($data,'startLog')){
            $data->startLog();
        }

        $data = $model->create($data);

        //hook
        $hook = $this->after_store($data,$request,__FUNCTION__);
        if($hook)
            return $hook;
        return Response()->success($data,'添加成功');
    }

    /**
     * store的回调
     * @param $data
     * @param $request
     */
    public function after_store($data,$request){
    }

    /**
     * 详情
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $model = $this->model->field($request['@columns']);
        if($this->link)
            $model = $this->model->link();
        $return = $model->find($id);
        return $return;
    }

    /**
     * 更新
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = $this->model;
        //参数验证
        $data = $request->all();
        $row = $model->find($id);
        foreach($data as $k=>$v){
            $row->$k=$v;
        }
        //开启日志系统
        if($this->log &&  method_exists($row,'startLog')){
            $row->startLog();
        }
        $flag = $row->save();

        //hook
        $hook = $this->after_update($row,$request,$flag);
        if($hook)
            return $hook;

        if($flag){
            return Response()->success($row,'修改成功');
        }else{
            apiError('修改失败');
        }

    }

    /**
     * store的回调
     * @param $data
     * @param $request
     */
    public function after_update($data,$request,$flag){
    }


    /**
     * 删除
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        $return = $this->model->where('id',$id)->delete();
        $hook = $this->after_destory($return,$request);
        if($hook)
            return $hook;
        if($return){
            return '删除成功';
        }else{
            apiError('删除失败');
        }
    }

    /**
     * 删除回调
     * @param $return
     * @param $request
     */
    public function after_destory($return,$request){
    }


    /**
     *
     * 同一个字段及多个字段组合查询
     * 示例: http://laravel.dev/api/min?id=1&search_id=2&gt_key=2&egt_key=2&lt_key=2&elt_key=3
     * @param $request
     * @return array
     */
    public function parseRequest($request,$model){

        $query = $request->all();
        $where = [];
        if(!$query)
            return $model;
        $columns = $this->model->getColumns();

//        DB::enableQueryLog();
        foreach($query as $key=>$val){

            //如果存在点.说明是链表的
//            if(stripos($val,'.')){
//                //链表
//            }
            //新模式
            $model->AdvWhere($key,$val);
        }

        if($request->get('@sort')){
            $sort= explode('.',$request->get('@sort'));
            $model->orderBy($sort[0],$sort[1]);
        }else{
            $model->orderBy('id','desc');
        }

        return $model;
    }

    public function setMsg(string $title){
        $this->msg=$title;
    }


    /**
     * 进行输入验证
     */
    public function getValidation($method,$httpMethod='POST')
    {
        $ext = isset($this->in[$method])?$this->in[$method]:['*'];
        $validate = ApiSchemas::getValidate( $this->modelName ,'in',$ext);
        //PUT修改的,不一定是所有字段都有,所以自动加上sometimes
        if($httpMethod=='PUT' && $method =='update'){
            array_walk($validate['rules'],function (&$value){
                if(stripos($value,'sometimes')===false){
                    $value = 'sometimes|'.$value;
                }
            });

        }
        return $validate;
    }

    /**
     * 获取输入变量的定义
     */
    public function getParamDefine($method,$group='in'){
        $ext = isset($this->in[$method])?$this->in[$method]:['*'];
        $validate = ApiSchemas::getApiAllowField( $this->modelName ,$group,$method,$ext);
        return $validate;
    }

    /**
     * 对输入的字段进行过滤
     * @param $request
     * @param $group
     * @param $method
     */
    protected function filterInput(&$request,$group,$method){
        $ext = isset($this->in[$method])?$this->in[$method]:[];
        //字段过滤
        $fields = ApiSchemas::getApiAllowField($this->modelName ,'in',$method,$ext);
        if($fields!=false){
            $data = array_diff_key($request->all(),array_flip(array_keys($fields)));
            foreach($data as $k=>$v){
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
        if( isset($parameters[0]) && $parameters[0] instanceof Request && ( $parameters[0]->getMethod() != 'GET' && $parameters[0]->getMethod() != 'DELETE')){
            $this->filterInput($parameters[0],'in',$method);
            $validate = $this->getValidation($method,$parameters[0]->getMethod());
            $this->validate($parameters[0], $validate['rules'], $validate['msg']);
        }

        //日志记录参数,方便debug
        if(function_exists('clock') && isset($parameters[0])) {
            clock($parameters[0]);
        }

        //执行真实的函数
        $return = call_user_func_array([$this, $method], $parameters);

        //如果已经有Response 也不管了
        if($return instanceof Response){
            return $return;
        }
        //如果已经有Resource 就不管了
        if($return instanceof Resource){
            return $return;
        }

        //如果是单文字
        if(is_string($return)){
            return (new ApiResource( collect([])  ))
                ->additional(['msg'=>$return]);
        }

        //进入默认的格式处理
        //如果不是collect类型
        if(!is_object($return)) {
            $return = collect($return);
        }
        return (new ApiResource( $return  ))
            ->additional(['msg'=>$this->msg]);

    }

}