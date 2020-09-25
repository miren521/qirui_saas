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
			'name' => 'BARGAIN_LIST',
			'title' => '砍价',
			'type' => 'OTHER',
			'controller' => 'Bargain',
            'value' => '{"sources" : "default", "categoryId" : 0, "goodsCount" : "6", "goodsId": [], "style": 1, "backgroundColor": "", "padding": 10, "list": {"imageUrl": "","title": "砍价专区"}, "listMore": {"imageUrl": "","title": "更多"}, "titleTextColor": "#000", "defaultTitleTextColor": "#000", "moreTextColor": "#858585", "defaultMoreTextColor": "#858585"}',
			'sort' => '10000',
			'support_diy_view' => '',
			'max_count' => 1
		]
	],
	'link' => [
        [
            'name' => 'BARGAIN',
            'title' => '砍价',
            'parent' => 'MARKETING_LINK',
            'wap_url' => '/promotionpages/bargain/list/list',
            'web_url' => '',
            'sort' => 0,
            'child_list' => [
                [
                    'name' => 'BARGAIN_PREFECTURE',
                    'title' => '砍价专区',
                    'parent' => '',
                    'wap_url' => '/promotionpages/bargain/list/list',
                    'web_url' => '',
                    'sort' => 0
                ],
                [
                    'name' => 'MY_BARGAIN',
                    'title' => '我的砍价',
                    'parent' => '',
                    'wap_url' => '/bargain/my_bargain/my_bargain',
                    'web_url' => '',
                    'sort' => 0
                ],
            ]
        ]
	],
];