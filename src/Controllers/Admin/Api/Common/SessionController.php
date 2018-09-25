<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 15:53
 */

namespace Larfree\Controllers\Admin\Api\Common;

use App\Models\Common\User;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * @param Request $request
     * 登录验证
     */
    public function store(Request $request){
        $phone = $request->phone;//手机号
        $password = $request->password;//密码
        $user = User::where('phone',$phone)->first();
        if($user){
            $pwdverify = password_verify($password,$user->password);
            if($pwdverify){
                if(!$user->api_token){
                    $user->api_token = str_random(32);
                    $user->save();
                }
                if($user){
                    return Response(['msg'=>'登录成功','data'=>$user]);
                }
            }else{
                apiError('密码错误,请重新登录',[],403);
            }
        }else {
            apiError('密码错误,请重新登录',[],403);
        }
    }


    public function show(){

    }

    public function index(){

    }

    public function update(){

    }
}