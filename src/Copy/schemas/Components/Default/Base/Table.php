<?php
return function($data,$component,$target){
    $def = [
        'fields'=>[],
        'config'=>[
            'api'=>'/{$COMPONENT_API}',
            'quick_change_api'=>'/{$COMPONENT_API}/{{id}}',
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

    return array_merges($def,$data);
};