<?php
return function ($data, $component, $target) {
    $def = [
        'fields' => [],
        'chart' => [
            'title'=>'统计模块',
            'component' => [
                'chart.line.new' => [
                    'title' => '标题1'
                ],
                'chart.pie.type' => [
                    'title' => '标题2',
                ]
            ]
        ],
        'search' => [],
        'config' => [
            'api' => 'GET:///{$COMPONENT_API}',
            'quick_change_api' => 'PUT:///{$COMPONENT_API}/{{id}}',
            'button' => [
                'add' => [
                    'type' => 'primary',
                    'html' => '添加',
                    'action' => 'add',
                    'url' => '/dialog/edit/{$COMPONENT}',
                    'api' => 'POST:///{$COMPONENT_API}',
                ],
            ],
            'action' => [
                'edit' => [
                    'type' => 'primary',
                    'html' => '编辑',
                    'action' => '/',
                    'url' => '/dialog/edit/{$COMPONENT}/{{id}}',
                    'api' => 'PUT:///{$COMPONENT_API}',
                ],
                'del' => [
                    'type' => 'danger',
                    'html' => '删除',
                    'action' => 'delRows',
                    'api' => 'DELETE:///{$COMPONENT_API}/{{id}}',
                ],
            ]
        ],
        'html' => ''
    ];
    if(!is_array($data)){
        apiError($target.'不存在',null,404);
    }
    return array_merges($def, $data);
};
