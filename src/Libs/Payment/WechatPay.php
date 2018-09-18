<?php
namespace Larfree\Libs\Payment;
use App\Models\Common\CommonPay;
use App\Models\Common\CommonUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Larfree\Libs\Payment\Pay;
class WechatPay implements Pay
{
    public $app;
    public function __construct()
    {
        $this->app = app('wechat.payment');
    }

    public function pay(CommonPay $Pay)
    {
        $user_id = getLoginUserID();
        $user = CommonUser::find($user_id);
        if(!$user->openid)
            throw \Exception('找不到用户的Openid');
        $result = $this->app->order->unify([
            'body' => $Pay->title,
            'out_trade_no' => time() . '-' . $Pay->id,//订单号
            'total_fee' => $Pay->price * 100,//价格
            'notify_url' => URL('api/common/pay/notify/wechatpay'), // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'sign_type' => 'MD5',
            'openid' => $user->openid,//当前用户的openid
        ]);
        $code = $this->app->jssdk->bridgeConfig($result['prepay_id'], false);
        if ($code) {
            return $code;
        }
    }

    public function refund(CommonPay $Pay)
    {
        // TODO: Implement refund() method.
    }
    public function find(CommonPay $Pay)
    {
        // TODO: Implement find() method.
    }


    /**
     * 完成支付的逻辑写在这里
     * @param $id
     */
    public function complete($id)
    {
        $pay = CommonPay::find($id);
        if($pay->status==1){
            return true;
        }
        DB::beginTransaction();
        try{
            $pay->status=1;
            $order = new $pay->model();
            $order->complete($pay->order_id);
            $pay->save();
            DB::commit();
        } catch (\Exception $e){
            DB::rollback();//事务回滚
//            echo $e->getMessage();
        }

    }

    public  function notify(Request $request)
    {
        //方便测试.记得删除
        if(@$request->blues_order_id) {
            $this->complete($request->blues_order_id);
            echo '测试用,直接支付成功';
            exit();
        }

//        file_put_contents(public_path().'/wx2.txt',serialize($request->all()));
        $response = $this->app->handlePaidNotify(function($message, $fail){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = $message['out_trade_no'];
            $rows = explode('-',$order);
            //$data = Exchange::find($rows[1]);
            if ($message['return_code'] === 'SUCCESS'){ // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    (new WechatPay())->complete($rows[1]);
                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
//                    $order->status = 'paid_fail';
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            return true; // 返回处理完成
        });
        $response->send(); // return $response;
    }

}