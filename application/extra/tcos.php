<?php
/**
 * Created by PhpStorm.
 * Tcos 配置 腾讯云cos
 * Date: 2018/6/24 0021
 * Time: 14:36
 */
return [
    // 测试配置
    'config' => [
        'bucket'    => 'cat' ,
        'app_id'    => '1254220117' ,
        'secret_id' => 'AKIDc7TdcQiTlurcBYLkYCfw2weDalkYuRBW'  ,
        'secret_key'=> 'Ni2KWdaMwK8oMsQzTRMAqT0jJ7kFak0A' ,
        'region'    => 'bj', // bucket所属地域：华北 'tj' 华东 'sh' 华南 'gz'
        'timeout'   => 60 ,
    ] ,
    // 线上配置
    'webconfig' => [
        'bucket'    => '51menke' ,
        'app_id'    => '1253417915' ,
        'secret_id' => 'AKIDoSs41kaSQ7YicSmMQDk00jCQN4UeAFem'  ,
        'secret_key'=> '0kEWYMwHOFqBF3cvynLfkcFdmgXCbleH' ,
        'region'    => 'gz', // bucket所属地域：华北 'tj' 华东 'sh' 华南 'gz'
        'timeout'   => 60 ,
    ] ,
];
