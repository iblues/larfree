<?php
return function($data,$component,$target) {
    $def= [
        'fields' => [],
        'class' => '',
        'style' => '',
        'config' => [
            'readApi' => 'GET:///{$COMPONENT_API}/{{id}}',
            'api' => 'PUT:///{$COMPONENT_API}/{{id}}',
        ]
    ];
    return array_merges($def,$data);
};
