<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
	[
		'name' => 'PROMOTION_FENXIAO_ROOT',
		'title' => '分销管理',
		'url' => 'fenxiao://admin/fenxiao/index',
		'parent' => 'TOOL_ROOT',
		'is_show' => 1,
		'is_control' => 1,
		'is_icon' => 0,
		'sort' => 1,
		'child_list' => [
			[
				'name' => 'PROMOTION_FENXIAO_INDEX',
				'title' => '分销概况',
				'url' => 'fenxiao://admin/fenxiao/index',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 1,
			],
			[
				'name' => 'PROMOTION_FENXIAO',
				'title' => '分销商',
				'url' => 'fenxiao://admin/fenxiao/lists',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 1,
				'child_list' => [
					[
						'name' => 'PROMOTION_FENXIAO_LISTS',
						'title' => '分销商',
						'url' => 'fenxiao://admin/fenxiao/lists',
						'is_show' => 1,
						'child_list' => [
							[
								'name' => 'PROMOTION_FENXIAO_DETAIL',
								'title' => '分销商信息',
								'url' => 'fenxiao://admin/fenxiao/detail',
								'is_show' => 1,
							],
                            [
                                'name' => 'PROMOTION_FENXIAO_TEAM',
                                'title' => '分销商团队',
                                'url' => 'fenxiao://admin/fenxiao/team',
                                'is_show' => 1,
                            ],
							[
								'name' => 'PROMOTION_FENXIAO_ACCOUNT',
								'title' => '账户明细',
								'url' => 'fenxiao://admin/fenxiao/account',
								'is_show' => 1,
							],
							[
								'name' => 'PROMOTION_FENXIAO_ORDERMANAGE',
								'title' => '订单管理',
								'url' => 'fenxiao://admin/fenxiao/order',
								'is_show' => 1,
							],
							[
								'name' => 'PROMOTION_FENXIAO_ORDERMANAGEDETAIL',
								'title' => '订单详情',
								'url' => 'fenxiao://admin/fenxiao/orderdetail',
								'is_show' => 0,
							],
						
						]
					],
					[
						'name' => 'PROMOTION_FENXIAO_APPLY',
						'title' => '待审核',
						'url' => 'fenxiao://admin/fenxiao/apply',
						'is_show' => 1,
					],
					[
						'name' => 'PROMOTION_FENXIAO_PASS',
						'title' => '审核通过',
						'url' => 'fenxiao://admin/fenxiao/pass',
						'is_show' => 0,
					],
					[
						'name' => 'PROMOTION_FENXIAO_REFUSE',
						'title' => '审核拒绝',
						'url' => 'fenxiao://admin/fenxiao/refuse',
						'is_show' => 0,
					],
					[
						'name' => 'PROMOTION_FENXIAO_FROZEN',
						'title' => '冻结',
						'url' => 'fenxiao://admin/fenxiao/frozen',
						'is_show' => 0,
					],
					[
						'name' => 'PROMOTION_FENXIAO_UNFROZEN',
						'title' => '恢复正常',
						'url' => 'fenxiao://admin/fenxiao/unfrozen',
						'is_show' => 0,
					],
				],
			
			],
			[
				'name' => 'PROMOTION_FENXIAO_GOODS',
				'title' => '分销商品',
				'url' => 'fenxiao://admin/goods/lists',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 2,
				'child_list' => [
					[
						'name' => 'PROMOTION_FENXIAO_GOODS_DETAIL',
						'title' => '商品详情',
						'url' => 'fenxiao://admin/goods/detail',
						'is_show' => 0,
					],
				
				]
			],
			[
				'name' => 'PROMOTION_FENXIAO_ORDER',
				'title' => '分销订单',
				'url' => 'fenxiao://admin/order/lists',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 3,
				'child_list' => [
					[
						'name' => 'PROMOTION_FENXIAO_ORDER_DETAIL',
						'title' => '订单详情',
						'url' => 'fenxiao://admin/order/detail',
						'is_show' => 0,
					],
				
				]
			],
			[
				'name' => 'PROMOTION_FENXIAO_WITHDRAW',
				'title' => '提现管理',
				'url' => 'fenxiao://admin/withdraw/lists',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 4,
				'child_list' => [
					[
						'name' => 'PROMOTION_FENXIAO_WITHDRAW_PASS',
						'title' => '审核通过',
						'url' => 'fenxiao://admin/withdraw/withdrawpass',
						'is_show' => 0,
					],
					[
						'name' => 'PROMOTION_FENXIAO_WITHDRAW_REFUSE',
						'title' => '审核拒绝',
						'url' => 'fenxiao://admin/withdraw/withdrawrefuse',
						'is_show' => 0,
					],
				
				],
			
			],
			[
				'name' => 'PROMOTION_FENXIAO_LEVEL',
				'title' => '分销等级',
				'url' => 'fenxiao://admin/level/lists',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 5,
				'child_list' => [
					[
						'name' => 'PROMOTION_FENXIAO_LEVEL_LISTS',
						'title' => '等级列表',
						'url' => 'fenxiao://admin/level/lists',
						'is_show' => 0,
					],
					[
						'name' => 'PROMOTION_FENXIAO_LEVEL_ADD',
						'title' => '添加等级',
						'url' => 'fenxiao://admin/level/add',
						'is_show' => 0,
					],
					[
						'name' => 'PROMOTION_FENXIAO_LEVEL_EDIT',
						'title' => '编辑等级',
						'url' => 'fenxiao://admin/level/edit',
						'is_show' => 0,
					],
					[
						'name' => 'PROMOTION_FENXIAO_LEVEL_STATUS',
						'title' => '等级状态设置',
						'url' => 'fenxiao://admin/level/status',
						'is_show' => 0,
					],
					[
						'name' => 'PROMOTION_FENXIAO_LEVEL_DELETE',
						'title' => '删除等级',
						'url' => 'fenxiao://admin/level/delete',
						'is_show' => 0,
					]
				]
			],
			[
				'name' => 'PROMOTION_FENXIAO_CONFIG',
				'title' => '分销设置',
				'url' => 'fenxiao://admin/config/basics',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 6,
				'child_list' => [
					[
						'name' => 'PROMOTION_FENXIAO_BASICS',
						'title' => '基础设置',
						'url' => 'fenxiao://admin/config/basics',
						'is_show' => 1,
					],
					[
						'name' => 'PROMOTION_FENXIAO_AGREEMENT',
						'title' => '申请协议',
						'url' => 'fenxiao://admin/config/agreement',
						'is_show' => 1,
					],
					[
						'name' => 'PROMOTION_FENXIAO_SETTLEMENT',
						'title' => '结算设置',
						'url' => 'fenxiao://admin/config/settlement',
						'is_show' => 1,
					],
					[
						'name' => 'PROMOTION_FENXIAO_WORDS',
						'title' => '文字设置',
						'url' => 'fenxiao://admin/config/words',
						'is_show' => 1,
					]
				]
			],
		]
	],
    [
        'name' => 'PROMOTION_FENXIAO_MARKET_ROOT',
        'title' => '页面装修',
        'url' => 'fenxiao://admin/market/index',
        'parent' => 'TOOL_ROOT',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'sort' => 2,
        'child_list' => [
            [
                'name' => 'PROMOTION_FENXIAO_MARKET',
                'title' => '分销市场',
                'url' => 'fenxiao://admin/market/index',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
            ],
        ]
    ]

];