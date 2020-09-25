<?php
// 事件定义文件
return [
    'bind'      => [

    ],

    'listen'    => [
        //展示活动
        'ShowPromotion' => [
            'addon\memberrecharge\event\ShowPromotion',
        ],
        //订单支付回调事件
        'MemberRechargeOrderPayNotify' => [
            'addon\memberrecharge\event\MemberRechargeOrderPayNotify',
        ],
        //关闭订单事件
        'MemberRechargeOrderClose' => [
            'addon\memberrecharge\event\MemberRechargeOrderClose',
        ],

        'MemberAccountFromType' => [
            'addon\memberrecharge\event\MemberAccountFromType',
        ],

        'MemberAccountRule' => [
            'addon\memberrecharge\event\MemberAccountRule',
        ],
    ],

    'subscribe' => [
    ],
];
