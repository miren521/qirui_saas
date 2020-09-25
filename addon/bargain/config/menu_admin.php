<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_ADMIN_BARGAIN',
        'title' => '砍价',
        'url' => 'bargain://admin/bargain/lists',
        'parent' => 'PROMOTION_SHOP',
        'is_show' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_ADMIN_BARGAIN_LIST',
                'title' => '砍价列表',
                'url' => 'bargain://admin/bargain/lists',
                'parent' => 'PROMOTION_BARGAIN',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_ADMIN_BARGAIN_DETAIL',
                        'title' => '活动详情',
                        'url' => 'bargain://admin/bargain/detail',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_BARGAIN_DELETE',
                        'title' => '删除活动',
                        'url' => 'bargain://admin/bargain/delete',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ADMIN_BARGAIN_FINISH',
                        'title' => '结束活动',
                        'url' => 'bargain://admin/bargain/finish',
                        'sort' => 1,
                        'is_show' => 0
                    ]

                ]

            ],
        ]
    ],
];
