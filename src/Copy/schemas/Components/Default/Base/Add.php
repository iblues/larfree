<?php
return function($data,$component,$target) {
    $def = [
        'fields' => [],
        'class' => '',
        'style' => '',
        'config' => [
            'api' => 'POST:///{$COMPONENT_API}',
        ]
    ];
    return array_merges($def,$data);
};
