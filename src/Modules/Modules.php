<?php
namespace Larfree\Modules;
class  Modules  {
    static public function view($name,$field){
        return view('/admin/modules/'.$name,['field'=>$field]);
    }

}