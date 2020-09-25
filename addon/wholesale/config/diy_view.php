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
            'name' => 'WHOLESALE_LIST',
            'title' => '批发',
            'type' => 'OTHER',
            'controller' => 'Wholesale',
            'value' => '{}',
            'sort' => '10000',
            'support_diy_view' => '',
            'max_count' => 1
        ]
    ],
	'link' => [
		[
			'name' => 'WHOLESALE_LIST',
			'title' => '批发列表',
			'wap_url' => '/promotionpages/wholesale/list/list',
			'web_url' => ''
		],
	],
];