<?php
// +----------------------------------------------------------------------
// | 店铺端菜单设置
// +----------------------------------------------------------------------
return [

    [
        'name' => 'PROMOTION_PINTUAN',
        'title' => '拼团',
        'url' => 'pintuan://shop/pintuan/lists',
        'parent' => 'PROMOTION_CENTER',
        'is_show' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_PINTUAN_LIST',
                'title' => '拼团列表',
                'url' => 'pintuan://shop/pintuan/lists',
                'is_show' => 1,

            ],
            [
                'name' => 'PROMOTION_PINTUAN_ADD',
                'title' => '添加活动',
                'url' => 'pintuan://shop/pintuan/add',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PINTUAN_EDIT',
                'title' => '编辑活动',
                'url' => 'pintuan://shop/pintuan/edit',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PINTUAN_DETAIL',
                'title' => '活动详情',
                'url' => 'pintuan://shop/pintuan/detail',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PINTUAN_DELETE',
                'title' => '删除活动',
                'url' => 'pintuan://shop/pintuan/delete',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PINTUAN_INVALID',
                'title' => '结束活动',
                'url' => 'pintuan://shop/pintuan/invalid',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PINTUAN_GROUP_ORDER',
                'title' => '拼团组订单列表',
                'url' => 'pintuan://shop/pintuan/groupOrder',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_PINTUAN_GROUP',
                'title' => '开团团队',
                'url' => 'pintuan://shop/pintuan/group',
                'parent' => 'PROMOTION_PINTUAN',
                'is_show' => 1,
                'child_list' => [

                ]
            ],


        ]
    ],


];