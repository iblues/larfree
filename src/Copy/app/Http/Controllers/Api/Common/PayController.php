<?php

namespace App\Http\Controllers\Api\Common;

use App\Models\Common\CommonPay;
use Illuminate\Http\Request;
use Larfree\Controllers\ApisController;
use Larfree\Libs\Payment\WechatPay;

//use Auth;

class PayController extends ApisController
{
    public function __construct(CommonPay $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    /**
     * 获取支付相关信息
     * @param int $id
     * @param Request $request
     * @return mixed
     */
    public function show($id,Request $request){
        $type = $request->type;
        $pay = $this->model->find($id);
        switch ($type) {
            case 'wechatpay':
            //当前只有微信支付
            default :
                $payment = new WechatPay();
                $code = $payment->pay($pay);
                break;
        }
        return $code;
    }

    /**
     * 支付成功的操作
     * 就把订单状态改为已支付
     * 并同时把purchasegoods表中的添加的数据状态改为已支付
     */
    public function notify(Request $request){
        $type = $request->type;
        switch ($type) {
            case 'wechatpay':
                $pay = new WechatPay();
                $return = $pay->notify($request);
                break;
        }
        return $return;
    }


    //前台禁止修改和编辑
    public function store(Request $request){}
    public function update(Request $request, $id){}

}