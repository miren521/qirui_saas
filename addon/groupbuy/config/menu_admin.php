<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_GROUPBUY',
        'title' => '团购活动',
        'url' => 'groupbuy://admin/groupbuy/lists',
        'parent' => 'PROMOTION_SHOP',
        'is_show' => 0,
        'is_control' => 1,
        'is_icon' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_GROUPBUY_LISTS',
                'title' => '团购列表',
                'url' => 'groupbuy://admin/groupbuy/lists',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 100,
            ],
            [
                'name' => 'PROMOTION_GROUPBUY_DETAIL',
                'title' => '团购详情',
                'url' => 'groupbuy://admin/groupbuy/detail',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 100,
            ]

        ]
    ],

];
