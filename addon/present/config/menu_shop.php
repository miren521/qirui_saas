<?php
// +----------------------------------------------------------------------
// | 店铺端菜单设置
// +----------------------------------------------------------------------
return [

    [
        'name' => 'PROMOTION_PRESENT',
        'title' => '赠品',
        'url' => 'present://shop/present/lists',
        'parent' => 'PROMOTION_CENTER',
        'is_show' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_PRESENT_LIST',
                'title' => '赠品列表',
                'url' => 'present://shop/present/lists',
                'parent' => 'PROMOTION_PRESENT',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_PRESENT_ADD',
                        'title' => '添加活动',
                        'url' => 'present://shop/present/add',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_PRESENT_EDIT',
                        'title' => '编辑活动',
                        'url' => 'present://shop/present/edit',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_PRESENT_DETAIL',
                        'title' => '活动详情',
                        'url' => 'present://shop/present/detail',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_PRESENT_DELETE',
                        'title' => '删除活动',
                        'url' => 'present://shop/present/delete',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_PRESENT_FINISH',
                        'title' => '结束活动',
                        'url' => 'present://shop/present/finish',
                        'sort'    => 1,
                        'is_show' => 0
                    ]

                ]

            ],
        ]
    ],


];