<?php
// +----------------------------------------------------------------------
// | 店铺端菜单设置
// +----------------------------------------------------------------------
return [

    [
        'name' => 'PROMOTION_GROUPBUY',
        'title' => '团购',
        'url' => 'groupbuy://shop/groupbuy/lists',
        'parent' => 'PROMOTION_CENTER',
        'is_show' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_GROUPBUY_LIST',
                'title' => '团购列表',
                'url' => 'groupbuy://shop/groupbuy/lists',
                'parent' => 'PROMOTION_GROUPBUY',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_GROUPBUY_ADD',
                        'title' => '添加活动',
                        'url' => 'groupbuy://shop/groupbuy/add',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_GROUPBUY_EDIT',
                        'title' => '编辑活动',
                        'url' => 'groupbuy://shop/groupbuy/edit',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_GROUPBUY_DETAIL',
                        'title' => '活动详情',
                        'url' => 'groupbuy://shop/groupbuy/detail',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_GROUPBUY_DELETE',
                        'title' => '删除活动',
                        'url' => 'groupbuy://shop/groupbuy/delete',
                        'sort'    => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_GROUPBUY_FINISH',
                        'title' => '结束活动',
                        'url' => 'groupbuy://shop/groupbuy/finish',
                        'sort'    => 1,
                        'is_show' => 0
                    ]

                ]

            ],
        ]
    ],


];