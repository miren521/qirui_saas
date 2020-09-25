<?php
// 事件定义文件
return [
	'bind' => [

	],

	'listen' => [
		//展示活动
		'ShowPromotion' => [
			'addon\pintuan\event\ShowPromotion',
		],
		//订单支付事件
		'OrderPay' => [
			'addon\pintuan\event\OrderPay',
		],
		'PromotionType' => [
			'addon\pintuan\event\PromotionType',
		],
		//开启拼团活动
		'OpenPintuan' => [
			'addon\pintuan\event\OpenPintuan',
		],
		//关闭拼团活动
		'ClosePintuan' => [
			'addon\pintuan\event\ClosePintuan',
		],
		//关闭拼团组
		'ClosePintuanGroup' => [
			'addon\pintuan\event\ClosePintuanGroup',
		],

		//默认广告位
		'InitAdv' => [
			'addon\pintuan\event\InitAdv',
		],
        // 商品列表
        'GoodsListPromotion' => [
            'addon\pintuan\event\GoodsListPromotion',
        ],
	],

	'subscribe' => [
	],
];
