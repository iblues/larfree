<?php
namespace Larfree\Libs\Payment;


use App\Models\Common\CommonPay;
use Illuminate\Http\Request;
interface Pay
{
    /**
     * 支付相关调用
     * @param CommonPay $Pay
     * @return mixed
     */
    public function pay(CommonPay $Pay);

    /**
     * 远程调用
     * @param Request $request
     * @return mixed
     */
    public function notify(Request $request);

    /**
     * 完成支付
     * @param $id
     * @return mixed
     */
    public function complete($id);

    /**
     * 退款
     * @param CommonPay $Pay
     * @return mixed
     */
    public function refund(CommonPay $Pay);

    /**
     * 查询远程平台.检查什么情况
     * @param CommonPay $Pay
     * @return mixed
     */
    public function find(CommonPay $Pay);
}
