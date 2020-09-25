<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_SECKILL',
        'title' => '限时秒杀',
        'url' => 'seckill://admin/seckill/lists',
        'parent' => 'PROMOTION_PLATFORM',
        'is_show' => 0,
        'is_control' => 0,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 100,
        'child_list' => [


            [
                'name' => 'PROMOTION_SECKILL_TIME',
                'title' => '秒杀管理',
                'url' => 'seckill://admin/seckill/lists',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'picture' => '',
                'picture_select' => '',
                'sort' => 1,
            ],
            [
                'name' => 'PROMOTION_SECKILL_ADD',
                'title' => '添加时间段',
                'url' => 'seckill://admin/seckill/add',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_ADD',
                'title' => '编辑时间段',
                'url' => 'seckill://admin/seckill/edit',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_DELETE',
                'title' => '删除时间段',
                'url' => 'seckill://admin/seckill/delete',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS',
                'title' => '秒杀商品',
                'url' => 'seckill://admin/seckill/goods',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_LIST',
                'title' => '商品管理',
                'url' => 'seckill://admin/seckill/goodslist',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'picture' => '',
                'picture_select' => '',
                'sort' => 100,
            ],
        ]
    ],
];
