<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/22
 * Time: 15:53
 */

namespace Larfree\Controllers\Admin\Api\Common;

use App\Models\Common\User;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Larfree\Controllers\AdminApisController;

class SessionController extends AdminApisController
{
//    /**
//     * @param Request $request
//     * 登录验证
//     */
//    public function store(Request $request)
//    {
//        $phone = $request->phone;//手机号
//        $password = $request->password;//密码
//        $user = User::where('phone', $phone)->first();
//        if ($user) {
//            $pwdverify = password_verify($password, $user->password);
//            if ($pwdverify) {
//                if (!$user->api_token) {
//                    $user->api_token = str_random(32);
//                    $user->save();
//                }
//                if ($user) {
//                    return Response(['msg' => '登录成功', 'data' => $user]);
//                }
//            } else {
//                apiError('密码错误,请重新登录', [], 403);
//            }
//        } else {
//            apiError('密码错误,请重新登录', [], 403);
//        }
//    }

    public $in = [
        'store' => [
            'phone' => [
                'rule' => 'required|min:11|max:11'
            ],
            'password' => [
                'rule' => 'required'
            ]
        ],
    ];

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @OA\Post(
     *   summary="用户登录",
     *   description="用户登录成功返回登录用户的数据",
     *   path="/user/session",
     *   tags={"用户相关"},
     *   @OA\Response(
     *     response="200",
     *     description="登录成功 md5:007e2d630723fb37e4afe441c3f84315",
     *   ),
     * )
     */
    public function store(Request $request)
    {

        $User = new CommonUser();

        $user = $User->where('phone', $request->phone)
            ->first();

        if (!$user) {
            apiError('该手机未注册');
        }

        try {
            if (!$token = JWTAuth::attempt($request)) {
                apiError("账号或者密码错误");
            }
        } catch (JWTException $e) {
            apiError("token 无法生成");
        }

        $user->api_token = $token;

        return Response()->success($user, '登录成功');
    }


    public function show()
    {

    }

    public function index()
    {

    }

    public function update()
    {

    }
}
