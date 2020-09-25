<?php
// +----------------------------------------------------------------------
// | 店铺端菜单设置
// +----------------------------------------------------------------------
return [

    [
        'name' => 'TOOL_WHOLESALE',
        'title' => '批发管理',
        'url' => 'wholesale://shop/wholesale/lists',
        'parent' => 'TOOL_ROOT',
        'picture' => 'addon/wholesale/shop/view/public/img/wholesale.png',
        'is_show' => 1,
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'TOOL_WHOLESALE_LIST',
                'title' => '批发列表',
                'url' => 'wholesale://shop/wholesale/lists',
                'is_show' => 1,
                'child_list' => [
                    [
                        'name' => 'TOOL_WHOLESALE_ADD',
                        'title' => '批发商品添加',
                        'url' => 'wholesale://shop/wholesale/add',
                        'sort'    => 1,
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'TOOL_WHOLESALE_EDIT',
                        'title' => '批发商品添加',
                        'url' => 'wholesale://shop/wholesale/edit',
                        'sort'    => 1,
                        'is_show' => 0,
                    ],
                    [
                        'name' => 'TOOL_WHOLESALE_DELETE',
                        'title' => '批发商品删除',
                        'url' => 'wholesale://shop/wholesale/delete',
                        'sort'    => 1,
                        'is_show' => 0,
                    ],

                ]

            ],
        ]
    ],


];