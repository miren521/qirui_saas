<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'WEAPP_ROOT',
        'title' => '微信小程序',
        'url' => 'weapp://admin/weapp/setting',
        'parent' => 'APPLET_ROOT',
        'picture_select' => '',
        'picture' => 'addon/weapp/admin/view/public/img/menu_icon/wechat_app.png',
        'is_show' => 1,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'WEAPP_SETTING',
                'title' => '小程序设置',
                'url' => 'weapp://admin/weapp/setting',
                'is_show' => 1,
                'picture' => '',
                'picture_select' => '',
                'sort' => 0,
                'child_list' => [
                    [
                         
                        'name' => 'WEAPP_CONFIG',
                        'title' => '小程序配置',
                        'url' => 'weapp://admin/weapp/config',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WEAPP_RELEASE',
                        'title' => '源码发布',
                        'url' => 'weapp://admin/weapp/release',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'WEAPP_PACKAGE',
                        'title' => '小程序包管理',
                        'url' => 'weapp://admin/weapp/package',
                        'is_show' => 0
                    ],
                ]
            ],
            /* [
                'name' => 'WEAPP_STAT',
                'title' => '访问统计',
                'url' => 'weapp://admin/stat/stat',
                'picture_select' => '',
                'picture' => '',
                'is_show' => 1,
                'sort' => 1,
            ], */
        ]
    ]
];
