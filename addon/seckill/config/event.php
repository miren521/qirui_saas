<?php
// 事件定义文件
return [
	'bind' => [

	],

	'listen' => [
		//秒杀开启
		'OpenSeckill' => [
			'addon\seckill\event\OpenSeckill',
		],
		//秒杀关闭
		'CloseSeckill' => [
			'addon\seckill\event\CloseSeckill',
		],
		//展示活动
		'ShowPromotion' => [
			'addon\seckill\event\ShowPromotion',
		],
		'PromotionType' => [
			'addon\seckill\event\PromotionType',
		],
		//PC默认导航
		'InitPcNav' => [
			'addon\seckill\event\InitPcNav',
		],

		//默认广告位
		'InitAdv' => [
			'addon\seckill\event\InitAdv',
		],
	],

	'subscribe' => [
	],
];
