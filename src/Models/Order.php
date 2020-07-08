<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5/005
 * Time: 16:29
 */

namespace Larfree\Models;


use App\Models\Common\CommonPay;
use Illuminate\Database\Eloquent\Model;

abstract class Order extends Api
{

    /**
     *  修改订单状态,回调会调用这个接口
     * @param $id
     * @return string
     */
    abstract public function complete($id);

    public function addPay(Model $model, $price, $uid)
    {
        $Pay           = new CommonPay();
        $Pay->model    = get_class($model);
        $Pay->order_id = $model->id;
        $Pay->user_id  = $uid;
        $Pay->title    = '订单id'.$model->id;
        $Pay->price    = $price;
        $Pay->status   = 0;
        $Pay->save();
        return $Pay;

//        $table->integer('pay_type')->comment('支付方式');
//        $table->string('pay_id')->comment('流水号');//支付成功后才有
    }

}