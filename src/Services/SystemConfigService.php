<?php
/**
 * 配置文件服务类
 * User: Blues
 * Date: 2019/9/22
 * Time: 11:54 AM
 */

namespace Larfree\Services;

use Illuminate\Support\Facades\DB;
use Larfree\Repositories\SystemConfigRepository;

class SystemConfigService
{
    /**
     * @var SystemConfigRepository
     */
    public $repository;
    public $admin = false;

    public function __construct(SystemConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    public function setAdmin($flag = true){
        $this->admin = true;
        return $this;
    }

    /**
     * 批量更新
     * @author Blues
     * @param array $data
     * @param $cat
     */
    public function updateConfigByCat(array $data, $cat)
    {
        try {
            DB::beginTransaction();
            return $this->repository->updateConfigByCat($data, $cat);
        }catch (\Exception $e){
            DB::rollBack();
        }
        DB::commit();
    }


    /**
     * 批量读取
     * @author Blues
     * @param $cat
     * @return mixed
     */
    public function getAllByCat($cat){
        return $this->repository->getAllByCat($cat);
    }
}
