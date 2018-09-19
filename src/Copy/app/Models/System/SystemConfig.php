<?php
/**
 * 没有任何逻辑的Model类
 * @author blues
 */
namespace App\Models\System;
use Larfree\Models\Api;
class SystemConfig extends Api
{
    //
    protected $table = "config";
    protected $casts = ['value'=>'array'];
}