<?php
//根据配置自动创建路由
$Schemas = Larfree\Libs\Schemas::getAllSchemas();
foreach($Schemas as  $dirname => $dir){
   foreach ($dir as $file){
       $file = basename($file,'.php');
       Route::resource(
           humpToLine($dirname).'/'.humpToLine($file),
           $path.$dirname.'\\'.$file.'Controller'
       );
   }
}

