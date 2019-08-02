<?php
/**
 * 现状图统计
 */
use Illuminate\Support\Facades\Crypt;
return function($data,$component,$target){


    if(!array_filter($data))
        apiError('Schemas not exits');
    $def = [
    ];

    $config = array_merges($def,$data);
    return $config;
};
