<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_GIFT',
        'title' => '礼品管理',
        'url' => 'gift://admin/gift/lists',
        'parent' => 'TOOL_ROOT',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_GIFT_LIST',
                'title' => '礼品列表',
                'url' => 'gift://admin/gift/lists',
                'parent' => 'PROMOTION_ROOT',
                'is_show' => 1,
                'picture' => '',
                'picture_select' => '',
                'sort' => 100,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_GIFT_ADD',
                        'title' => '添加礼品',
                        'url' => 'gift://admin/gift/add',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_GIFT_EDIT',
                        'title' => '编辑礼品',
                        'url' => 'gift://admin/gift/edit',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_GIFT_DELETE',
                        'title' => '删除礼品',
                        'url' => 'gift://admin/gift/delete',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                ]
            ],
            [
                'name' => 'PROMOTION_GIFT_ORDER',
                'title' => '领取记录',
                'url' => 'gift://admin/giftorder/lists',
                'is_show' => 1,
                'picture' => '',
                'picture_select' => '',
                'sort' => 100,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_GIFT_ORDER_EXPRESS',
                        'title' => '发货',
                        'url' => 'gift://admin/giftorder/express',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_GIFT_ORDER_DETAIL',
                        'title' => '订单详情',
                        'url' => 'gift://admin/giftorder/detail',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                ]
            ],
           

        ]
    ],

];
