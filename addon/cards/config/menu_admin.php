<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_ADMIN_CARDS',
        'title' => '刮刮乐',
        'url' => 'cards://admin/cards/lists',
        'parent' => 'PROMOTION_MEMBER',
        'is_show' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_ADMIN_CARDS_LIST',
                'title' => '刮刮乐列表',
                'url' => 'cards://admin/cards/lists',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_ADMIN_CARDS_ADD',
                        'title' => '添加活动',
                        'url' => 'cards://admin/cards/add',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_EDIT',
                        'title' => '编辑活动',
                        'url' => 'cards://admin/cards/edit',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_DETAIL',
                        'title' => '活动详情',
                        'url' => 'cards://admin/cards/detail',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_DELETE',
                        'title' => '删除活动',
                        'url' => 'cards://admin/cards/delete',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_FINISH',
                        'title' => '关闭活动',
                        'url' => 'cards://admin/cards/finish',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_RECORD',
                        'title' => '抽奖记录',
                        'url' => 'cards://admin/record/lists',
                        'sort' => 1,
                        'is_show' => 0
                    ]

                ]

            ],
        ]
    ],

];