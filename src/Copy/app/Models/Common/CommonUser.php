<?php
/**
 * 没有任何逻辑的Model类
 * @author blues
 */
namespace App\Models\Common;
use Larfree\Models\Api;
class CommonUser extends Api
{
    use CommonUserScope;
    public function setPasswordAttribute($value)
    {
        if($value)
            $this->attributes['password'] = password_hash($value,PASSWORD_DEFAULT);
        else
            unset($this->attributes['password']);
    }
}