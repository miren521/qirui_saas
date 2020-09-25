<?php
// 事件定义文件
return [
    'bind'      => [
        
    ],

    'listen'    => [
        //短信方式
        'OssType' => [
            'addon\qiniu\event\OssType'
        ],
        'Put' => [
            'addon\qiniu\event\Put'
        ],
        'CloseOss' => [
            'addon\qiniu\event\CloseOss'
        ],
    ],

    'subscribe' => [
    ],
];
