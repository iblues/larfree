<?php
namespace Larfree\Models;

use Larfree\Events\ModelSaved;
use Larfree\Events\ModelSaving;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Larfree\Libs\Schemas;
use Larfree\Libs\Table;
use DB;
use Watson\Rememberable\Rememberable;

class Api extends Model
{
    use AdvWhere,Chart,Rememberable;

    protected $_modelName = '';
    protected $_schemas='';
    protected $casts=[];
    protected $guarded = [];
    protected $appends = [];
    protected $_link = [];//link的列表
    protected $_dolink = [];//真正查询Link的
    protected $_dolinkCount = [];//统计link的数字
    protected $_tmpSave;//save事情临时存储用
    protected $dispatchesEvents = [
        'saved' => ModelSaved::class,//编辑和保存在里面
        'saving' => ModelSaving::class,//编辑和保存在里面
    ];

    /**
     * Api constructor.
     * 1,如果没有设置table,那么自动获取表名.
     * 2.获取modelName方便后面使用
     * 3.加载Schemas
     * 4.根据schemas的各个值 执行initProtected
     * @param array $attributes
     * @throws \Exception
     */
    public function __construct(array $attributes = [])
    {
        if(!$this->table)
            $this->table= humpToLine(basename(str_ireplace('\\','/',get_class($this))));
        parent::__construct($attributes);
        if (!$this->_modelName) {
            //自动提取modelName
            $this->_modelName=substr(get_class($this),strpos(get_class($this),'\Models\\')+8);
            $this->_modelName = str_ireplace('\\','.',$this->_modelName);
            $tmp = explode('.',$this->_modelName);
            if(@$tmp[1]){
                $this->_modelName = $tmp[0].'.'.substr($tmp[1],strlen($tmp[0]));
            }
        }
        $this->_schemas = Schemas::getSchemas($this->_modelName);

        if ($this->_schemas === false) {
            throw new \Exception('找不到schemas配置:' . $this->_modelName);
        }
        if ($this->_schemas === false)
            $this->_schemas = [];
        else
            $this->_schemas = array_map([$this, 'initProtected'], $this->_schemas);

    }


    public function scopeField($model,$field){
        if(!$field)
            return $model;

        if(!is_array($field)){
            $field = explode(',',$field);
        }

        //排除appends字段
        if($this->appends) {
            $this->appends = array_intersect($field, $this->appends);
            $field = array_diff($field, $this->appends); //排除append的字段.
        }

        //排除link
        if($field) {
            $this->_dolink = array_intersect($this->_link,$field );
            $link = array_flip($this->_dolink);
            $field = array_merge($field,$link);//为了处理用了as的字段
        }

        foreach($field as $f){
            if(stripos($f,'.count')) {
                $tmp = explode('.', $f);
                if ($tmp[1] == 'count') {
                    $_dolinkCount[$tmp[1]]=$tmp[1];
                }
            }
        }


        $columns = $this->getColumns();//只筛选数据库有的
        return $model->select(array_intersect($columns,$field));
    }

    /**
     * 配置的链表
     * @param $model
     * @return mixed
     */
    public function scopeLink($model){
        foreach($this->_dolink as $k=>$name){
            //多对多关系
            if($name)
                $model = $model->with($name);
        }
        foreach($this->_dolinkCount as $k=>$name){
            //多对多关系
            if($name)
                $model = $model->withCount($name);
        }
        return $model;
    }


    /**
     * 获取数据库中的数据列表
     * @return mixed
     */
    public function getColumns(){
        static $Columns=[];//laravels可能会出问题
        if(!$Columns)
            $Columns = Table::getColumns($this->getTable());
        return $Columns;
    }

    public function  getSchemas(){
        return $this->_schemas;
    }



    /**
     * 初始化各个私有变量
     * 1.检查cast
     * 2.检查是不是append
     * 3.是不是需要链表
     * @param $schemas
     * @return mixed
     */
    protected function initProtected($schemas){
        $key = $schemas['key'];
        if(isset($schemas['cast'])){
            $this->casts[$key]=$schemas['cast'];
        }

        if(isset($schemas['append'])){
            $this->appends[]=$key;
        }
        if (isset($schemas['link'])) {
            $link = $schemas['link'];

            switch ($link['model'][0]) {
                case 'belongsToMany':
                case 'hasMany':
                    $as = isset($link['as']) ? $schemas['link']['as'] : $key;
                    break;
                default:
                    $as = isset($link['as']) ? $schemas['link']['as'] : $key . '_link';
                    break;
            }
            $this->_link[$key] = $as;//添加到link里面.否则无法识别
            //不初始化
            if(!isset($link['init']) || $link['init']==true )
                $this->_dolink[$key] = $as;
        }
        //dolink是实际执行
        //_link是保存个原始的方便恢复原状


        return $schemas;
    }

    /**
     * 配置发起user_link的时候 回调他 进行关联
     * @param $field
     * @return mixed
     */
    protected function callLink($field){
        $link =  array_flip($this->_link);
        $field = $link[$field];
        $schema = $this->_schemas[$field];
        if(isset($schema['link'])) {
            $parm = $schema['link']['model'];
            $method = $parm[0];

            //自动创健中间关联表
            if (@$_ENV['APP_DEBUG']) {
                if ($method == 'belongsToMany'){
                    $this->createLinkTable(get_class($this), $parm[1]);
//                    return $this->belongsToMany($this,'user_user','user_id','user_sub_id','id','id');
                }
            }
            switch (count($parm)) {
                case '2':
                    $model = $this->$method($parm[1]);
                    break;
                case '3':
                    $model = $this->$method($parm[1], $parm[2]);
                    break;
                case '4':
                    $model = $this->$method($parm[1], $parm[2], $parm[3]);
                    break;
                case '5':
                    $model = $this->$method($parm[1], $parm[2], $parm[3], $parm[4]);
                    break;
                case '6':
                    $model = $this->$method($parm[1], $parm[2], $parm[3], $parm[4], $parm[5]);
                    break;
                case '7':
                    $model = $this->$method($parm[1], $parm[2], $parm[3], $parm[4], $parm[5],$parm[6]);
                    break;
            }

            //额外筛选
            if($model){
                if(isset($schema['link']['with'])){
                    $model = $model->with($schema['link']['with']);
                }
                if(isset($schema['link']['field'])){
                    //如果是has_many的时候 一定吧对应的外键给选出来,否则连不了
                    $model = $model->field($schema['link']['field']);
                }
                if(isset($schema['link']['where'])){
                    $model = $model->where($schema['link']['where']);
                }
                if(isset($schema['link']['limit'])){
                    $model = $model->take($schema['link']['limit']);
                }
            }
            return $model;
        }
        return $this;
    }

    protected function createLinkTable($table1,$table2){
        Table::creatLinkTable(getClassName($table1),getClassName($table2));
    }

    /**
     * 拦截,然后增加自己的component事件处理
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        foreach ($attributes as $key=>$attribute){
            if(isset($this->_schemas[$key])) {
                $this->callComponent('getAttribute', $this->_schemas[$key], $attributes);
            }
        }
        return $attributes;
    }

    /**
     * 调用对应的componet事件
     * @param $method
     * @param $config
     * @param $data
     */
    protected function callComponent($method,$config,&$data){
        $class='App\Components\Field\\'.ucfirst($config['type']);
        if(method_exists($class,$method)){
            $class::$method($config,$data);
        }
    }

    public function __call($method, $parameters)
    {
        if(in_array($method,$this->_link)){
            return $this->callLink($method);
        }else {
            return parent::__call($method, $parameters);
        }
    }

    public function create($data){
        foreach($data as $k=>$v){
            $this->$k = $v;
        }
        if( $this->save() )
            return $this;
        else
            return false;
    }

    /**
     * saving事件中,用来临时存储下列以外的数据
     * @param string $key
     * @return mixed
     */
    public function getTmpSave($key=''){
        if(!$key)
            return $this->_tmpSave;
        else
            return $this->_tmpSave[$key];
    }

    public function setTmpSave($key,$val){
        $this->_tmpSave[$key]=$val;
    }




    /**
     * 重写
     * 让appends可以动态增减
     * @param array $attributes
     * @param bool $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model =parent::newInstance($attributes, $exists);
        //解决get获取模型实例时丢失动态添加的appends
        $field = $this->getArrayableAppends();
        $model->appends = $field;
        return $model;
    }
}


/**
 * 统计组建
 * Trait Chart
 * @package Larfree\Models
 */
trait Chart {
    /**
     * 基于timestamp的统计
     */
    function scopeTimeChart($query,array $y,string $xField='create_at',$xFormat='%Y-%m-%d %H:%M:%S'){
//        $ySql="({$ySql})";//方便实现字段之见的 操作
//        $query2 = clone $query;

        $field = [DB::raw("FROM_UNIXTIME(UNIX_TIMESTAMP({$xField}),'{$xFormat}') as x")];
        $query = $query->groupBy('x')->orderBy("x","asc");
        $countData=[];


        foreach($y as $k=>$q){
            $queryField = $field;
            $newQuery = clone $query;
            $queryField[] = DB::raw('('.$q['sql']['field'].') as y');
            $count = $newQuery->select($queryField)->whereRaw($q['sql']['where'])->get();

            foreach($count as $v){
                $countData[$v->x][$k]=$v->y;
            }
        }
//        $date = array_keys($countData);

//        $minDate = min($date);
//        $maxDate = date('Y-m-d');

        return $countData;

    }
}
