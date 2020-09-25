<?php
// 事件定义文件
return [
	'bind' => [

	],

	'listen' => [
		//展示活动
		'ShowPromotion' => [
			'addon\turntable\event\ShowPromotion',
		],

		//关闭幸运抽奖
		'CloseTurntable' => [
			'addon\turntable\event\CloseTurntable',
		],

		//开启幸运抽奖
		'OpenTurntable' => [
			'addon\turntable\event\OpenTurntable',
		],

        'MemberAccountFromType' => [
            'addon\turntable\event\MemberAccountFromType',
        ],

	],

	'subscribe' => [
	],
];
