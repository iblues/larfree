<?php

namespace Larfree\Models;

use Illuminate\Database\Eloquent\Model;
use Larfree\Models\Traits\AdvWhere;
use Larfree\Models\Traits\Base;
use Larfree\Models\Traits\Chart;
use Watson\Rememberable\Rememberable;

class Api extends Model
{
    use Base, Chart, Rememberable;

    protected $guarded = [];

    // 日志相关
//    protected $revisionEnabled = true; //是否开启日志记录
//    protected $dontKeepRevisionOf = ['deleted_at', 'created_at', 'updated_at'];//这3个字段不写日志
//    protected $revisionCreationsEnabled = false;//创建是否监控


}
