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

    public function setAdmin($flag = true)
    {
        $this->admin = true;
        return $this;
    }

    /**
     * 批量更新
     * @param array $data
     * @param $cat
     * @author Blues
     */
    public function updateConfigByCat(array $data, $cat)
    {
        try {
            DB::beginTransaction();
            $this->repository->updateConfigByCat($data, $cat);
        } catch (\Exception $e) {
            DB::rollBack();
            apiError($e->getMessage(), null, 500);
        }

        DB::commit();
        return $this->repository->getAllByCat($cat);
    }


    /**
     * 批量读取
     * @param $cat
     * @return mixed
     * @author Blues
     */
    public function getAllByCat($cat,$key)
    {
        return $this->repository->getAllByCat($cat,$key);
    }
}
