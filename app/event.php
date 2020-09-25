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
    'bind' => [

    ],

    'listen' => [

        /**
         * 系统基础事件
         * 完成系统基础化操作执行
         */
        //应用初始化事件
        'AppInit' => [
            'app\event\InitConfig',
            'app\event\InitRoute',
            'app\event\InitAddon',
            'app\event\InitCron',

        ],
        'HttpRun' => [],
        'HttpEnd' => [],
        'LogLevel' => [],
        'LogWrite' => [],

        /**
         * 营销活动查询事件
         * 用于添加到对应营销活动展示
         */
        //营销活动
        'ShowPromotion' => [
            'app\event\ShowPromotion'
        ],

        /**
         * 店铺相关事件
         * 完成店铺相关功能操作
         */
        //添加店铺账户数据
        'AddShopAccount' => [],
        //添加店铺事件
        'AddShop' => [
            'app\event\AddShopDiyView',//增加默认自定义数据：网站主页、底部导航
        ],
        'ShopClose' => [
            'app\event\ShopClose'
        ],
        'CronShopClose' => [
            'app\event\CronShopClose'
        ],
        /**
         * 会员相关事件
         *完成会员相关功能操作调用
         */
        //添加会员账户数据
        'AddMemberAccount' => [
            'app\event\UpdateMemberLevel',//会员账户变化检测会员等级
        ],
        //会员行为事件
        'MemberAction' => [],
        //会员营销活动标志
        'MemberPromotion' => [],
        //会员注册后执行事件
        'MemberRegister' => [
            'app\event\MemberRegister'
        ],
        'MemberLogin' => [
            'app\event\MemberLogin'
        ],


        /**
         * 支付功能事件
         * 对应支付相关功能调用
         */
        //支付异步回调(支付插件完成，作用判定支付成功，返回对应支付编号)
        'PayNotify' => [

        ],

        /**
         * 订单功能事件
         * 完成订单相关操作调用
         */
        //订单支付异步执行
        'OrderPayNotify' => [
            'app\event\OrderPayNotify',//商城订单支付异步回调
        ],
        //订单创建后执行事件
        'OrderCreate' => [
            'app\event\OrderCreateShopMember',  //创建订单后添加店铺关注
        ],
        'OrderPay' => [
            'app\event\ShopOrderCalc',  //订单支付后店铺计算订单佣金相关

        ],  //订单支付成功后执行事件
        'OrderDelivery' => [], //订单发货
        'orderTakeDelivery' => [], //订单收货
        'OrderComplete' => [
        ],  //订单完成后执行事件
        'OrderClose' => [], //订单关闭后执行事件
        'OrderRefundFinish' => [
            'app\event\ShopOrderRefundCalc'
        ],//订单项完成退款操作之后
        //核销类型
        'VerifyType' => [
        ],
        //核销
        'Verify' => [
            'app\event\PickupOrderVerify',//自提订单核销
            'app\event\VirtualGoodsVerify',//虚拟商品核销
        ],
        //执行店铺续签申请后店铺入驻时间续期
        'CronShopRelpay' => [
            'app\event\CronShopRelpay'
        ],
        'CronOrderClose' => [
            'app\event\CronOrderClose'
        ],
        'CronOrderTakeDelivery' => [
            'app\event\CronOrderTakeDelivery'
        ],
        //自动执行订单自动完成
        'CronOrderComplete' => [
            'app\event\CronOrderComplete'
        ],

        /**
         * 自定义模板事件
         * 自定义模板展示调用相关功能
         */
        //自定义模板
        'DiyViewUtils' => [
            'app\event\DiyViewUtils',//自定义组件
        ],
        'DiyViewEdit' => [
            'app\event\DiyViewEdit',//自定义页面编辑
        ],

        // 微页面

        // 推广链接

        // 链接入口

        // 底部导航

        /**
         * 物流公司
         */
        //物流跟踪
        'Trace' => [
            'app\event\Kd100Trace',//快递100 物流查询
            'app\event\KdbirdTrace'//快递鸟物流查询
        ],
        'CloseTrace' => [
            'app\event\CloseKd100Trace',//快递100 物流查询关闭
            'app\event\CloseKdbirdTrace'//快递鸟物流查询关闭
        ],

        /**
         * 消息发送
         */
        //消息模板
        'SendMessageTemplate' => [
            // 订单创建
            'app\event\MessageOrderCreate',
            // 订单关闭
            'app\event\MessageOrderClose',
            // 订单完成
            'app\event\MessageOrderComplete',
            // 订单支付
            'app\event\MessageOrderPaySuccess',
            // 订单发货
            'app\event\MessageOrderDelivery',
            // 订单收货
            'app\event\MessageOrderReceive',

            // 商家同意退款
            'app\event\MessageShopRefundAgree',
            // 商家拒绝退款
            'app\event\MessageShopRefundRefuse',
            // 核销通知
            'app\event\MessageShopVerified',

            // 注册验证
            'app\event\MessageRegisterCode',
            // 注册成功
            'app\event\MessageRegisterSuccess',
            // 找回密码
            'app\event\MessageFindCode',
            // 会员登陆成功
            'app\event\MessageLogin',
            // 帐户绑定验证码
            'app\event\MessageBindCode',
            // 动态码登陆验证码
            'app\event\MessageLoginCode',
            // 支付密码修改通知
            'app\event\MessageMemberPayPassword',

            // 买家发起退款提醒
            'app\event\MessageOrderRefundApply',
            // 买家已退货提醒
            'app\event\MessageOrderRefundDelivery',
            // 设置密码
            'app\event\MessageSetPassWord',

        ],
        //发送短信
        'sendSms' => [

        ],

        'Qrcode' => [
            'app\event\Qrcode'
        ],

        //店铺周期结算
        'ShopWithdrawPeriodCalc' => [
            'app\event\ShopWithdrawPeriodCalc',
        ],

        //PC默认导航
        'InitPcNav' => [
            'app\event\InitPcNav',
        ],

        //默认广告
        'initAdv' => [
            'app\event\initAdv',
        ],

    ],

    'subscribe' => [
    ],
];
