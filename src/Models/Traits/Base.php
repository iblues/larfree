<?php

namespace Larfree\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Larfree\Events\ModelSaved;
use Larfree\Events\ModelSaving;
use Larfree\Exceptions\SchemasException;
use Larfree\Libs\Schemas;
use Larfree\Libs\Table;

trait Base
{
    use AdvWhere;

    protected $_modelName = '';
    protected $_schemas = '';

//    protected $hidden = ['deleted_at'];
    protected $_link = [];//link的列表
    protected $_doLink = [];//真正查询Link的
    protected $_doLinkCount = [];//统计link的数字
    protected $_tmpSave;//save事情临时存储用
//    protected $dispatchesEvents = [
//        'saved' => ModelSaved::class,//编辑和保存在里面
//        'saving' => ModelSaving::class,//编辑和保存在里面
//    ];

    /**
     * Api constructor.
     * 1,如果没有设置table,那么自动获取表名.
     * 2.获取modelName方便后面使用
     * 3.加载Schemas
     * 4.根据schemas的各个值 执行initProtected
     * @param  array  $attributes
     * @throws \Exception
     */
    public function __construct(array $attributes = [])
    {
        if (!$this->table) {
            $this->table = humpToLine(basename(str_ireplace('\\', '/', get_class($this))));
        }

        if (!$this->_modelName) {
            //自动提取modelName
            $this->_modelName = substr(get_class($this), strpos(get_class($this), '\Models\\') + 8);
            $this->_modelName = str_ireplace('\\', '.', $this->_modelName);
            $tmp              = explode('.', $this->_modelName);
            if (@$tmp[1]) {
                $this->_modelName = $tmp[0].'.'.substr($tmp[1], strlen($tmp[0]));
            }
        }

        //静态缓存此变量,避免多次读取
        static $schemasCache;
        if (!isset($schemasCache[$this->_modelName])) {
            $schemas                         = Schemas::getSchemas($this->_modelName);
            $schemasCache[$this->_modelName] = $schemas;
        } else {
            $schemas = $schemasCache[$this->_modelName];
        }
        $this->_schemas = $schemas;

        if ($this->_schemas === false) {
            $this->_schemas = [];
        } else {
            $this->_schemas = array_map([$this, 'initProtected'], $this->_schemas);
        }


        //保存相关事件
        $this->dispatchesEvents['saved']  = ModelSaved::class;
        $this->dispatchesEvents['saving'] = ModelSaving::class;

        //检查关联关系和config中的是不是有冲突. 如果有冲突.需要在method中加上@override.表示确实是要覆盖
        $this->checkLinkOverride($this->_link);

        parent::__construct($attributes);
    }

    protected function checkLinkOverride($schemas)
    {
        //非debug环境. 如果method有值,就直接用.
        if (!config('app.debug')) {
            return;
        }
        foreach ($schemas as $as) {
            if (method_exists($this, $as)) {
                //如果没配置过,直接跳过
                if (!isset($this->_schemas[$as])) {
                    return '';
                }
                //如果没有设置Link. 那说明在model中有可能设置过了
                if (!isset($schema['model'])) {
                    return;
                }
                $class  = new \ReflectionClass($this);
                $method = $class->getMethod($as);
                if (!stripos($method->getDocComment(), '@override')) {
                    $lang = static ::class." 配置的关联关系 <{$as}> 被覆盖了,如果确实要覆盖,请在对应方法注释中加上@override \n";
                    $lang .= static ::class." schemas relationship <{$as}> has been override.If you sure to override,Please add @override to the mehotd's phpdoc \n";
                    throw  new SchemasException($lang);
                }
            }
        }
    }

    /**
     * 类似select函数. 但是他可以动态排除filed和link的字段
     * @param $model
     * @param $field
     * @return mixed
     * @author Blues
     * @author Blues
     */
    public function scopeField($model, $field = '')
    {
        if (!$field) {
            return $model;
        }

        if (!is_array($field)) {
            $field = explode(',', $field);
        }

        //排除appends字段
        if ($this->appends) {
            $this->appends = array_intersect($field, $this->appends);
            $field         = array_diff($field, $this->appends); //排除append的字段.
        }

        //排除link
        if ($field) {
            $this->_doLink = array_intersect($this->_link, $field);
            $link          = array_flip($this->_doLink);
            $field         = array_merge($field, $link);//为了处理用了as的字段
        }

        foreach ($field as $f) {
            if (stripos($f, '.count')) {
                $tmp = explode('.', $f);
                if ($tmp[1] == 'count') {
                    $_doLinkCount[$tmp[1]] = $tmp[1];
                }
            }
        }


        $columns = $this->getColumns();//只筛选数据库有的
        return $model->select(array_intersect($columns, $field));
    }

    /**
     * 获取到对象后,想要对应的值. 暂时没用
     * @param $model
     * @param  array  $field
     * @return mixed
     * @deprecated
     * @author Blues
     */
    public function linkMissing($field = [])
    {
        //还需要改进到collection中去;
        $model = $this;

        foreach ($this->_doLink as $k => $name) {
            if ($field && !in_array($name, $field)) {
                continue;
            }
            //多对多关系
            if ($name) {
                $model = $model->loadMissing($name);
            }
        }
        foreach ($this->_doLinkCount as $k => $name) {
            if ($field && !in_array($name, $field)) {
                continue;
            }
            //多对多关系
            if ($name) {
                $model = $model->loadCount($name);
            }
        }
        return $model;
    }

    /**
     * 提前返回完整对象的配置的链表
     * @param $model
     * @param  array  $field
     * @return mixed
     */
    public function scopeLink($model, $field = [])
    {
        foreach ($this->_doLink as $k => $name) {
            if ($field !== true && $field && !in_array($name, $field)) {
                continue;
            }
            //多对多关系
            if ($name) {
                $model = $model->with($name);
            }
        }
        foreach ($this->_doLinkCount as $k => $name) {
            if ($field !== true && $field && !in_array($name, $field)) {
                continue;
            }
            //多对多关系
            if ($name) {
//                $model = $model->withCount($name);
            }
        }
        return $model;
    }


    /**
     * 将动态link的能力赋进去
     * @param $key
     * @return mixed
     * @author Blues
     */
    public function getRelationValue($key)
    {
        $return = parent::getRelationValue($key);
        //如果已经有结果了直接返回.
        if ($return) {
            return $return;
        }

        //动态查询的时候 自动去link查下有没有定义关联关系
        if (in_array($key, $this->_link)) {
            return $this->getRelationshipFromMethod($key);
        }

        //处理 xx_link , created_at_timestamp 等加工属性
        if (stripos($key, '_') !== false) {
            $schemasKey = explode('_', $key);
            array_pop($schemasKey);
            $schemasKey = implode('_', $schemasKey);
            //读取component拼接 输出link的字段.
            if ($schemasKey && $schemas = $this->getSchemas($schemasKey)) {
                $this->callComponent($schemas, 'getAttribute', $this);
                return $this->attributes[$key];
            }
        }
    }


    /**
     * 获取数据库中的数据列表
     * @return mixed
     */
    public function getColumns()
    {
        static $Columns = [];//laravels可能会出问题
        if (!$Columns) {
            $Columns = Table::getColumns($this->getTable());
        }
        return $Columns;
    }

    /**
     * 获取配置项
     * @param  string  $key  ='' 空为全部
     * @return array|string
     * @author Blues
     */
    public function getSchemas($key = '')
    {
        if (!$key) {
            return $this->_schemas;
        } else {
            return isset($this->_schemas[$key]) ? $this->_schemas[$key] : null;
        }
    }

    public function getModelName()
    {
        return $this->_modelName;
    }


    /**
     * 初始化各个私有变量
     * 1.检查cast
     * 2.检查是不是append
     * 3.是不是需要链表
     * @param $schemas
     * @return mixed
     */
    protected function initProtected($schemas)
    {
        $key = $schemas['key'];
        if (isset($schemas['cast'])) {
            $this->casts[$key] = $schemas['cast'];
        }

        if (isset($schemas['append'])) {
            $this->appends[] = $key;
        }
        if (isset($schemas['link'])) {
            $link = $schemas['link'];
            //默认as是key本身
            $as                = $schemas['link']['as'] ?? $key;
            $this->_link[$key] = $as;//添加到link里面.否则无法识别
            //  后续发现设置了 在loadMissing会有问题 而且新版本不需要设置就可以懒加载了
//            $this->setRelation($as, $this->$as());//设置有关联关系. 方便后续用relationLoaded判断.

            //不初始化
            if (!isset($link['init']) || $link['init'] == true) {
                $this->_doLink[$key] = $as;
            }
        }

        //读取Component上的参数
//        dump($schemas);

        //doLink是实际执行
        //_link是保存个原始的方便恢复原状

        return $schemas;
    }


    /**
     * 配置发起user_link的时候 回调他 进行关联
     * @param $field
     * @return mixed
     */
    protected function callLink($field)
    {
        $link   = array_flip($this->_link);
        $field  = $link[$field];
        $schema = $this->_schemas[$field];
        if (isset($schema['link']) && isset($schema['link']['model'])) {
            $parm   = $schema['link']['model'];
            $method = $parm[0];

            if ($method == 'belongsToMany') {
                //没有手动定义中间表的
                if (!isset($parm[2])) {
                    //自动创健中间关联表
                    if (config('app.debug')) {
                        $this->createLinkTable(get_class($this), $parm[1]);
                    }
                    //中间表名
                    $tableName = $this->getLinkTableName(get_class($this), $parm[1]);
                    $parm[2]   = $tableName;
                }
            }

            array_shift($parm);
            $model = $this->$method(...$parm);

            //额外筛选
            if ($model) {
                if (isset($schema['link']['with'])) {
                    $model = $model->with($schema['link']['with']);
                }
                if (isset($schema['link']['field'])) {
                    //如果是has_many的时候 一定吧对应的外键给选出来,否则连不了
                    $model = $model->field($schema['link']['field']);
                }
                if (isset($schema['link']['where'])) {
                    $model = $model->where($schema['link']['where']);
                }
                if (isset($schema['link']['limit'])) {
                    $model = $model->take($schema['link']['limit']);
                }
            }
            return $model;
        }
        return $this;
    }

    protected function createLinkTable($table1, $table2)
    {
        Table::creatLinkTable(getClassName($table1), getClassName($table2));
    }

    protected function getLinkTableName($table1, $table2)
    {
        return Table::getLinkTableName(getClassName($table1), getClassName($table2));
    }

    /**
     * 拦截,然后增加自己的component事件处理
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        foreach ($attributes as $key => $attribute) {
            if (isset($this->_schemas[$key])) {
                $this->callComponent($this->_schemas[$key], 'getAttribute', $attributes);
            }
        }
        return $attributes;
    }

    public function setAttribute($key, $value)
    {
        if (isset($this->_schemas[$key])) {
            $this->callComponent($this->_schemas[$key], 'setAttribute', $value);
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * 调用对应的componet事件
     * @param $method
     * @param $config
     * @param $data
     * @return mixed
     */
    protected function callComponent(&$config, $method = 'getAttribute', &$data = null)
    {
        //常用的就不用去找了
        $blackType = ['text', 'number'];
        $type      = Arr::get($config, 'type', 'text');


        //规范返回类型
        $sqlType = Arr::get($config, 'sql_type', '');
        //数字化
        if (stripos($sqlType, 'int') !== false && isset($data[$config['key']])) {
            $data[$config['key']] = $data[$config['key']] * 1;
        }

        //常用的可以不用去判断了 除非指定了component字段.
        if (in_array($type, $blackType)) {
            return '';
        }
        //如果有指定component字段, 用component的
        $component = ucfirst(Arr::get($config, 'component', $type));
        //扩展包内的
        $larfreeClass = 'Larfree\Components\Field\\'.$component;
        //程序内的
        $class = 'App\Components\Field\\'.$component;
        if (method_exists($larfreeClass, $method)) {
            $larfreeClass::$method($config, $data);
        } elseif (method_exists($class, $method)) {
            $class::$method($config, $data);
        }
    }

    public function __call($method, $parameters)
    {
        if (!method_exists($this, $method) && in_array($method, $this->_link)) {
            return $this->callLink($method);
        } else {
            return parent::__call($method, $parameters);
        }
    }

//    public function create($data)
//    {
//        foreach ($data as $k => $v) {
//            $this->$k = $v;
//        }
//        if ($this->save())
//            return $this;
//        else
//            return false;
//    }

    /**
     * saving事件中,用来临时存储下列以外的数据
     * @param  string  $key
     * @return mixed
     */
    public function getTmpSave($key = '')
    {
        if (!$key) {
            return $this->_tmpSave;
        } else {
            return $this->_tmpSave[$key];
        }
    }

    public function setTmpSave($key, $val)
    {
        $this->_tmpSave[$key] = $val;
    }


    /**
     * 重写
     * 让appends可以动态增减
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);
        //解决get获取模型实例时丢失动态添加的appends
        $field          = $this->getArrayableAppends();
        $model->appends = $field;
        return $model;
    }


    /**
     * @author Blues
     * 返回model中的link
     */
    public function getLink()
    {
        return $this->_link;
    }

    /**
     * 保存和添加的回调
     * @param $data
     */
    public function beforeSave(Model $data)
    {
    }

    /**
     * 保存和添加的回调
     * @param $data
     */
    public function afterSave(Model $data)
    {
    }
}

