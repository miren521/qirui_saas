<?php
// +----------------------------------------------------------------------
// | 店铺端菜单设置
// +----------------------------------------------------------------------
return [
	[
		'name' => 'PROMOTION_FENXIAO',
		'title' => '分销管理',
		'url' => 'fenxiao://shop/fenxiao/index',
		'parent' => 'TOOL_ROOT',
		'picture' => 'addon/fenxiao/shop/view/public/img/distribution.png',
		'is_show' => 1,
		'sort' => 1,
		'child_list' => [
			[
				'name' => 'PROMOTION_FENXIAO_INDEX',
				'title' => '分销概况',
				'url' => 'fenxiao://shop/fenxiao/index',
				'is_show' => 1,
				'child_list' => [
				]
			],
			[
				'name' => 'PROMOTION_FENXIAO_LIST',
				'title' => '分销商品',
				'url' => 'fenxiao://shop/fenxiao/lists',
				'is_show' => 1,
				'child_list' => [
					[
						'name' => 'PROMOTION_FENXIAO_CONFIG',
						'title' => '商品设置',
						'url' => 'fenxiao://shop/fenxiao/config',
						'sort' => 1,
						'is_show' => 0
					],
					[
						'name' => 'PROMOTION_FENXIAO_MODIFY',
						'title' => '状态设置',
						'url' => 'fenxiao://shop/fenxiao/modify',
						'sort' => 1,
						'is_show' => 0
					],
				]
			],
			[
				'name' => 'PROMOTION_FENXIAO_ORDER',
				'title' => '分销订单',
				'url' => 'fenxiao://shop/order/lists',
				'is_show' => 1,
				'child_list' => [
					[
						'name' => 'PROMOTION_FENXIAO_ORDER_DETAIL',
						'title' => '订单详情',
						'url' => 'fenxiao://shop/order/detail',
						'sort' => 1,
						'is_show' => 0
					],
				]
			],
		]
	],


];