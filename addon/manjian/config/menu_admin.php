<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_MANJIAN',
        'title' => '满减',
        'url' => 'manjian://admin/manjian/lists',
        'parent' => 'PROMOTION_SHOP',
        'is_show' => 0,
        'is_control' => 0,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_MANJIAN_CLOSE',
                'title' => '关闭活动',
                'url' => 'manjian://admin/manjian/close',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_MANJIAN_DELETE',
                'title' => '删除活动',
                'url' => 'manjian://admin/manjian/delete',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_MANJIAN_DETAIL',
                'title' => '活动详情',
                'url' => 'manjian://admin/manjian/detail',
                'sort'    => 1,
                'is_show' => 0
            ],
        
        ]
    ],
];
