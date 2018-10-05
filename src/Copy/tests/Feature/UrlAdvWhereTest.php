<?php

namespace Tests\Feature;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Larfree\Models\Api;
use Tests\TestCase;


class UrlAdvWhereTest extends TestCase {
    /**
     * 用于测试高级url的功能.
     * 详情见doc/url.md
     * @return void
     */
//    public function testExample() {
//        $ctrl = \App::make(\App\Http\Controllers\AaaController::class);
//        \App::call([$ctrl, "aaa"]);

//        DB::connection()->enableQueryLog(); // 开启查询日志
//        TestController

//        print_r($queries); // 即可查看执行的sql，执行的时间，传入的参数等等
//        dump($return);
//    }

    /**
     * name|id$ = 20
     */
    public function testMutilField(){
        $query = ['name|id$'=>20];
        $sql = $this->do($query);
        $this->assertContains('(`name` = ?) or (`id` = ?)',$sql);
    }

    public function testLike(){
        $query = ['name$'=>'%test%'];
        $sql = $this->do($query);
        $this->assertContains('`name` like ?',$sql);
    }

    public function testBetween(){
        $query = ['id$'=>'>1,<3'];
        $sql = $this->do($query);
        $this->assertContains('`id` > ? and `id` <',$sql);
    }

    public function testEqBetween(){
        $query = ['id$'=>'>=1,<=3'];
        $sql = $this->do($query);
        $this->assertContains('`id` >= ? and `id` <=',$sql);
    }
    public function testIn(){
        $query = ['id$'=>'[1,2,3]'];
        $sql = $this->do($query);
        $this->assertContains('`id` in',$sql);
    }

    public function testEqBetweenOr(){
        $query = ['id$'=>'>200 | <100'];
        $sql = $this->do($query);
        $this->assertContains('(`id` > ?)) or (`id` = ?)',$sql);
    }


    protected function do($query){
        $model = new Api();
        foreach($query as $key=>$value){
            $model = $model->AdvWhere($key, $value);
        }
        return $model->toSql();
    }

}
