<?php

namespace Tests\Feature;


use Larfree\Models\Api;
use Tests\TestCase;


class ComponentApiTest extends TestCase {
    /**
     * 用于测试高级url的功能.
     * 详情见doc/url.md
     * @return void
     */

    /**
     * name|id$ = 20
     */
    public function testApi(){
        $dir = self::dirToArray(base_path('/config/Schemas/Schemas'));
        foreach ($dir as $key=>$value){
            foreach ($value as $v){
                $name = explode('.',$v);
                $url = '/api/admin/system/component/'.strtolower($key).'.'.lcfirst($name[0]).'/base.table';
                $response = $this->json('GET', $url);
                dump($url);
                $response
                    ->assertStatus(200)
                    ->assertJson([
                        'code' => true,
                    ]);
            }
        }
    }

    static function dirToArray($dir) {
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array(".",".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                }
                else {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }


}
