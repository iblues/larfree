<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Admin\Api\Test;

use App\Models\Test\TestTest;
use Illuminate\Http\Request;
use Larfree\Controllers\ApisController as Controller;
class TestController extends Controller
{
    public function __construct(TestTest $model )
    {
        $this->model = $model;
        parent::__construct();
    }
}