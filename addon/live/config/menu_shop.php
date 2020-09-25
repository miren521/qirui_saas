<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'LIVE_ROOT',
        'title' => '小程序直播',
        'url' => 'live://shop/room/index',
        'picture' => 'addon/live/shop/view/public/img/live.png',
        'parent' => 'TOOL_ROOT',
        'is_show' => 1,
        'sort' => 1,
        'child_list' => [
            [
                'name' => 'LIVE_ROOM',
                'title' => '直播间',
                'url' => 'live://shop/room/index',
                'is_show' => 1,
                'sort' => 1,
                'child_list' => [
                    [
                        'name' => 'ADD_LIVE_ROOM',
                        'title' => '添加直播间',
                        'url' => 'live://shop/room/add',
                        'is_show' => 0,
                        'sort' => 1,
                    ],
                    [
                        'name' => 'EDIT_LIVE_ROOM',
                        'title' => '编辑直播间',
                        'url' => 'live://shop/room/edit',
                        'is_show' => 0,
                        'sort' => 1,
                    ],
                    [
                        'name' => 'LIVE_ROOM_OPERATE',
                        'title' => '运营',
                        'url' => 'live://shop/room/operate',
                        'is_show' => 0,
                        'sort' => 2,
                    ]
                ]
            ],
            [
                'name' => 'LIVE_GOODS',
                'title' => '直播商品',
                'url' => 'live://shop/goods/index',
                'is_show' => 1,
                'sort' => 2,
                'child_list' => [
                    [
                        'name' => 'ADD_LIVE_GOODS',
                        'title' => '添加商品',
                        'url' => 'live://shop/goods/add',
                        'is_show' => 0,
                        'sort' => 1,
                    ],
                ]
            ],
        ]
    ]
];
