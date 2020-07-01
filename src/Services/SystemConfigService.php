<?php
/**
 * 配置文件服务类
 * User: Blues
 * Date: 2019/9/22
 * Time: 11:54 AM
 */

namespace Larfree\Services;

use Illuminate\Support\Facades\DB;
use Larfree\Models\System\SystemConfig;
use Larfree\Repositories\SystemConfigRepository;

class SystemConfigService
{
    /**
     * @var SystemConfigRepository
     */
    public $repository;
    public $admin = false;

    public function __construct(SystemConfig $model)
    {
        $this->model = $model;
    }

    public function setAdmin($flag = true)
    {
        $this->admin = true;
        return $this;
    }

    /**
     * 批量更新
     * @param  array  $data
     * @param $cat
     * @return mixed
     * @throws \Larfree\Exceptions\ApiException
     * @author Blues
     */
    public function updateConfigByCat(array $data, $cat)
    {
        try {
            DB::beginTransaction();
            foreach ($data as $k => $v) {
                if (!is_null($v)) {
                    $this->model->updateOrCreate(
                        ['key' => $k, 'cat' => $cat],
                        ['value' => $v, 'type' => 'json']
                    );
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            apiError($e->getMessage(), null, 500);
        }

        DB::commit();

        $this->refreshCache();
        return $this->getAllByCat($cat);
    }


    /**
     * 刷新缓存
     * @author Blues
     */
    public function refreshCache()
    {
        $file = config_path('/system.php');
        $data = $this->model->where('cat','!=','plane')->get()->toArray();
        $group=[];
        foreach ($data as $val){
            $group[$val['cat']][$val['key']]=$val;
        }

        $strings = [];
        foreach ($group as $key=>$fields){
            $string = "    '{$key}' => [\r\n";

            foreach ($fields as $field){
                $string.="        \"{$field['key']}\" => json_decode('".json_encode($field["value"])."',1),"."\r\n";
            }

            $string .= "    ]";
            $strings[]=$string;
        }
        $strings = implode(",    \r\n",$strings);

        $content=<<<CONTENT
<?php
/**
 * larfree的系统配置. 生成到system文件中. 方便调用
 */
return [
{$strings}
];
CONTENT;

        file_put_contents($file,$content);
        //正式环境. 保存就刷新缓存
        if (!config('app.debug')) {
            Artisan::call('config:cache');
        }
    }

    /**
     * 批量读取
     * @param $category
     * @param $key
     * @return mixed
     * @throws \Larfree\Exceptions\ApiException
     * @author Blues
     */
    public function getAllByCat($category = '', $key = '')
    {
        //获取对应的配置文件 , 还需要进一步处理
//        $data = Schemas::getSchemas('Config.'.$category);
        if (!$key) {
            if ($category) {
                $data = $this->model->link()->where('cat', $category)->get();
            } else {
                //读取所有配置
                $data = $this->model->link()->get();
            }
            if (!$data) {
                apiError('配置文件不存在');
            }
            return $data->pluck('value', 'key');
        } else {
            $data = $this->model->where('key', $key)->where('cat', $category)->first();
            if (!$data) {
                apiError('配置文件不存在');
            }
            return [$key=>$data->value];
        }
    }


    /**
     * 生成config下的system配置文件
     * @author Blues
     *
     */
    public function createFileConfig()
    {
    }
}
