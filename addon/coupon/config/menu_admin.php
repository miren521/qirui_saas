<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_COUPON',
        'title' => '优惠券',
        'url' => 'coupon://admin/coupon/lists',
        'parent' => 'PROMOTION_SHOP',
        'is_show' => 0,
        'is_control' => 0,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_COUPON_CLOSE',
                'title' => '关闭优惠券',
                'url' => 'coupon://admin/coupon/close',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_COUPON_DELETE',
                'title' => '删除优惠券',
                'url' => 'coupon://admin/coupon/delete',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_COUPON_DETAIL',
                'title' => '优惠券详情',
                'url' => 'coupon://admin/coupon/detail',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_COUPON_LIST',
                'title' => '优惠券列表',
                'url' => 'coupon://admin/coupon/list',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_COUPON_RECEIVE',
                'title' => '优惠券领取记录',
                'url' => 'coupon://admin/coupon/receive',
                'sort'    => 1,
                'is_show' => 0
            ],
        
        ]
    ],
];
