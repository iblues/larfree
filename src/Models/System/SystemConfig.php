<?php
/**
 * 没有任何逻辑的Model类
 * @author blues
 */

namespace Larfree\Models\System;

use Larfree\Models\Api;

class SystemConfig extends Api
{
    //
    protected $table = "system_config";
    protected $casts = ['value' => 'array'];
}