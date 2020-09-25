<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'QINIU_CONFIG',
        'title' => '七牛云上传配置',
        'url' => 'qiniu://admin/config/config',
        'parent' => 'UPLOAD_OSS',
        'is_show' => 0,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 1,
    ],
];
