<?php
/**
 * 现状图统计
 */

return function ($data, $component, $target) {
    if (!array_filter($data)) {
        apiError('Schemas not exits');
    }
    $def = [
    ];

    $config = array_merges($def, $data);
    return $config;
};
