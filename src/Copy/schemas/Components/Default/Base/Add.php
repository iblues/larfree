<?php
return function($data,$component,$target) {
    $def = [
        'fields' => [],
        'class' => '',
        'style' => '',
        'config' => [
            'api' => '/{$COMPONENT_API}',
        ]
    ];
    return array_merges($def,$data);
};