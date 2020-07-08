<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/5
 * Time: 21:15
 */

namespace Larfree\Libs;

use Route;

class Routes
{
    /**
     * @param  string  $type
     * @return array
     * @author Blues
     */
    static public function getRoutes($type = '')
    {
        $routes = Route::getRoutes();
        dump($routes);
        $apiRoutes = [];
        foreach ($routes as $route) {
            if (substr($route->uri, 0, 4) == 'api/') {
                //只要前端api
                if ($type == 'home' && @substr($route->uri, 0, 11) != 'api/manager') {
                    $apiRoutes[] = $route;
                } else {
//                    $apiRoutes[] = $route;
                }
            }
        }

        return $apiRoutes = self::PareseRoutes($apiRoutes);
    }

    static public function PareseRoutes($apiRoutes)
    {
        $data = [];
        foreach ($apiRoutes as $route) {
            $url    = $route->uri;
            $method = $route->methods[0];
            $as     = @$route->action['controller'];
            $data[] = ['as' => $as, 'method' => $method, 'url' => $url];
        }
        return $data;
    }


}
