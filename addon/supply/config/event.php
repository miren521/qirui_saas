<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        /**
         * 订单功能事件
         * 完成订单相关操作调用
         */
        //订单支付异步执行
        'SupplyOrderPayNotify' => [
            'addon\supply\event\SupplyOrderPayNotify',//商城订单支付异步回调
        ],
        //订单创建后执行事件
        'SupplyOrderCreate' => [
            //todo 创建订单后添加供应商关注(主体店铺)
        ],
        'SupplyOrderPay' => [
            'addon\supply\event\SupplyOrderCalc',  //订单支付后店铺计算订单佣金相关

        ],  //订单支付成功后执行事件
        'SupplyOrderDelivery' => [], //订单发货
        'SupplyOrderTakeDelivery' => [], //订单收货
        'SupplyOrderComplete' => [
        ],  //订单完成后执行事件
        'SupplyOrderClose' => [], //订单关闭后执行事件

        //执行店铺续签申请后店铺入驻时间续期
//        'CronSupplyShopRelpay' => [
//            'addon\supply\event\CronSupplyShopRelpay'
//        ],
        'CronSupplyOrderClose' => [
            'addon\supply\event\CronSupplyOrderClose'
        ],
        'CronSupplyOrderTakeDelivery' => [
            'addon\supply\event\CronSupplyOrderTakeDelivery'
        ],
        //自动执行订单自动完成
        'CronSupplyOrderComplete' => [
            'addon\supply\event\CronSupplyOrderComplete'
        ],

        'SupplyOrderRefundFinish' => [
            'addon\supply\event\SupplyOrderRefundCalc'
        ],//订单项完成退款操作之后


        'SupplyPeriodCalc' => [
            'addon\supply\event\SupplyPeriodCalc',  //订单支付后店铺计算订单佣金相关

        ],
        //供应商关闭
        'SupplyClose' => [
            'app\event\SupplyClose'
        ],
        //自动事件检测供应商是否过期
        'CronSupplyClose' => [
            'app\event\CronSupplyClose'
        ],
    ],

    'subscribe' => [
    ],
];
