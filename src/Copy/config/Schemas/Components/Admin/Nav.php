<?php
/**
 * 其他可以用组建默认的参数
 * 也可以自己指定
 */
return [
    'detail'=>[
        'table'=>[
            'fields'=>[
                'id',
            'catid',
            'name',
            'url',
            'class',
//            'module',
            'parent_id',
            'ranking',
            'status',
//            'created_at',
//            'updated_at'
             ],
        ],
        'add'=>[
            'fields'=>[
                'catid',
            'name',
            'url',
            'class',
            'module',
            'parent_id',
            'ranking',
            'status'
             ],
        ],
        'edit'=>[
            'fields'=>[
                'catid',
            'name',
            'url',
            'class',
            'module',
            'parent_id',
            'ranking',
            'status'
            ],
        ],
        'detail'=>[
            'fields'=>[
                'id',
            'catid',
            'name',
            'url',
            'class',
            'module',
            'parent_id',
            'ranking',
            'status',
            'created_at',
            'updated_at'
            ],
        ],
    ],
];