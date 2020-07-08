<?php


namespace Larfree\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Passport\HasApiTokens;

/**
 * 带了laravel默认的auth验证的
 * Class ApiUser
 * @package Larfree\Models
 */
class ApiUser extends Api implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

//    /**
//     * The attributes that are mass assignable.
//     *
//     * @var array
//     */
//    protected $fillable = [
//        'name', 'phone', 'password','api_token'
//    ];
//
//    /**
//     * The attributes that should be hidden for arrays.
//     *
//     * @var array
//     */
    protected $hidden = [
        'password', 'remember_token',
    ];


}
