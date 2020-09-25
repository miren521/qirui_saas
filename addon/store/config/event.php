<?php
/**
 * KirySaaS--------||bai T o o Y ||
 * =========================================================
 * ----------------------------------------------
 * User Mack Qin
 * Copy right 2019-2029 kiry 保留所有权利。
 * ----------------------------------------------
 * =========================================================
 */

return [
    'bind'      => [
        
    ],

    'listen'    => [
        'OrderCreate' => [
            'addon\store\event\OrderCreate',
        ],
        //订单关闭
        'OrderClose' => [
            'addon\store\event\OrderClose',
        ],
        //订单完成
        'OrderComplete' => [
            'addon\store\event\OrderComplete',
        ],
        //订单支付
        'OrderPay' => [
            'addon\store\event\OrderPay',
        ],
        //门店结算
        'StoreWithdrawPeriodCalc' => [
            'addon\store\event\StoreWithdrawPeriodCalc'
        ],
        //门店添加
        'AddShop' => [
            'addon\store\event\AddShop'
        ],
    ],

    'subscribe' => [
    ],
];
