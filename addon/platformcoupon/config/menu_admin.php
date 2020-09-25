<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_PLATFORMCOUPON',
        'title' => '优惠券',
        'url' => 'platformcoupon://admin/platformcoupon/lists',
        'parent' => 'PROMOTION_PLATFORM',
        'is_show' => 0,
        'is_control' => 0,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_PLATFORMCOUPON_DETAIL',
                'title' => '优惠券详情',
                'url' => 'platformcoupon://admin/platformcoupon/detail',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PLATFORMCOUPON_ADD',
                'title' => '添加优惠券',
                'url' => 'platformcoupon://admin/platformcoupon/add',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PLATFORMCOUPON_EDIT',
                'title' => '编辑优惠券',
                'url' => 'platformcoupon://admin/platformcoupon/edit',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PLATFORMCOUPON_CLOSE',
                'title' => '关闭优惠券',
                'url' => 'platformcoupon://admin/platformcoupon/close',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PLATFORMCOUPON_DELETE',
                'title' => '删除优惠券',
                'url' => 'platformcoupon://admin/platformcoupon/delete',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PLATFORMCOUPON_RECEIVE',
                'title' => '优惠券领取记录',
                'url' => 'platformcoupon://admin/platformcoupon/receive',
                'sort'    => 1,
                'is_show' => 0
            ],

        ]
    ],
];
