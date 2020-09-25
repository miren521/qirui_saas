<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_PRESENT',
        'title' => '赠品活动',
        'url' => 'present://admin/present/lists',
        'parent' => 'PROMOTION_SHOP',
        'is_show' => 0,
        'is_control' => 1,
        'is_icon' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_PRESENT_LISTS',
                'title' => '赠品列表',
                'url' => 'present://admin/present/lists',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 100,
            ],
            [
                'name' => 'PROMOTION_PRESENT_DETAIL',
                'title' => '赠品详情',
                'url' => 'present://admin/present/detail',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 100,
            ]

        ]
    ],

];
