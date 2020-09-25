<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_BUNDLING',
        'title' => '优惠套餐',
        'url' => 'bundling://admin/bundling/lists',
        'parent' => 'PROMOTION_SHOP',
        'is_show' =>0,
        'is_control' => 0,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_BUNDLING_DELETE',
                'title' => '删除套餐',
                'url' => 'bundling://admin/bundling/delete',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_BUNDLING_DETAIL',
                'title' => '套餐详情',
                'url' => 'bundling://admin/bundling/detail',
                'sort'    => 1,
                'is_show' => 0
            ],
        
        ]
    ],
];
