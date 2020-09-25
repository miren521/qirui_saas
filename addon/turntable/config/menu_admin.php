<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_ADMIN_TURNTABLE',
        'title' => '幸运抽奖',
        'url' => 'turntable://admin/turntable/lists',
        'parent' => 'PROMOTION_MEMBER',
        'is_show' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_ADMIN_TURNTABLE_LIST',
                'title' => '幸运抽奖列表',
                'url' => 'turntable://admin/turntable/lists',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_ADMIN_TURNTABLE_ADD',
                        'title' => '添加活动',
                        'url' => 'turntable://admin/turntable/add',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_TURNTABLE_EDIT',
                        'title' => '编辑活动',
                        'url' => 'turntable://admin/turntable/edit',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_TURNTABLE_DETAIL',
                        'title' => '活动详情',
                        'url' => 'turntable://admin/turntable/detail',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_TURNTABLE_DELETE',
                        'title' => '删除活动',
                        'url' => 'turntable://admin/turntable/delete',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_TURNTABLE_FINISH',
                        'title' => '关闭活动',
                        'url' => 'turntable://admin/turntable/finish',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_TURNTABLE_RECORD',
                        'title' => '抽奖记录',
                        'url' => 'turntable://admin/record/lists',
                        'sort' => 1,
                        'is_show' => 0
                    ]

                ]

            ],
        ]
    ],

];