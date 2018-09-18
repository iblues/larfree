<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/22/022
 * Time: 10:31
 */

namespace Larfree\Libs;
use App\Models\ApiDoc;
use Route;

class Swagger
{
    public function getRoutes($type){
        $routes = Route::getRoutes();
        $apiRoutes=[];
        foreach($routes as $route){
            if(substr($route->uri,0,4)=='api/'){
                //只要前端api
                if($type=='home' &&  @substr($route->uri,0,11) != 'api/manager'){
                    $apiRoutes[] = $route;
                }else {
//                    $apiRoutes[] = $route;
                }
            }
        }

        $apiRoutes = $this->PareseRoutes($apiRoutes);


    }

    public function PareseRoutes($apiRoutes){
        $data=[];
        foreach ($apiRoutes as $route){
            $url = $route->uri;
            $method = $route->methods[0];
            $as = @$route->action['controller'];
            $data[]  = ['as'=>$as,'method'=>$method,'url'=>$url];
        }
        print_r($data);
    }
    public function getAllJson($type="home"){
        $routes = $this->getRoutes($type);
        if($type=='home'){

        }
    }
}