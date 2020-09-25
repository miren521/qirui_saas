<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_PINTUAN',
        'title' => '拼团活动',
        'url' => 'pintuan://admin/pintuan/lists',
        'parent' => 'PROMOTION_SHOP',
        'is_show' => 0,
        'is_control' => 1,
        'is_icon' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_PINTUAN_LISTS',
                'title' => '拼团列表',
                'url' => 'pintuan://admin/pintuan/lists',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 100,
            ],
            [
                'name' => 'PROMOTION_PINTUAN_GROUP',
                'title' => '拼团详情',
                'url' => 'pintuan://admin/pintuan/group',
                'sort'    => 1,
                'is_show' => 0,
                'is_control' => 1,
                'is_icon' => 0,
            ],
            [
                'name' => 'PROMOTION_PINTUAN_GROUP_ORDER',
                'title' => '拼团订单',
                'url' => 'pintuan://admin/pintuan/grouporder',
                'sort'    => 1,
                'is_show' => 0,
                'is_control' => 1,
                'is_icon' => 0,
            ],

        ]
    ],

];
