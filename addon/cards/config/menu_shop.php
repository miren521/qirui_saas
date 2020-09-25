<?php
// +----------------------------------------------------------------------
// | 店铺端菜单设置
// +----------------------------------------------------------------------
return [
	[
		'name' => 'PROMOTION_CARDS',
		'title' => '刮刮乐',
		'url' => 'cards://shop/cards/lists',
		'parent' => 'PROMOTION_CENTER',
		'is_show' => 0,
		'sort' => 100,
		'child_list' => [
			[
				'name' => 'PROMOTION_CARDS_LIST',
				'title' => '刮刮乐列表',
				'url' => 'cards://shop/cards/lists',
				'is_show' => 1,
				'child_list' => [
					[
						'name' => 'PROMOTION_CARDS_ADD',
						'title' => '添加活动',
						'url' => 'cards://shop/cards/add',
						'sort' => 1,
						'is_show' => 0
					],
					[
						'name' => 'PROMOTION_CARDS_EDIT',
						'title' => '编辑活动',
						'url' => 'cards://shop/cards/edit',
						'sort' => 1,
						'is_show' => 0
					],
					[
						'name' => 'PROMOTION_CARDS_DETAIL',
						'title' => '活动详情',
						'url' => 'cards://shop/cards/detail',
						'sort' => 1,
						'is_show' => 0
					],
					[
						'name' => 'PROMOTION_CARDS_DELETE',
						'title' => '删除活动',
						'url' => 'cards://shop/cards/delete',
						'sort' => 1,
						'is_show' => 0
					],
					[
						'name' => 'PROMOTION_CARDS_FINISH',
						'title' => '关闭活动',
						'url' => 'cards://shop/cards/finish',
						'sort' => 1,
						'is_show' => 0
					],
                    [
                        'name' => 'PROMOTION_CARDS_RECORD',
                        'title' => '抽奖记录',
                        'url' => 'cards://shop/record/lists',
                        'sort' => 1,
                        'is_show' => 0
                    ]
				
				]
			
			],
		]
	],

];