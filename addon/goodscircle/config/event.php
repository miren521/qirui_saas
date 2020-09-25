<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        //展示活动
        'ShowPromotion' => [
            'addon\goodscircle\event\ShowPromotion',
        ],
        'OrderPay' => [
            'addon\goodscircle\event\OrderPay',
        ],
        'WeappMenu' => [
            'addon\goodscircle\event\WeappMenu',
        ]
    ],

    'subscribe' => [
    ],
];
