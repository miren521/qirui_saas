<?php
// 事件定义文件
return [
	'bind' => [

	],

	'listen' => [
		//展示活动
		'ShowPromotion' => [
			'addon\pointexchange\event\ShowPromotion',
		],
		'PointexchangeOrderPayNotify' => [
			'addon\pointexchange\event\PointexchangeOrderPayNotify',
		],

		'MemberAccountFromType' => [
			'addon\pointexchange\event\MemberAccountFromType',
		],

		//默认广告位
		'InitAdv' => [
			'addon\seckill\event\InitAdv',
		],
	],

	'subscribe' => [
	],
];
