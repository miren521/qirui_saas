<?php
// 事件定义文件
return [
	'bind' => [
	
	],
	
	'listen' => [
		//展示活动
		'ShowPromotion' => [
			'addon\printer\event\ShowPromotion',
		],
		
		'PromotionType' => [
			'addon\printer\event\PromotionType',
		],

        //添加订单任务
        'OrderPay' => [
            'addon\printer\event\OrderPay',
        ],

        //订单打印
        'PrintOrder' => [
            'addon\printer\event\PrintOrder',
        ],

	],
	
	'subscribe' => [
	],
];
