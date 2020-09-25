<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
	[
		'name' => 'ADDON_CITY_ROOT',
		'title' => '概况',
		'url' => 'city://city/index/index',
		'is_show' => 1,
		'sort' => 1,
	],
	[
		'name' => 'ADDON_CITY_SHOP_ROOT',
		'title' => '店铺管理',
		'url' => 'city://city/shop/lists',
		'parent' => '',
		'is_show' => 1,
		'sort' => 2,
		'child_list' => [
			[
				'name' => 'ADDON_CITY_SHOP_INDEX',
				'title' => '店铺管理',
				'url' => 'city://city/shop/lists',
				'is_show' => 1,
				'sort' => 1,
				'child_list' => [
					[
						'name' => 'ADDON_CITY_SHOP_LIST',
						'title' => '店铺列表',
						'url' => 'city://city/shop/lists',
						'is_show' => 1,
						'sort' => 1,
						'child_list' => [
							[
								'name' => 'ADDON_CITY_SHOP_DETAIL',
								'title' => '店铺详情',
								'url' => 'city://city/shop/shopdetail',
								'is_show' => 0,
							],
							[
								'name' => 'ADDON_CITY_SHOP_BISIC_INFO',
								'title' => '基本信息',
								'url' => 'city://city/shop/basicinfo',
								'is_show' => 1,
							],
							[
								'name' => 'ADDON_CITY_SHOP_CERT_INFO',
								'title' => '认证信息',
								'url' => 'city://city/shop/certinfo',
								'is_show' => 1,
							],
							[
								'name' => 'ADDON_CITY_SHOP_SETTLEMENT_INFO',
								'title' => '结算账户',
								'url' => 'city://city/shop/settlementinfo',
								'is_show' => 1,
							],
							[
								'name' => 'ADDON_CITY_SHOP_ACCOUNT_INFO',
								'title' => '账户信息',
								'url' => 'city://city/shop/accountinfo',
								'is_show' => 1,
							],
							[
								'name' => 'ADDON_CITY_SHOP_LOCK',
								'title' => '店铺锁定',
								'url' => 'city://city/shop/lockshop',
								'is_show' => 0,
							],
							[
								'name' => 'ADDON_CITY_SHOP_UNLOCK',
								'title' => '店铺解锁',
								'url' => 'city://city/shop/unlockshop',
								'is_show' => 0,
							],
						]
					],
					[
						'name' => 'ADDON_CITY_SHOP_APPLY',
						'title' => '入驻申请',
						'url' => 'city://city/shopapply/apply',
						'is_show' => 1,
						'picture' => '',
						'sort' => 6,
						'child_list' => [
							[
								'name' => 'ADDON_CITY_SHOP_APPLY_DETAIL',
								'title' => '申请详情',
								'url' => 'city://city/shopapply/applydetail',
								'is_show' => 0,
							],
							[
								'name' => 'ADDON_CITY_SHOP_APPLY_EDIT',
								'title' => '支付信息',
								'url' => 'city://city/shopapply/editapply',
								'is_show' => 0,
							],
							[
								'name' => 'ADDON_CITY_SHOP_APPLY_PASS',
								'title' => '申请通过',
								'url' => 'city://city/shopapply/applypass',
								'is_show' => 0,
							],
							[
								'name' => 'ADDON_CITY_SHOP_APPLY_REJECT',
								'title' => '申请拒绝',
								'url' => 'city://city/shopapply/applyreject',
								'is_show' => 0,
							],
							[
								'name' => 'ADDON_CITY_SHOP_OPEN_SHOP',
								'title' => '入驻通过',
								'url' => 'city://city/shopapply/openshop',
								'is_show' => 0,
							],
						]
					],
					[
						'name' => 'ADDON_CITY_SHOP_REPLAY',
						'title' => '续签申请',
						'url' => 'city://city/shopreopen/reopen',
						'is_show' => 1,
						'picture' => '',
						'sort' => 6,
					],
				],
			],
		],
	],
	[
		'name' => 'ADDON_CITY_GOODS_ROOT',
		'title' => '商品管理',
		'url' => 'city://city/goods/lists',
		'parent' => '',
		'is_show' => 1,
		'is_control' => 1,
		'is_icon' => 0,
		'sort' => 3,
		'child_list' => [
			[
				'name' => 'ADDON_CITY_GOODS_MANAGE',
				'title' => '商品列表',
				'url' => 'city://city/goods/lists',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 1,
				'child_list' => [
					[
						'name' => 'ADDON_CITY_PHYSICAL_GOODS_LOCKUP',
						'title' => '违规下架',
						'url' => 'city://city/goods/lockup',
						'sort' => 1,
						'is_show' => 0
					],
					[
						'name' => 'ADDON_CITY_PHYSICAL_GOODS_VERIFY_ON',
						'title' => '审核通过',
						'url' => 'city://city/goods/verifyon',
						'sort' => 2,
						'is_show' => 0
					],
				
				]
			],
			[
				'name' => 'ADDON_CITY_GOODS_EVALUATE',
				'title' => '商品评价',
				'url' => 'city://city/goods/evaluatelist',
				'is_show' => 1,
				'sort' => 5,
				'child_list' => [
					[
						'name' => 'ADDON_CITY_GOODS_EVALULATE_DELETE',
						'title' => '评价删除',
						'url' => 'city://city/goods/deleteevaluate',
						'is_show' => 0
					],
				]
			]
		]
	],
	[
		'name' => 'ADDON_CITY_ORDER_ROOT',
		'title' => '订单管理',
		'url' => 'city://city/order/lists',
		'parent' => '',
		'is_show' => 1,
		'sort' => 4,
		'child_list' => [
			[
				'name' => 'ADDON_CITY_ORDER_EXPRESS',
				'title' => '订单列表',
				'url' => 'city://city/order/lists',
				'is_show' => 1,
				'sort' => 1,
				"child_list" => [
					[
						'name' => 'ADDON_CITY_EXPRESS_ORDER_DETAIL',
						'title' => '订单详情',
						'url' => 'city://city/order/detail',
						'is_show' => 0
					],
					[
						'name' => 'ADDON_CITY_STORE_ORDER_DETAIL',
						'title' => '自提订单详情',
						'url' => 'city://city/storeorder/detail',
						'is_show' => 0
					],
					[
						'name' => 'ADDON_CITY_LOCAL_ORDER_DETAIL',
						'title' => '外卖订单详情',
						'url' => 'city://city/localorder/detail',
						'is_show' => 0
					],
					[
						'name' => 'ADDON_CITY_VIRTUAL_ORDER_DETAIL',
						'title' => '虚拟订单详情',
						'url' => 'city://city/virtualorder/detail',
						'is_show' => 0
					],
				]
			],
			
			[
				'name' => 'ADDON_CITY_ORDER_REFUND',
				'title' => '退款维权',
				'url' => 'city://city/refund/lists',
				'is_show' => 1,
				'sort' => 5,
				"child_list" => [
					[
						'name' => 'ADDON_CITY_ORDER_REFUND_DETAIL',
						'title' => '退款详情',
						'url' => 'city://city/refund/detail',
						'is_show' => 0
					],
				]
			],
			[
				'name' => 'ADDON_CITY_ORDER_COMPLAIN',
				'title' => '平台维权',
				'url' => 'city://city/complain/lists',
				'is_show' => 1,
				'sort' => 6,
				"child_list" => [
					[
						'name' => 'ADDON_CITY_ORDER_COMPLAIN_DETAIL',
						'title' => '维权详情',
						'url' => 'city://city/complain/detail',
						'is_show' => 0
					],
				]
			],
		],
	],
	[
		'name' => 'ADDON_CITY_ACCOUNT_ROOT',
		'title' => '资产',
		'url' => 'city://city/account/dashboard',
		'is_show' => 1,
		'sort' => 5,
		'child_list' => [
			[
				'name' => 'ADDON_CITY_ACCOUNT_DASHBOARD_INDEX',
				'title' => '资产概况',
				'url' => 'city://city/account/dashboard',
				'is_show' => 1,
				'is_control' => 1,
				'is_icon' => 0,
				'sort' => 1,
				'child_list' => [
					[
						'name' => 'ADDON_CITY_ACCOUNT_ORDERLIST',
						'title' => '交易记录',
						'url' => 'city://city/account/orderlist',
						'is_show' => 0,
						'is_control' => 1,
						'sort' => 1,
					],
				]
			],
			[
				'name' => 'ADDON_CITY_ city://city_WITHDRAW',
				'title' => '转账记录',
				'url' => 'city://city/withdraw/lists',
				'is_show' => 1,
				'is_control' => 1,
				'sort' => 2,
				'child_list' => [
					[
						'name' => 'ADDON_CITY_WITHDRAW_APPLY',
						'title' => '申请提现',
						'url' => 'city://city/withdraw/apply',
						'is_show' => 0,
						'is_control' => 1,
						'sort' => 1,
					],
				]
			],
			[
				'name' => 'ADDON_CITY_ACCOUNT_FEE',
				'title' => '店铺入驻',
				'url' => 'city://city/account/fee',
				'is_show' => 1,
				'is_control' => 1,
				'sort' => 3,
			],
			[
				'name' => 'ADDON_CITY_ACCOUNT_SETTLEMENT_ROOT',
				'title' => '结算列表',
				'url' => 'city://city/settlement/lists',
				'is_show' => 1,
				'is_control' => 1,
				'sort' => 4,
                'child_list' => [
                    [
                        'name' => 'ADDON_CITY_ACCOUNT_SETTLEMENT',
                        'title' => '结算列表',
                        'url' => 'city://city/settlement/lists',
                        'is_show' => 0,
                        'is_control' => 1,
                        'sort' => 1,
                        'child_list' => [
                            [
                                'name' => 'ADDON_CITY_SETTLEMENT_ORDER',
                                'title' => '订单结算',
                                'url' => 'city://city/settlement/orderdetail',
                                'is_show' => 1,
                                'is_control' => 1,
                            ],
                            [
                                'name' => 'ADDON_CITY_SETTLEMENT_OPEN_SHOP',
                                'title' => '店铺入驻',
                                'url' => 'city://city/settlement/openshopaccount',
                                'is_show' => 1,
                                'is_control' => 1,
                            ],
                        ]
                    ],
                ]
			],
		]
	],
	[
		'name' => 'ADDON_CITY_CONFIG',
		'title' => '分站设置',
		'url' => 'city://city/website/config',
		'is_show' => 1,
		'is_control' => 1,
		'is_icon' => 0,
		'sort' => 6,
		'child_list' => [
			[
				'name' => 'ADDON_CITY_WEBSITE_CONFIG',
				'title' => '站点设置',
				'url' => 'city://city/website/config',
				'is_show' => 1,
				'sort' => 1,
			],
			[
				'name' => 'ADDON_CITY_WEBSITE_DIY_CONFIG',
				'title' => '装修信息',
				'url' => 'city://city/diy/index',
				'is_show' => 1,
				'sort' => 2,
				'child_list' => [
					[
						'name' => 'ADDON_CITY_WEBSITE_INDEX',
						'title' => '分站主页',
						'url' => 'city://city/diy/index',
						'parent' => '',
						'is_show' => 1,
						'picture' => '',
						'picture_selected' => '',
						'sort' => 1,
						'child_list' => [
						],
					],
				],
			],
		],
	],
	[
		'name' => 'ADDON_CITY_USER',
		'title' => '管理员',
		'url' => 'city://city/user/user',
		'is_show' => 1,
		'sort' => 7,
		'child_list' => [
			[
				'name' => 'ADDON_CITY_CONFIG_USER_INDEX',
				'title' => '用户列表',
				'url' => 'city://city/user/user',
				'is_show' => 1,
				'sort' => 1,
			],
			[
				'name' => 'ADDON_CITY_CONFIG_USER_ADD',
				'title' => '添加用户',
				'url' => 'city://city/user/adduser',
				'is_show' => 0,
				'sort' => 0,
				'child_list' => [],
			],
			[
				'name' => 'ADDON_CITY_CONFIG_USER_EDIT',
				'title' => '编辑用户',
				'url' => 'city://city/user/edituser',
				'is_show' => 0,
				'sort' => 0,
				'child_list' => [],
			],
			[
				'name' => 'ADDON_CITY_CONFIG_USER_GROUP',
				'title' => '用户组',
				'url' => 'city://city/user/group',
				'is_show' => 1,
				'sort' => 2,
				'child_list' => [
				
				],
			],
			[
				'name' => 'ADDON_CITY_CONFIG_USER_GROUP_ADD',
				'title' => '添加用户组',
				'url' => 'city://city/user/addgroup',
				'is_show' => 0,
				'sort' => 0,
				'child_list' => [],
			],
			[
				'name' => 'ADDON_CITY_CONFIG_USER_GROUP_EDIT',
				'title' => '编辑用户组',
				'url' => 'city://city/user/editgroup',
				'is_show' => 0,
				'sort' => 1,
				'child_list' => [],
			],
			[
				'name' => 'ADDON_CITY_CONFIG_USER_GROUP_DELETE',
				'title' => '删除用户组',
				'url' => 'city://city/user/deletegroup',
				'is_show' => 0,
				'sort' => 1,
				'child_list' => [],
			],
			[
				'name' => 'ADDON_CITY_CONFIG_MANAGE_USERLOG',
				'title' => '操作日志',
				'url' => 'city://city/user/userlog',
				'is_show' => 1,
				'sort' => 3,
			],
			[
				'name' => 'ADDON_CITY_CONFIG_MANAGE_USERLOG_DELETE',
				'title' => '删除日志',
				'url' => 'city://city/user/deleteuserlog',
				'is_show' => 0,
				'sort' => 1,
			],
		
		],
	],
];