<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'PROMOTION_SECKILL',
        'title' => '限时秒杀',
        'url' => 'seckill://shop/seckill/goodslist',
        'parent' => 'PROMOTION_CENTER',
        'is_show' => 0,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 100,
        'child_list' => [
            [
                'name' => 'PROMOTION_SECKILL_GOODS_LIST',
                'title' => '商品管理',
                'url' => 'seckill://shop/seckill/goodslist',
                'parent' => 'PROMOTION_CENTER',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'picture' => '',
                'picture_select' => '',
                'sort' => 100,
            ],
            [
                'name' => 'PROMOTION_SECKILL_TIME',
                'title' => '时段管理',
                'url' => 'seckill://shop/seckill/lists',
                'parent' => 'PROMOTION_CENTER',
                'is_show' => 1,
                'is_control' => 1,
                'is_icon' => 0,
                'picture' => '',
                'picture_select' => '',
                'sort' => 100,
            ] ,
            [
                'name' => 'PROMOTION_SECKILL_GOODS',
                'title' => '秒杀商品',
                'url' => 'seckill://shop/seckill/goods',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_SELECT',
                'title' => '选择商品',
                'url' => 'seckill://shop/seckill/selectgoods',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_ADD',
                'title' => '添加商品',
                'url' => 'seckill://shop/seckill/addgoods',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_UPDATE',
                'title' => '编辑商品',
                'url' => 'seckill://shop/seckill/updategoods',
                'sort'    => 1,
                'is_show' => 0
            ],
            [
                'name' => 'PROMOTION_SECKILL_GOODS_DELETE',
                'title' => '删除商品',
                'url' => 'seckill://shop/seckill/deletegoods',
                'sort'    => 1,
                'is_show' => 0
            ],
        ]
    ],

];
