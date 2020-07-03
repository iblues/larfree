<?php
/**
 * Larfree Api类
 * @author blues
 */

namespace Larfree\Controllers\Admin\Api\System;

use App\Models\System\SystemComponent;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Larfree\Services\SchemaService;

/**
 * 对蓝图的相关操作
 * @author Blues
 * Class SchemaController
 * @package Larfree\Controllers\Admin\Api\System
 */
class SchemaController extends Controller
{

    protected $service;
    public function __construct(SchemaService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->service->getList($request->toArray());
    }

    public function store(Request $request)
    {
    }

    public function update($id, Request $request)
    {
    }

    public function delete($id)
    {
    }

}
