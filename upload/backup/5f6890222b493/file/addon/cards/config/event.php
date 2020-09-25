<?php
// 事件定义文件
return [
	'bind' => [

	],

	'listen' => [
		//展示活动
		'ShowPromotion' => [
			'addon\cards\event\ShowPromotion',
		],
		//关闭刮刮乐
		'CloseCards' => [
			'addon\cards\event\CloseCards',
		],

		//开启刮刮乐
		'OpenCards' => [
			'addon\cards\event\OpenCards',
		],

        'MemberAccountFromType' => [
            'addon\cards\event\MemberAccountFromType',
        ],

	],

	'subscribe' => [
	],
];
