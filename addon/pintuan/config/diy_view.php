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
			'name' => 'PINTUAN_LIST',
			'title' => '拼团',
			'type' => 'OTHER',
			'controller' => 'Pintuan',
			'value' => '{"backgroundColor":""}',
			'sort' => '10000',
			'support_diy_view' => '',
			'max_count' => 1
		]
	],
	'link' => [
		[
			'name' => 'PINTUAN_LIST',
			'title' => '拼团列表',
			'wap_url' => '/promotionpages/pintuan/list/list',
			'web_url' => ''
		],
	],
];