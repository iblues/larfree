<?php
/**
 * Larfree Api类
 * @author blues
 */
namespace App\Http\Controllers\Api\Test;
use App\Models\Test\TestTestDetail;
use Illuminate\Http\Request;
use Larfree\Controllers\ApisController as Controller;
class TestDetailController extends Controller
{
    public function __construct(TestTestDetail $model)
    {
        $this->model = $model;
        parent::__construct();
    }
}