<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FunctionTest extends TestCase
{
    /**
     * array_merges函数的测试
     * 用于多维数组的合并
     * @return void
     */
    public function testArrayMerges()
    {
        $def = [
            'fields'=>[],
            'config'=>[
                'api'=>'/{$COMPONENT_API}',
                'button'=>[
                    'add'=>[
                        'type'=>'primary',
                        'html'=>'添加',
                        'action'=>'add',
                        'url'=>'edit/{$COMPONENT}'
                    ],
                ],
                'action'=>[
                    'edit'=>[
                        'type'=>'primary',
                        'html'=>'编辑',
                        'action'=>'/',
                        'url'=>'edit/{$COMPONENT}/{{id}}',
                    ],
                    'del'=>[
                        'type'=>'danger',
                        'html'=>'删除',
                        'action'=>'delRows',
                        'api'=>'/{$COMPONENT_API}/{{id}}',
                    ],
                ]
            ],
            'html'=>''
        ];
        $config = [
            'fields'=>[
                'id'=>123,
            ],
            'config'=>[
                'api'=>'/{$COMPONENT_API}',
                'button'=>[
                    'add'=>[
                        'html'=>'新添加',
                    ],
                    'refresh'=>[
                        'html'=>'刷新',
                    ]
                ],
                'action'=>[
                    'edit'=>null,
                    'edit2'=>[
                        'type'=>'primary',
                        'html'=>'编辑',
                        'action'=>'/',
                        'url'=>'edit/{$COMPONENT}/{{id}}',
                    ],
                ]
            ],
            'html'=>'123'
        ];
        $last = array_merges($def,$config);
//        dump($last);
        $this->assertEquals('123',@$last['fields']['id'],'fields.id应该存在并等于123');
        $this->assertArrayNotHasKey('edit',$last['config']['action'],'config.edit应该不存在了');
        $this->assertArrayHasKey('edit2',$last['config']['action'],'config.edit2应该存在');
        $this->assertArrayHasKey('del',$last['config']['action'],'config.del应该存在');
        $this->assertEquals('新添加',@$last['config']['button']['add']['html'],'config.button.html应该存在并等于新添加');
        $this->assertEquals('刷新',@$last['config']['button']['refresh']['html'],'config.button.html应该存在并等于添加22');

    }


}
