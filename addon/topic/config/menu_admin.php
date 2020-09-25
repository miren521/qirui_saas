<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
	[
		'name' => 'PROMOTION_TOPIC',
		'title' => '专题活动',
		'url' => 'topic://admin/topic/lists',
		'parent' => 'PROMOTION_PLATFORM',
		'is_show' => 0,
		'is_control' => 0,
		'is_icon' => 0,
		'picture' => '',
		'picture_select' => '',
		'sort' => 100,
		'child_list' => [
			[
				'name' => 'PROMOTION_TOPIC_LIST',
				'title' => '活动管理',
				'url' => 'topic://admin/topic/lists',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'picture' => '',
				'picture_select' => '',
				'sort' => 100,
			],
			[
				'name' => 'PROMOTION_TOPIC_GOODS_LIST',
				'title' => '商品管理',
				'url' => 'topic://admin/topic/goodslist',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'picture' => '',
				'picture_select' => '',
				'sort' => 101,
			],
			[
				'name' => 'PROMOTION_TOPIC_ADD',
				'title' => '添加活动',
				'url' => 'topic://admin/topic/add',
				'sort' => 1,
				'is_show' => 0
			],
			[
				'name' => 'PROMOTION_TOPIC_EDIT',
				'title' => '编辑活动',
				'url' => 'topic://admin/topic/edit',
				'sort' => 1,
				'is_show' => 0
			],
			[
				'name' => 'PROMOTION_TOPIC_DELETE',
				'title' => '删除活动',
				'url' => 'topic://admin/topic/delete',
				'sort' => 1,
				'is_show' => 0
			],
			[
				'name' => 'PROMOTION_TOPIC_CLOSE',
				'title' => '关闭活动',
				'url' => 'topic://admin/topic/close',
				'sort' => 1,
				'is_show' => 0
			],
			[
				'name' => 'PROMOTION_TOPIC_GOODS',
				'title' => '活动商品管理',
				'url' => 'topic://admin/topic/goods',
				'sort' => 1,
				'is_show' => 0
			],
			[
				'name' => 'PROMOTION_TOPIC_GOODS_DELETE',
				'title' => '删除活动商品',
				'url' => 'topic://admin/topic/deletetopicgoods',
				'sort' => 1,
				'is_show' => 0
			],
		]
	],
];
