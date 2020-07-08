<?php
/**
 * 后台专用的控制器
 */

namespace Larfree\Controllers;

use Illuminate\Http\Request;

class AdminApisController extends ApisController
{


    public function index(Request $request)
    {
        return $this->service->link()->paginate($request->toArray(), $request->get('@columns'),
            $request->get('pageSize'));
    }

    public function import(Request $request, $module = 'import')
    {
        //批量导出
        $file = $request->post('file');
        if ($file) {
            return $this->service->link()->import($this->modelName, $module, $file);
        } else {
            apiError('file文件不存在');
        }
    }

    public function chart(Request $request, $module = 'chart.line.line')
    {
        return $this->service->link()->chart($module, $request->toArray());
    }

    public function export(Request $request, $module = 'export')
    {
        //批量导出
        return $this->service->link()->export($this->modelName, $module, $request->toArray());
    }

}
