<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'SERVICER',
        'title' => '客服管理',
        'url' => 'servicer://admin/servicer/index',
        'parent' => 'TOOL_ROOT',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'SERVICER_MANAGER',
                'title' => '客服列表',
                'url' => 'servicer://admin/servicer/index',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 1,
                'child_list' => [
                    [
                        'name' => 'SERVICE_MANAGER_ADD',
                        'title' => '添加客服',
                        'url' => 'servicer://admin/servicer/add',
                        'is_show' => 1,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 100,
                    ],
                    [
                        'name' => 'SERVICE_MANAGER_EDIT',
                        'title' => '编辑客服',
                        'url' => 'servicer://admin/servicer/edit',
                        'is_show' => 1,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 100,
                    ],
                    [
                        'name' => 'SERVICE_MANAGER_DELETE',
                        'title' => '删除客服',
                        'url' => 'servicer://admin/servicer/delete',
                        'is_show' => 0,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 100,
                    ],

                ]
            ],
            [
                'name' => 'SERVICER_GROUP',
                'title' => '客服分组',
                'url' => 'servicer://admin/group/index',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 2,
                'child_list' => [
                    [
                        'name' => 'SERVICER_GROUP_ADD',
                        'title' => '添加分组',
                        'url' => 'servicer://admin/group/add',
                        'is_show' => 1,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 100,
                    ],
                    [
                        'name' => 'SERVICER_GROUP_EDIT',
                        'title' => '编辑分组',
                        'url' => 'servicer://admin/group/edit',
                        'is_show' => 1,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 100,
                    ],
                    [
                        'name' => 'SERVICER_GROUP_DELETE',
                        'title' => '删除分组',
                        'url' => 'servicer://admin/group/delete',
                        'is_show' => 0,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 100,
                    ],

                ]
            ],
        ]
    ]
];
