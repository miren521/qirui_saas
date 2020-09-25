<?php

/**
 * 数据库连接配置
 * 1.客服需要单独配置数据库，文件路径 addon/servicer/gateway/Applications/Service/config.php
 * 2.客服插件使用，需要启动Workerman服务（需要PHP环境5.6及以上）
 * 3.启动客服服务：
 *  Linux环境下，命令： [php路径]/bin/php [项目路径]/addon/servicer/gateway/start.php start -d
 *  Windows环境下，双击启动： addon/servicer/gateway/start_for_win.bat
 */
return [
    // 数据库连接（已弃用， 请到同目录Events.php中配置数据库）
    'db' => [
        // 连接地址
        'host'   => '',
        // 端口
        'port'   => '',
        // 用户名
        'user'   => '',
        // 密码
        'passwd' => '',
        // 表前缀
        'prefix' => '',
        // 数据库名称
        'dbname' => ''
    ],
    'ssl' => [
        'cert' => 'server.pem', // 无需设置
        'key' => 'server.key', // 无需设置
        'enable' => false
    ],
    'ping' => [
        'interval' => 60, // 心跳死亡间隔为180秒
    ]
];
