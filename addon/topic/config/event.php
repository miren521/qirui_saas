<?php
// 事件定义文件
return [
	'bind' => [

	],

	'listen' => [
		//专题活动开启事件
		'OpenTopic' => [
			'addon\topic\event\OpenTopic',
		],
		//专题活动关闭事件
		'CloseTopic' => [
			'addon\topic\event\CloseTopic',
		],
		//展示活动
		'ShowPromotion' => [
			'addon\topic\event\ShowPromotion',
		],
		//用于订单
		'PromotionType' => [
			'addon\topic\event\PromotionType',
		],

		//默认广告位
		'InitAdv' => [
			'addon\topic\event\InitAdv',
		],
	],

	'subscribe' => [
	],
];
