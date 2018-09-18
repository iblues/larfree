<?php
namespace App\Http\Controllers;

use App\Models\Common\CommonUser;
use Log;
use Illuminate\Http\Request;
use App\Models\User;
use Image;
class WeChatController extends Controller
{

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve(Request $request)
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');
        $app->server->push(function($message){
            return "欢迎关注 overtrue！";
        });
        return $app->server->serve();
    }

    public function pay(){
        $app = app('wechat.payment');
        $result = $app->order->unify([
            'body' => '测试用',
            'out_trade_no' => time(),
            'total_fee' => 1,
            'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'sign_type'=>'MD5',
            'openid' => 'o_LLy0J5OTc3pWoIHsR8c70CNShE',
        ]);

        $code = $app->jssdk->bridgeConfig($result['prepay_id'], false);
//        $code['_package'] = $code['package'];
        return response()->success($code,'',['cat'=>1]);
        echo json_encode($code);
    }

    public function getMiniQr(){
        $path = storage_path();
        $path = $path.'/qr';
        $app = app('wechat.mini_program');
        $url = '/pages/activity/content/index?id=1&inviter_id=1';
        $response  = $app->app_code->get($url);

        $filename = $response->save($path);
        $qr = $path.'/'.$filename;
        $bg =  storage_path().'/qr/bg.jpg';
        $this->watermark($qr,$bg);
    }

    function watermark($qr,$bg){
//        echo $bg;
//echo $qr;
//exit();
        $backGround = Image::make($bg)->resize(null,915);
        $qr = Image::make($qr)->resize(120, 120);;


        $last = storage_path().'/qr/'.time().'.jpg';
        $backGround->save($last);

        $backGround->insert($qr,'bottom-center',0,380);
        $backGround->save();
        echo $last;
    }



    public function weChatLogin(){
        $user = session('wechat.oauth_user'); // 拿到授权用户资料
        $User = new CommonUser();
        $openid = $user->original['openid'];//拿到授权用户的openid
        $nickname = $user->nickname;//拿到授权用户的昵称
        $thumb = $user->avatar;//拿到授权用户的头像
        $sex = $user->user;
        $user = $User->where('openid',$openid)->first();
        if(!$user){
            $user = $User->create(['openid'=>$openid,'avatar'=>$thumb,'name'=>$nickname,'sex'=>$sex]);
        }
        //换一个token
        $token = str_random(random_int(30,40));
        $user->api_token=$token;
        $user->save();
//        dd($user)

        $url = $_GET['url'];
        $ref = $_GET['ref'];
        //执行微信登录
        $token = $token;
        $url = $url."/token/".$token.'/url/'.urlencode($ref);
        return redirect($url);
    }
}