<?php
return function($data,$component,$target) {
    $def= [
        'fields' => [],
        'class' => '',
        'style' => '',
        'config' => [
            'readApi' => '/{$COMPONENT_API}/{{id}}',
            'api' => '/{$COMPONENT_API}/{{id}}',
        ]
    ];
    return array_merges($def,$data);
};
