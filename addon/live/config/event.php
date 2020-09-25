<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        //展示活动
        'ShowPromotion' => [
            'addon\live\event\ShowPromotion',
        ],
        'WeappMenu' => [
            'addon\live\event\WeappMenu',
        ],
        // 轮询更新直播商品状态
        'LiveGoodsStatus' => [
            'addon\live\event\LiveGoodsStatus',
        ],
        // 轮询更新直播间状态
        'LiveRoomStatus' => [
            'addon\live\event\LiveRoomStatus',
        ]
    ],

    'subscribe' => [
    ],
];
