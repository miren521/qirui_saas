<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'TOOL_WHOLESALE',
        'title' => '批发',
        'url' => 'wholesale://admin/wholesale/lists',
        'parent' => 'TOOL_ROOT',
        'is_show' => 1,
        'is_control' => 1,
        'is_icon' => 0,
        'sort' => 20,
        'child_list' => [
            [
                'name' => 'TOOL_WHOLESALE_LISTS',
                'title' => '批发列表',
                'url' => 'wholesale://admin/wholesale/lists',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'sort' => 100,
                'child_list' => [
                    [
                        'name' => 'TOOL_WHOLESALE_SKU_DETAIL',
                        'title' => '批发商品详情',
                        'url' => 'wholesale://admin/wholesale/detail',
                        'is_show' => 0,
                        'is_control' => 1,
                        'is_icon' => 0,
                        'sort' => 100,
                    ],

                ]
            ],

        ]
    ],

];
