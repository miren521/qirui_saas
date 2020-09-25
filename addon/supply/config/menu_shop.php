<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'SUPPLY_MARKET',
        'title' => '供货市场',
        'url' => 'supply://shop/market/index',
        'is_show' => 1,
        'picture' => '',
        'picture_selected' => '',
        'sort' => 7,
        'child_list' => [
            [
                'name' => 'SUPPLY_MARKET_INDEX',
                'title' => '市场首页',
                'url' => 'supply://shop/market/index',
                'is_show' => 0,
                'child_list' => [
                    [
                        'name' => 'SUPPLY_MARKET_LISTS',
                        'title' => '商品列表',
                        'url' => 'supply://shop/goods/goodslist',
                        'is_show' => 0,
                        'child_list' => [
                            [
                                'name' => 'SUPPLY_MARKET_DETAIL',
                                'title' => '商品详情',
                                'url' => 'supply://shop/goods/detail',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_SHOP_SUPPLY_INDEX',
                                'title' => '供应商专页',
                                'url' => 'supply://shop/supply/index',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_MARKET_CART',
                                'title' => '购物车',
                                'url' => 'supply://shop/cart/cart',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_MARKET_EVALUATE',
                                'title' => '商品评价',
                                'url' => 'supply://shop/goodsevaluate/evaluate',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_MARKET_EVALUATE_AGAIN',
                                'title' => '商品追评',
                                'url' => 'supply://shop/goodsevaluate/again',
                                'is_show' => 0,
                            ],
                        ]
                    ],
                    [
                        'name' => 'SUPPLY_SHOP_PAY',
                        'title' => '支付',
                        'url' => 'supply://shop/pay/pay',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'SUPPLY_SHOP_PAY',
                        'title' => '支付通知',
                        'url' => 'supply://shop/pay/notify',
                        'is_show' => 0
                    ],
                    [
                        'name' => 'SUPPLY_SHOP_ORDER_ROOT',
                        'title' => '店铺订单',
                        'url' => 'supply://shop/order/lists',
                        'parent' => '',
                        'is_show' => 0,
                        'child_list' => [
                            [
                                'name' => 'SUPPLY_SHOP_ORDER_CREATE',
                                'title' => '订单创建',
                                'url' => 'supply://shop/ordercreate/payment',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_SHOP_ORDER_EXPRESS',
                                'title' => '订单列表',
                                'url' => 'supply://shop/order/lists',
                                'is_show' => 0,
                                "child_list" => [
                                    [
                                        'name' => 'SUPPLY_SHOP_EXPRESS_ORDER_DETAIL',
                                        'title' => '订单详情',
                                        'url' => 'supply://shop/order/detail',
                                        'is_show' => 0
                                    ],
                                    [
                                        'name' => 'SUPPLY_SHOP_EXPRESS_ORDER_DELIVERY',
                                        'title' => '订单收货',
                                        'url' => 'supply://shop/order/takedelivery',
                                        'is_show' => 0
                                    ],
                                    [
                                        'name' => 'SUPPLY_SHOP_EXPRESS_ORDER_CLOSE',
                                        'title' => '订单关闭',
                                        'url' => 'supply://shop/order/close',
                                        'is_show' => 0
                                    ],
                                    [
                                        'name' => 'SUPPLY_SHOP_EXPRESS_ORDER_PAY',
                                        'title' => '订单支付',
                                        'url' => 'supply://shop/order/pay',
                                        'is_show' => 0
                                    ],
                                    [
                                        'name' => 'SUPPLY_SHOP_EXPRESS_ORDER_PACKAGE',
                                        'title' => '订单物流',
                                        'url' => 'supply://shop/order/package',
                                        'is_show' => 0
                                    ],
                                ]
                            ],

                            [
                                'name' => 'SUPPLY_SHOP_ORDER_REFUND',
                                'title' => '维权列表',
                                'url' => 'supply://shop/orderrefund/lists',
                                'is_show' => 0,
                                'sort' => 5,
                                "child_list" => [
                                    [
                                        'name' => 'SUPPLY_SHOP_REFUND_DETAIL',
                                        'title' => '退款详情',
                                        'url' => 'supply://shop/orderrefund/detail',
                                        'is_show' => 0
                                    ],
                                    [
                                        'name' => 'SUPPLY_SHOP_REFUND_APPLY',
                                        'title' => '申请维权',
                                        'url' => 'supply://shop/orderrefund/apply',
                                        'is_show' => 0
                                    ],
                                    [
                                        'name' => 'SUPPLY_SHOP_REFUND_CANCEL',
                                        'title' => '撤销维权',
                                        'url' => 'supply://shop/orderrefund/detail',
                                        'is_show' => 0
                                    ],
                                    [
                                        'name' => 'SUPPLY_SHOP_REFUND_DELIVERY',
                                        'title' => '维权收货',
                                        'url' => 'supply://shop/orderrefund/delivery',
                                        'is_show' => 0
                                    ],
                                ]
                            ],
                        ],
                    ],


                    [
                        'name' => 'SUPPLY_PURCHASE',
                        'title' => '求购',
                        'url' => 'supply://shop/purchase/purchase',
                        'is_show' => 0,
                        'child_list' => [
                            [
                                'name' => 'SUPPLY_MY_PURCHASE',
                                'title' => '我的求购',
                                'url' => 'supply://shop/purchase/detail',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_MY_PURCHASE_DETAIL',
                                'title' => '求购单详情',
                                'url' => 'supply://shop/purchase/purchaseinfo',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_PURCHASE_RELEASE',
                                'title' => '发布求购单',
                                'url' => 'supply://shop/purchase/release',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_PURCHASE_CLOSE',
                                'title' => '求购单截止',
                                'url' => 'supply://shop/purchase/close',
                                'is_show' => 0,
                            ],
                            [
                                'name' => 'SUPPLY_PURCHASE_DELETE',
                                'title' => '求购单删除',
                                'url' => 'supply://shop/purchase/delete',
                                'is_show' => 0,
                            ],
                        ]
                    ],
                ]
            ],
        ],
    ],
];
