<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        //展示活动
        'ShowPromotion' => [
            'addon\wholesale\event\ShowPromotion',
        ],
        
        'PromotionType' => [
            'addon\wholesale\event\PromotionType',
        ],
    ],

    'subscribe' => [
    ],
];
