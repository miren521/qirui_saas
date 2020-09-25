<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        //展示活动
        'ShowPromotion' => [
            'addon\fenxiao\event\ShowPromotion',
        ],
        'OrderComplete' => [
            'addon\fenxiao\event\OrderSettlement',
        ],
        'OrderRefundFinish' => [
            'addon\fenxiao\event\OrderGoodsRefund',
        ],
        'OrderPay' => [
            'addon\fenxiao\event\OrderPay',
        ],

        'MemberAccountFromType' => [
            'addon\fenxiao\event\MemberAccountFromType',
        ],
    		
    	'MemberRegister' => [
    		'addon\fenxiao\event\MemberRegister',
    	],
    	'FenxiaoUpgrade' => [
    		'addon\fenxiao\event\FenxiaoUpgrade',
    	],
        // 商品列表
        'GoodsListPromotion' => [
            'addon\fenxiao\event\GoodsListPromotion',
        ],
    ],

    'subscribe' => [
    ],
];
