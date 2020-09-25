<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'ADDON_CITY_ROOT',
        'title' => '城市分站',
        'url' => 'city://admin/website/lists',
        'parent' => '',
        'is_show' => 1,
        'is_control' => 0,
        'is_icon' => 0,
        'picture' => 'app/admin/view/public/img/menu_icon/survey.png',
        'picture_selected' => 'app/admin/view/public/img/menu_icon/survey_selected.png',
        'sort' => 5,
        'child_list' => [
            [
                'name' => 'ADDON_CITY_INDEX_LISTS',
                'title' => '分站列表',
                'url' => 'city://admin/website/lists',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 1,
                'child_list' => [
                    [
                        'name' => 'ADDON_CITY_LISTS',
                        'title' => '分站列表',
                        'url' => 'city://admin/website/lists',
                        'is_show' => 0,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 1,
                        'child_list' => [
                            [
                                'name' => 'ADDON_CITY_ADD',
                                'title' => '添加分站',
                                'url' => 'city://admin/website/add',
                                'is_show' => 0,
                                'is_control' => 1,
                                'is_icon' => 0,

                            ],
                            [
                                'name' => 'ADDON_CITY_EDIT',
                                'title' => '编辑分站',
                                'url' => 'city://admin/website/edit',
                                'is_show' => 0,
                                'is_control' => 1,
                                'is_icon' => 0,
                            ],
                            [
                                'name' => 'ADDON_CITY_DETAIL',
                                'title' => '分站详情',
                                'url' => 'city://admin/website/detail',
                                'is_show' => 1,
                                'is_control' => 1,
                                'is_icon' => 0,
                            ],
                            [
                                'name' => 'ADDON_CITY_DASHBOARD',
                                'title' => '账户明细',
                                'url' => 'city://admin/website/dashboard',
                                'is_show' => 1,
                                'is_control' => 1,
                                'is_icon' => 0,
                            ],


                        ]
                    ],
                ]
            ],

        ]
    ],
    [
        'name' => 'ADDON_CITY_WITHDRAW_LISTS_ROOT',
        'title' => '分站转账',
        'url' => 'city://admin/withdraw/adminlists',
        'parent' => 'ACCOUNT_ROOT',
        'is_show' => 1,
        'is_control' => 0,
        'is_icon' => 0,
    ],
    [
        'name' => 'ADDON_CITY_SETTLEMENT_CONFIG',
        'title' => '分站设置',
        'url' => 'city://admin/config/config',
        'parent' => 'CONFIG_MALL',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
    ],
    [
        'name' => 'ADDON_CITY_ACCOUNT_SETTLEMENT',
        'title' => '分站结算',
        'url' => 'city://admin/settlement/lists',
        'parent' => 'ACCOUNT_ROOT',
        'is_show' => 1,
        'is_control' => 1,
        'child_list' => [
            [
                'name' => 'ADDON_CITY_ACCOUNT_SETTLEMENT_DETAIL',
                'title' => '结算详情',
                'url' => 'city://admin/settlement/detail',
                'is_show' => 0,
                'is_control' => 1,
                'child_list' => [
                    [
                        'name' => 'ADDON_CITY_SETTLEMENT_ORDER',
                        'title' => '订单结算',
                        'url' => 'city://admin/settlement/orderdetail',
                        'is_show' => 1,
                        'is_control' => 1,
                    ],
                    [
                        'name' => 'ADDON_CITY_SETTLEMENT_OPEN_SHOP',
                        'title' => '店铺入驻',
                        'url' => 'city://admin/settlement/openshopaccount',
                        'is_show' => 1,
                        'is_control' => 1,
                    ],
                ]
            ],
        ]
    ],



];
