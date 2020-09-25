<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
return [
    [
        'name' => 'WECHAT_ROOT',
        'title' => '微信公众号',
        'url' => 'wechat://admin/wechat/setting',
        'parent' => 'APPLET_ROOT',
        'picture_select' => '',
        'picture' => 'addon/wechat/admin/view/public/img/menu_icon/wechat_icon.png',
        'is_show' => 1,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'WECHAT_SETTING',
                'title' => '公众号设置',
                'url' => 'wechat://admin/wechat/setting',
                'is_show' => 1,
                'picture' => '',
                'picture_select' => '',
                'sort' => 0,
                'child_list' => [
                    [
                         
                        'name' => 'WCHAT_CONFIG',
                        'title' => '公众平台配置',
                        'url' => 'wechat://admin/wechat/config',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_MENU',
                        'title' => '菜单管理',
                        'url' => 'wechat://admin/menu/menu',
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'WECHAT_MATERIAL',
                        'title' => '消息素材',
                        'url' => 'wechat://admin/material/lists',
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'WECHAT_MATERIAL_ADD',
                        'title' => '添加图文',
                        'url' => 'wechat://admin/material/add',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_MATERIAL_EDIT',
                        'title' => '修改图文',
                        'url' => 'wechat://admin/material/edit',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_MATERIAL_DELETE',
                        'title' => '删除图文',
                        'url' => 'wechat://admin/material/delete',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_QRCODE',
                        'title' => '推广二维码管理',
                        'url' => 'wechat://admin/wechat/qrcode',
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'WECHAT_QRCODE_ADD',
                        'title' => '推广二维码添加',
                        'url' => 'wechat://admin/wechat/addqrcode',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_QRCODE_EDIT',
                        'title' => '推广二维码编辑',
                        'url' => 'wechat://admin/wechat/editqrcode',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_QRCODE_DELETE',
                        'title' => '推广二维码删除',
                        'url' => 'wechat://admin/wechat/deleteqrcode',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_SHARE',
                        'title' => '分享内容设置',
                        'url' => 'wechat://admin/wechat/share',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_REPLAY_INDEX',
                        'title' => '回复设置',
                        'url' => 'wechat://admin/replay/replay',
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'WECHAT_REPLAY_KEYS',
                        'title' => '关键词自动回复',
                        'url' => 'wechat://admin/replay/replay',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_REPLAY_FOLLOW',
                        'title' => '关注后自动回复',
                        'url' => 'wechat://admin/replay/follow',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_MASS_INDEX',
                        'title' => '群发设置',
                        'url' => 'wechat://admin/wechat/mass',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_FANS',
                        'title' => '粉丝管理',
                        'url' => 'wechat://admin/fans/lists',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WECHAT_FANS_LIST',
                        'title' => '粉丝列表',
                        'url' => 'wechat://admin/fans/lists',
                        'is_show' => 1
                    ],
                    [
                        'name' => 'WECHAT_FANS_TAG_LIST',
                        'title' => '粉丝标签',
                        'url' => 'wechat://admin/fans/fanstaglist',
                        'is_show' => 1
                    ],
                    [
                        'name' => 'WECHAT_MESSAGE_CONFIG',
                        'title' => '微信消息模板',
                        'url' => 'wechat://admin/message/config',
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'WECHAT_MESSAGE_EDIT',
                        'title' => '编辑消息模板',
                        'url' => 'wechat://admin/message/edit',
                        'is_show' => 0
                    ]
                ]
            ],
            /* [
                'name' => 'WECHAT_STAT',
                'title' => '访问统计',
                'url' => 'wechat://admin/wechat/stat',
                'picture_select' => '',
                'picture' => '',
                'is_show' => 1,
                'sort' => 1,
            ], */
        ]
    ],
    [
        'name' => 'WECHAT_MESSAGE_CONFIG',
        'title' => '微信消息模板配置',
        'url' => 'wechat://admin/message/edit',
        'parent' => 'MANAGE_MARAGE',
        'is_show' => 0,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 1,
    ],
        
];