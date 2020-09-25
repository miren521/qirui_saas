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
			'name' => 'FENXIAO_GOODS_LIST',
			'title' => '分销商品',
			'type' => 'OTHER',
			'controller' => 'FenxiaoGoodsList',
			'value' => '{}',
			'sort' => '10000',
			'support_diy_view' => 'DIY_FENXIAO_MARKET',
			'max_count' => 1
		]
	],
	'link' => [
		[
			'name' => 'FENXIAO_GOODS_LIST',
			'title' => '分销商品列表',
			'wap_url' => '/otherpages/fenxiao/goods_list/goods_list',
			'web_url' => '',
			'support_diy_view' => 'DIY_VIEW_INDEX',
		],
	],
];