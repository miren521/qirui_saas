<?php
// 事件定义文件
return [
	'bind' => [
	],
	
	'listen' => [
		
		//展示活动
		'ShowPromotion' => [
			'addon\bundling\event\ShowPromotion',
		],
		'PromotionType' => [
			'addon\bundling\event\PromotionType',
		],
	],
	
	'subscribe' => [
	],
];
