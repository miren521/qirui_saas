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
			'name' => 'GROUPBUY_LIST',
			'title' => '团购',
			'type' => 'OTHER',
			'controller' => 'Groupbuy',
			'value' => '{"backgroundColor": ""}',
			'sort' => '10000',
			'support_diy_view' => '',
			'max_count' => 1
		]
	],
	'link' => [
		[
			'name' => 'GROUPBUY_LIST',
			'title' => '团购列表',
			'wap_url' => '/promotionpages/groupbuy/list/list',
			'web_url' => ''
		],
	],
];