#开启微信支付
##打开config.wechat.php
检查payment是否解除注释

##调用
    $app = app('wechat.payment');
    $result = $app->order->unify([
        'body' => '测试用',
        'out_trade_no' => time(),
        'total_fee' => 1,
        'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
        'trade_type' => 'JSAPI',
        'openid' => 'o_LLy0J5OTc3pWoIHsR8c70CNShE',
    ]);
    dump($result);

##回调



#开启微信登录
##web登录法
路由
Route::group(['middleware' =>['web', 'wechat.oauth:snsapi_userinfo']], function () {
   Route::any('/wechatLogin','WeChatController@weChatLogin');
});

控制器
public function weChatLogin(Request $request){
    $user = session('wechat.oauth_user'); // 拿到授权用户资料
}