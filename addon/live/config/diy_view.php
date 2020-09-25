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
			'name' => 'WEAPP_LIVE',
			'title' => '小程序直播',
			'type' => 'OTHER',
			'controller' => 'LiveInfo',
			'value' => '{
                "paddingUpDown": 0,
                "isShowAnchorInfo": 1,
                "isShowLiveGood": 1
			}',
			'sort' => '10000',
			'support_diy_view' => '',
			'max_count' => 1
		]
	],
	'link' => [
        [
            'name' => 'LIVE_LIST',
            'title' => '直播',
            'wap_url' => '/otherpages/live/list/list',
            'web_url' => ''
        ],
	],
];