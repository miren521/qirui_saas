<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'ADDON_SUPPLY_ROOT',
        'title' => '供应商',
        'url' => 'supply://admin/supplier/index',
        'parent' => '',
        'is_show' => 1,
        'is_control' => 0,
        'is_icon' => 0,
        'sort' => 6,
        'child_list' => [
            [
                'name' => 'ADDON_SUPPLY_INDEX',
                'title' => '供应商管理',
                'url' => 'supply://admin/supplier/index',
                'is_show' => 1,
                'sort' => 1,
                'child_list' => [
                    [
                        'name' => 'ADDON_SUPPLY_LIST',
                        'title' => '供应商列表',
                        'url' => 'supply://admin/supplier/index',
                        'is_show' => 1,
                        'is_control' => 0,
                        'sort' => 1,
                        'child_list' => [
                            [
                                'name' => 'ADDON_SUPPLY_ADD',
                                'title' => '添加供应商',
                                'url' => 'supply://admin/supplier/add',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'ADDON_SUPPLY_EDIT',
                                'title' => '供应商信息',
                                'url' => 'supply://admin/supplier/edit',
                                'is_show' => 1,
                            ],
                            [
                                'name' => 'ADDON_SUPPLY_CERT',
                                'title' => '认证信息',
                                'url' => 'supply://admin/supplier/cert',
                                'is_show' => 1,
                            ],
                            [
                                'name' => 'ADDON_SUPPLY_SETTLEMENT',
                                'title' => '结算账户',
                                'url' => 'supply://admin/supplier/settlement',
                                'is_show' => 1,
                            ],
                        ]
                    ],
                    [
                        'name' => 'ADDON_SUPPLY_APPLY_LIST',
                        'title' => '入驻申请',
                        'url' => 'supply://admin/apply/lists',
                        'parent' => '',
                        'is_show' => 1,
                        'is_control' => 1,
                        'sort' => 1,
                        'child_list' => [
                            [
                                'name' => 'ADDON_SUPPLY_APPLY_DETAIL',
                                'title' => '认证信息',
                                'url' => 'supply://admin/apply/detail',
                                'is_show' => 0,
                                'is_control' => 1,
                                'sort' => 1,
                            ],
                            [
                                'name' => 'ADDON_SUPPLY_APPLY_EDIT',
                                'title' => '支付信息',
                                'url' => 'supply://admin/apply/edit',
                                'is_show' => 0,
                                'is_control' => 1,
                                'sort' => 1,
                            ],
                        ]
                    ],
                    [
                        'name' => 'ADDON_SUPPLY_REPLAY',
                        'title' => '续签申请',
                        'url' => 'supply://admin/reopen/lists',
                        'is_show' => 1,
                        'is_control' => 0,
                        'picture' => '',
                        'sort' => 2,
                        'child_list' => [
                            [
                                'name' => 'ADDON_SUPPLY_REOPEN_DETAIL',
                                'title' => '申请详情',
                                'url' => 'supply://admin/reopen/detail',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'ADDON_SUPPLY_REOPEN_PASS',
                                'title' => '申请通过',
                                'url' => 'supply://admin/reopen/pass',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'ADDON_SUPPLY_REOPEN_FAIL',
                                'title' => '申请失败',
                                'url' => 'supply://admin/reopen/fail',
                                'is_show' => 0,
                            ],
                        ]
                    ],
                    [
                        'name'             => 'ADDON_SUPPLY_SERVICE',
                        'title'            => '消保服务',
                        'url'              => 'supply://admin/services/lists',
                        'is_show'          => 1,
                        'is_control' => 0,
                        'sort'             => 4,
                        'child_list'       => [
                            [
                                'name'    => 'SUPPLY_SERVICE_DETAIL',
                                'title'   => '申请详情',
                                'url'     => 'supply://admin/services/detail',
                                'is_show' => 0,
                            ],
                            [
                                'name'    => 'SUPPLY_SERVICE_PASS',
                                'title'   => '申请通过',
                                'url'     => 'supply://admin/services/pass',
                                'is_show' => 0,
                            ],
                            [
                                'name'    => 'SUPPLY_SERVICE_REJECT',
                                'title'   => '申请拒绝',
                                'url'     => 'supply://admin/services/reject',
                                'is_show' => 0,
                            ],
                        ],
                    ],
                ]
            ],
            [
                'name' => 'SUPPLY_ORDER_ROOT',
                'title' => '订单管理',
                'url' => 'supply://admin/order/lists',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 2,
                'child_list' => [
                    [
                        'name' => 'SUPPLY_ORDER_EXPRESS',
                        'title' => '订单列表',
                        'url' => 'supply://admin/order/lists',
                        'is_show' => 1,
                        'sort' => 1,
                        "child_list" => [
                            [
                                'name' => 'SUPPLY_EXPRESS_ORDER_DETAIL',
                                'title' => '订单详情',
                                'url' => 'supply://admin/order/detail',
                                'is_show' => 0
                            ]
                        ]
                    ],

                    [
                        'name' => 'SUPPLY_ORDER_REFUND',
                        'title' => '退款维权',
                        'url' => 'supply://admin/refund/lists',
                        'is_show' => 1,
                        'sort' => 5,
                        "child_list" => [
                            [
                                'name' => 'SUPPLY_ORDER_REFUND_DETAIL',
                                'title' => '退款详情',
                                'url' => 'supply://admin/refund/detail',
                                'is_show' => 0
                            ],
                        ]
                    ],
                ],
            ],
            [
                'name' => 'SUPPLY_GOODS_ROOT',
                'title' => '商品管理',
                'url' => 'supply://admin/goods/lists',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 3,
                'child_list' => [
                    [
                        'name' => 'SUPPLY_GOODS_MANAGE',
                        'title' => '商品列表',
                        'url' => 'supply://admin/goods/lists',
                        'is_show' => 1,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 1,
                        'child_list' => [
                            [
                                'name' => 'SUPPLY_PHYSICAL_GOODS_LOCKUP',
                                'title' => '违规下架',
                                'url' => 'supply://admin/goods/lockup',
                                'sort' => 1,
                                'is_show' => 0
                            ],
                            [
                                'name' => 'SUPPLY_PHYSICAL_GOODS_VERIFY_ON',
                                'title' => '审核通过',
                                'url' => 'supply://admin/goods/verifyon',
                                'sort' => 2,
                                'is_show' => 0
                            ],

                        ]
                    ],
                    [
                        'name' => 'SUPPLY_GOODS_EVALUATE',
                        'title' => '商品评价',
                        'url' => 'supply://admin/goods/evaluatelist',
                        'is_show' => 1,
                        'sort' => 5,
                        'child_list' => [
                            [
                                'name' => 'SUPPLY_GOODS_EVALULATE_DELETE',
                                'title' => '评价删除',
                                'url' => 'supply://admin/goods/deleteevaluate',
                                'is_show' => 0
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'SUPPLY_ROOT_CONFIG',
                'title' => '供应商配置',
                'url' => 'supply://admin/supplier/config',
                'is_show' => 1,
                'picture' => '',
                'picture_selected' => '',
                'sort' => 4,
                'child_list' => [
                    [
                        'name' => 'SUPPLY_CONFIG',
                        'title' => '供应商配置',
                        'url' => 'supply://admin/supplier/config',
                        'is_show' => 1,
                    ],
                    [
                        'name' => 'SUPPLY_TRADE_CONFIG',
                        'title' => '交易配置',
                        'url' => 'supply://admin/order/config',
                        'is_show' => 1,
                    ],
                    [
                        'name' => 'SUPPLY_WITHDRAW_CONFIG',
                        'title' => '提现配置',
                        'url' => 'supply://admin/withdraw/config',
                        'is_show' => 1,
                    ],
                    [
                        'name' => 'ADDON_SUPPLY_CATEGORY',
                        'title' => '主营行业',
                        'url' => 'supply://admin/supplycategory/lists',
                        'is_show' => 1,
                        'child_list' => [
                            [
                                'name' => 'SUPPLY_CATEGORY_ADD',
                                'title' => '行业添加',
                                'url' => 'supply://admin/supplycategory/addcategory',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_CATEGORY_EDIT',
                                'title' => '行业编辑',
                                'url' => 'supply://admin/supplycategory/editcategory',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_CATEGORY_DELETE',
                                'title' => '行业删除',
                                'url' => 'supply://admin/supplycategory/deletecategory',
                                'is_show' => 0,
                            ],
                        ],
                    ],
                    [
                        'name' => 'SUPPLY_APPLY_AGREEMENT',
                        'title' => '入驻协议',
                        'url' => 'supply://admin/apply/agreement',
                        'is_show' => 1,
                    ],
                ],
            ],

            [
                'name' => 'SUPPLY_WEBSITE_ADV',
                'title' => '广告位管理',
                'url' => 'supply://admin/adv/index',
                'parent' => '',
                'is_show' => 1,
                'picture' => '',
                'picture_selected' => '',
                'sort' => 8,
                'child_list' => [
                    [
                        'name' => 'SUPPLY_WEBSITE_ADV_POSITION_ADD',
                        'title' => '添加广告位',
                        'url' => 'supply://admin/adv/addposition',
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'SUPPLY_WEBSITE_ADV_POSITION_EDIT',
                        'title' => '编辑广告位',
                        'url' => 'supply://admin/adv/editposition',
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'SUPPLY_WEBSITE_ADV_POSITION',
                        'title' => '广告位管理',
                        'url' => 'supply://admin/adv/index',
                        'is_show' => 1,
                    ],
                    [
                        'name' => 'SUPPLY_WEBSITE_ADV_LISTS',
                        'title' => '广告管理',
                        'url' => 'supply://admin/adv/lists',
                        'is_show' => 1,
                        'child_list' => [
                            [
                                'name' => 'SUPPLY_WEBSITE_ADV_ADD',
                                'title' => '添加广告',
                                'url' => 'supply://admin/adv/addadv',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_WEBSITE_ADV_EDIT',
                                'title' => '编辑广告',
                                'url' => 'supply://admin/adv/editadv',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_WEBSITE_ADV_DELETE',
                                'title' => '删除广告',
                                'url' => 'supply://admin/adv/deleteadv',
                                'is_show' => 0,
                            ],
                        ],
                    ],
                ],
            ]
        ]
    ],

    [
        'name' => 'SUPPLY_DEPOSIT_LIST',
        'title' => '供应商保证金',
        'url' => 'supply://admin/account/deposit',
        'parent' => 'ACCOUNT_INDEX',
        'is_show' => 0,
    ],
    [
        'name' => 'SUPPLY_BALANCE_LIST',
        'title' => '供应商余额',
        'url' => 'supply://admin/account/balance',
        'parent' => 'ACCOUNT_INDEX',
        'is_show' => 0,
        'child_list' => [
            [
                'name' => 'SUPPLY_ACCOUNT_INFO',
                'title' => '供应商账户信息',
                'url' => 'supply://admin/supplier/accountInfo',
                'is_show' => 0,
            ],
        ]
    ],
    [
        'name' => 'ACCOUNT_SUPPLY_SETTLEMENT',
        'title' => '供应商结算',
        'url' => 'supply://admin/supplysettlement/lists',
        'is_show' => 1,
        'parent' => 'ACCOUNT_ROOT',
        'sort' => 101,
        'child_list' => [
            [
                'name' => 'ACCOUNT_SUPPLY_SETTLEMENT_LIST',
                'title' => '结算列表',
                'url' => 'supply://admin/supplysettlement/lists',
                'is_show' => 0,
            ],
            [
                'name' => 'ACCOUNT_SUPPLY_SETTLEMENT_DETAIL',
                'title' => '结算详情',
                'url' => 'supply://admin/supplysettlement/detail',
                'is_show' => 0,
            ],
            [
                'name' => 'ACCOUNT_SUPPLY_SETTLEMENT_SHOPDETAIL',
                'title' => '供应商结算列表',
                'url' => 'supply://admin/supplysettlement/supply',
                'is_show' => 0,
            ],
        ]
    ],
    [
        'name' => 'ACCOUNT_SUPPLY_WITHDRAW',
        'title' => '供应商提现',
        'url' => 'supply://admin/supplyaccount/withdraw',
        'parent' => 'ACCOUNT_ROOT',
        'sort' => 102,
        'is_show' => 1,
    ],
    [
        'name' => 'ACCOUNT_SUPPLY_OPEN_ACCOUNT',
        'title' => '供应商入驻',
        'url' => 'supply://admin/supplyaccount/openaccount',
        'parent' => 'ACCOUNT_ROOT',
        'sort' => 103,
        'is_show' => 1,
    ],
];
