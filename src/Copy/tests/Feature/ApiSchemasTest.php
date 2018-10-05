<?php

namespace Tests\Feature;


use Tests\TestCase;
use Larfree\Libs\ApiSchemas;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiSchemasTest extends TestCase
{
    public $in=[
        'index'=>[
            'datetime'=>[
                'name'=>'日期',
                'rule'=>['required','date'],
            ],
            'title4'=>[
                'rule'=>['required'],
            ],
        ],
        'update'=>[
            '*',
            'datetime'=>[
                'name'=>'日期',
                'rule'=>['required','date'],
            ],
            'select'=>[
                'rule'=>['required'],
            ],
            'title3'=>[
                'name'=>'额外参数',
                'rule'=>['required'],
            ],
        ],
        'store'=>[
            '*',
            'datetime'=>[
                'name'=>'日期2',
                'rule'=>['required','date'],
            ],
            'select'=>[
                'rule'=>['required'],
            ],
            'title3'=>false,
        ],
        'only'=>[
            'title'=>[
                'rule'=>'',//不要title
            ],
            'select'=>[
                'rule'=>['required'],
            ],
        ]
    ];

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
//        $_ENV['ADMIN']=true;
//        $validate = ApiSchemas::getValidate( 'test' ,'store',[]);
//print_r($validate);
//echo 123;
        $this->assertTrue(true);
    }


    /**
     * 验证test中的store有没有正常
     */
    public function testStoreValidate(){
        $method = 'store';
        $validate = ApiSchemas::getValidate( 'test.test' ,'in',$this->in[$method]);

        $this->assertArrayHasKey('select',$validate['rules']);
        $this->assertArrayHasKey('title',$validate['rules']);
        $this->assertArrayNotHasKey('created_at',$validate['rules'],'不应该有created_at,除非created_at定义了规则');
    }

    /**
     * 验证test中的store有没有正常
     */
    public function testUpdateValidate(){
        $method = 'update';
        $validate = ApiSchemas::getValidate( 'test.test' ,'in',$this->in[$method]);
        $this->assertArrayHasKey('select',$validate['rules']);
        $this->assertArrayHasKey('title3',$validate['rules']);
        $this->assertEquals($validate['rules']['title3'],'required','提取的规则不正确');
        $this->assertArrayNotHasKey('created_at',$validate['rules']);
    }

    /**
     * 测试限定字段
     */
    public function testIndexValidate(){
        $method = 'index';
        $validate = ApiSchemas::getValidate( 'test.test','in',$this->in[$method]);

        //自定义的字段应该有
        $this->assertArrayHasKey('title4',$validate['rules']);
        $this->assertArrayNotHasKey('title',$validate['rules'],'title已经排除,不应该存在了');
    }

    /**
     * show 没有定义api和in变量.应该没有值
     */
    public function testShowValidate(){
        $method = 'show';
        $validate = ApiSchemas::getValidate( 'test.test','in',@$this->in[$method]);
        $this->assertNotEmpty($validate['rules'],'这里就读取所有的主结构');
    }

    /**
     * show 没有定义api和in变量.应该没有值
     */
    public function testOnlyValidate(){
        $method = 'only';
        $validate = ApiSchemas::getValidate( 'test.test','in',@$this->in[$method]);
        $this->assertArrayHasKey('select',$validate['rules']);
        $this->assertArrayNotHasKey('title',$validate['rules'],'title的rule为空,不应该验证');
    }

    /**
     * 测试如果不存在配置的时候,直接用定义的 有没有问题
     */
    public function testNoModelStoreValidate(){
        $method = 'store';
        $validate = ApiSchemas::getValidate( 'test222','in',@$this->in[$method]);
        $rules = array_keys($validate['rules']);
        $this->assertArraySubset($rules,['datetime','select'],true,'应该只有datetime和select存在');
    }



    /**
     * 验证test中的store有没有正常
     */
    public function testOnlyAllowedField(){
        $method = 'only';
        $allow = ApiSchemas::getApiAllowField( 'test.test' ,'in',$method,$this->in[$method]);
//        dump($allow);
        $this->assertArrayHasKey('select',$allow);
        $this->assertArrayHasKey('title',$allow);
        $this->assertArrayNotHasKey('created_at',$allow,'不应该有created_at,除非created_at定义了规则');
    }

    /**
     * show没有定义,则默认可以返回所有变量
     */
    public function testShowAllowedField(){
        $method = 'show';
        $allow = ApiSchemas::getApiAllowField( 'test.test' ,'in',$method,@$this->in[$method]);
        $this->assertEquals($allow,false,'应该为*,允许所有的字段');
    }

    /**
     * 验证test中的store有没有正常
     */
    public function testIndexAllowedField(){
        $method = 'index';
        $allow = ApiSchemas::getApiAllowField( 'test.test' ,'in',$method,$this->in[$method]);
        $this->assertArrayHasKey('datetime',$allow);
        $this->assertArrayHasKey('title4',$allow);
        $this->assertArrayNotHasKey('title',$allow,'不应该有title');
    }


    /**
     * 验证test中的store有没有正常
     */
    public function testUpdateAllowedField(){
        $method = 'update';
        $allow = ApiSchemas::getApiAllowField( 'test.test' ,'in',$method,$this->in[$method]);
        $this->assertArrayHasKey('title',$allow);
        $this->assertArrayHasKey('title3',$allow);
    }



}
