<?php

namespace App\Models\Admin;

use App\Scopes\Admin\AdminNavScope;
use Larfree\Models\Admin\AdminNav as Nav;
use DB;//载入DB类
class AdminNav extends Nav
{
    use AdminNavScope;
}
