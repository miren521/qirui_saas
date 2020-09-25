<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        //展示活动
        'ShowPromotion' => [
            'addon\present\event\ShowPromotion',
        ],
        
        'PromotionType' => [
            'addon\present\event\PromotionType',
        ],

        //关闭赠品
        'ClosePresent' => [
            'addon\present\event\ClosePresent',
        ],

        //开启赠品
        'OpenPresent' => [
            'addon\present\event\OpenPresent',
        ],

        // 商品列表
        'GoodsListPromotion' => [
            'addon\present\event\GoodsListPromotion',
        ],
        //同步库存
        'SyncStock' => [
            'addon\present\event\SyncStock',
        ]
    ],

    'subscribe' => [
    ],
];
