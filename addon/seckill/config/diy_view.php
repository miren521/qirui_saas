<?php
/**
 * KirySaaS--------||bai T o o Y ||
 * =========================================================
 * ----------------------------------------------
 * User Mack Qin
 * Copy right 2019-2029 kiry 保留所有权利。
 * ----------------------------------------------
 * =========================================================
 */

return [
	'template' => [
	],
	'util' => [
		[
			'name' => 'SECKILL_LIST',
			'title' => '秒杀',
			'type' => 'OTHER',
			'controller' => 'Seckill',
			'value' => '{"backgroundColor":""}',
			'sort' => '10000',
			'support_diy_view' => '',
			'max_count' => 1
		]
	],
	'link' => [
		[
			'name' => 'SECKILL_LIST',
			'title' => '秒杀列表',
			'wap_url' => '/promotionpages/seckill/list/list',
			'web_url' => ''
		],
	],
];