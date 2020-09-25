<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        //短信方式
        'SmsType' => [
            'addon\alisms\event\SmsType'
        ],
        'DoEditSmsMessage' => [
            'addon\alisms\event\DoEditSmsMessage'
        ],
        'SendSms' => [
            'addon\alisms\event\SendSms'
        ]
    ],

    'subscribe' => [
    ],
];
