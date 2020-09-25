<?php
// 事件定义文件
return [
    'bind'      => [ 
    ],

    'listen'    => [

        //展示活动
        'ShowPromotion' => [
            'addon\platformcoupon\event\ShowPromotion',
        ],
        //优惠券自动关闭
        'CronPlatformcouponEnd' => [
            'addon\platformcoupon\event\CronPlatformcouponEnd',
        ],
    	// 优惠券活动定时结束
    	'CronPlatformcouponTypeEnd' => [
    		'addon\platformcoupon\event\CronPlatformcouponTypeEnd',
    	],
        'OrderClose' => [
            'addon\platformcoupon\event\OrderClose',
        ]
    ],

    'subscribe' => [
    ],
];
