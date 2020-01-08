<?php
/**
 * 没有任何逻辑的Model类
 * @author blues
 */
namespace Larfree\Models\Test;
use Larfree\Models\Admin\AdminNav;
use Larfree\Models\Api;
class TestTest extends Api
{
    public function users(){
        return $this->hasOne(AdminNav::class,'id','user_id');
    }
}
