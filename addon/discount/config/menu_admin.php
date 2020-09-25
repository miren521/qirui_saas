<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_DISCOUNT',
        'title' => '限时折扣',
        'url' => 'discount://admin/discount/lists',
        'parent' => 'PROMOTION_SHOP',
        'is_show' => 0,
        'is_control' => 0,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_DISCOUNT_CLOSE',
                'title' => '关闭活动',
                'url' => 'discount://admin/discount/close',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_DISCOUNT_DELETE',
                'title' => '删除活动',
                'url' => 'discount://admin/discount/delete',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_DISCOUNT_DETAIL',
                'title' => '活动详情',
                'url' => 'discount://admin/discount/detail',
                'sort'    => 1,
                'is_show' => 0
            ],
        
        ]
    ],
];
