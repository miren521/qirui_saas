<?php
// +----------------------------------------------------------------------
// | 店铺端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_ELECTRONICSHEET',
        'title' => '电子面单',
        'url' => 'electronicsheet://shop/config/config',
        'parent' => 'TOOL_ROOT',
        'picture' => 'addon/electronicsheet/shop/view/public/img/distribution.png',
        'is_show' => 1,
        'sort' => 1,
        'child_list' => [
            [
                'name' => 'PROMOTION_ELECTRONICSHEET_CONFIG',
                'title' => '设置',
                'url' => 'electronicsheet://shop/config/config',
                'is_show' => 1,
                'sort' => 1,
                'child_list' => [
                ]
            ],
            [
                'name' => 'PROMOTION_ELECTRONICSHEET_GOODS_LIST',
                'title' => '电子面单模板',
                'url' => 'electronicsheet://shop/electronicsheet/lists',
                'is_show' => 1,
                'sort' => 2,
                'child_list' => [
                    [
                        'name' => 'PROMOTION_ELECTRONICSHEET_ADD',
                        'title' => '添加模板',
                        'url' => 'electronicsheet://shop/electronicsheet/add',
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'PROMOTION_ELECTRONICSHEET_EDIT',
                        'title' => '编辑模板',
                        'url' => 'electronicsheet://shop/electronicsheet/edit',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ELECTRONICSHEET_DELETE',
                        'title' => '删除模板',
                        'url' => 'electronicsheet://shop/electronicsheet/delete',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                    [
                        'name' => 'PROMOTION_ELECTRONICSHEET_SET_DEFAULT_STATUS',
                        'title' => '设置默认状态',
                        'url' => 'electronicsheet://shop/electronicsheet/setdefaultstatus',
                        'sort' => 1,
                        'is_show' => 0
                    ],
                ]
            ],
        ]
    ],


];