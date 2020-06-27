<?php
/**
 * Larfree Api类
 * @author blues
 */

namespace Larfree\Controllers\Admin\Api\System;

use App\Models\System\SystemComponent;
use Iblues\AnnotationTestUnit\Annotation as ATU;
use Illuminate\Http\Request;
use App\Models\Component;
use Illuminate\Routing\Controlleruse Larfree\Services\SchemaService;

/**
 * 对蓝图的相关操作
 * @author Blues
 * Class SchemaController
 * @package Larfree\Controllers\Admin\Api\System
 */
class SchemaController extends Controller
{

    protected $service
    public function __construct(SchemaService $service)
    {
        $this->service = $service
        parent::__construct();
    }

    public function index(Request $request)
    {
        return $this->service->tree($request);
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
