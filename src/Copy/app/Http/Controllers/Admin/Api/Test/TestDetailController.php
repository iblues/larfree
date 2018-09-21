<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Admin\Api\Test;

use App\Models\Test\TestTestDetail;
use Illuminate\Http\Request;
use Larfree\Controllers\AdminApiController as Controller;
use App\Models\Test\TestDetail;
class TestDetailController extends Controller
{
    public function __construct(TestTestDetail $model )
    {
        $this->model = $model;
        parent::__construct();
    }
}