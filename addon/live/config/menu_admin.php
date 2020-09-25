<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'LIVE_ROOT_ADMIN',
        'title' => '小程序直播',
        'url' => 'live://admin/room/index',
        'picture' => 'addon/live/admin/view/public/img/live.png',
        'parent' => 'TOOL_ROOT',
        'is_show' => 1,
        'sort' => 1,
        'child_list' => [
            [
                'name' => 'LIVE_ROOM_ADMIN',
                'title' => '直播间',
                'url' => 'live://admin/room/index',
                'is_show' => 1,
                'sort' => 1,
                'child_list' => [
                    [
                        'name' => 'LIVE_ROOM_ADMIN_DETAIL',
                        'title' => '直播间详情',
                        'url' => 'live://admin/room/detail',
                        'is_show' => 0,
                        'sort' => 1,
                        'child_list' => [

                        ]
                    ]
                ]
            ],
        ]
    ]
];
