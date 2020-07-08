<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 16:52
 */

namespace Larfree\Console\Commands;


use App\Models\Address\AddressArea;
use App\Models\Address\AddressCity;
use App\Models\Address\AddressProvince;
use Illuminate\Console\Command;

class AddressMake extends Command
{
    protected $signature = 'larfree:address_init';

    protected $description = '导入省市区到数据库';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        $arguments = $this->arguments();//获取命令中的参数和选项
        $area      = file_get_contents(dirname(__FILE__).'/area.json');
        $area      = json_decode($area);
        foreach ($area->province_list as $pKey => $province) {
            $data            = [
                'id' => intval(substr($pKey, 0, 2).'0000'),
                'name' => $province,
            ];
            $addressProvince = new AddressProvince();
            $addressProvince->firstOrCreate($data);
        }
        foreach ($area->city_list as $cKey => $city) {
            $data        = [
                'id' => intval(substr($cKey, 0, 4).'00'),
                'province_id' => intval(substr($cKey, 0, 2).'0000'),
                'name' => $city,
            ];
            $addressCity = new AddressCity();
            $addressCity->firstOrCreate($data);
        }
        foreach ($area->county_list as $aKey => $area) {
            $data        = [
                'id' => intval(substr($aKey, 0, 6)),
                'province_id' => intval(substr($aKey, 0, 2).'0000'),
                'city_id' => intval(substr($aKey, 0, 4).'00'),
                'name' => $area,
            ];
            $addressArea = new AddressArea();
            $addressArea->firstOrCreate($data);
        }
    }
}