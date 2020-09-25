<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_ADMIN_EGG',
        'title' => '砸金蛋',
        'url' => 'egg://admin/egg/lists',
        'parent' => 'PROMOTION_MEMBER',
        'is_show' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_ADMIN_EGG_LIST',
                'title' => '砸金蛋列表',
                'url' => 'egg://admin/egg/lists',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_ADMIN_EGG_ADD',
                        'title' => '添加活动',
                        'url' => 'egg://admin/egg/add',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_EGG_EDIT',
                        'title' => '编辑活动',
                        'url' => 'egg://admin/egg/edit',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_EGG_DETAIL',
                        'title' => '活动详情',
                        'url' => 'egg://admin/egg/detail',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_EGG_DELETE',
                        'title' => '删除活动',
                        'url' => 'egg://admin/egg/delete',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_EGG_FINISH',
                        'title' => '关闭活动',
                        'url' => 'egg://admin/egg/finish',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_EGG_RECORD',
                        'title' => '抽奖记录',
                        'url' => 'egg://admin/record/lists',
                        'sort' => 1,
                        'is_show' => 0
                    ]
                ]
            ],
        ]
    ],
];