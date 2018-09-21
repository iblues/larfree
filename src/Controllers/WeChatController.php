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

    /**
     * 微信登录相关接口
     * @return mixed
     * @throws \Exception
     */
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