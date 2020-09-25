<?php
// 事件定义文件
return [
	'bind' => [
	
	],
	
	'listen' => [
		//展示活动
		'ShowPromotion' => [
			'addon\electronicsheet\event\ShowPromotion',
		],
		
		'PromotionType' => [
			'addon\electronicsheet\event\PromotionType',
		],

	],
	
	'subscribe' => [
	],
];
