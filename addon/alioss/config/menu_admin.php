<?php
// +----------------------------------------------------------------------
// | 平台端菜单设置
// +----------------------------------------------------------------------
return [
    [
        'name' => 'ALIOSS_CONFIG',
        'title' => '阿里云OSS上传配置',
        'url' => 'alioss://admin/config/config',
        'parent' => 'UPLOAD_OSS',
        'is_show' => 0,
        'is_control' => 1,
        'is_icon' => 0,
        'picture' => '',
        'picture_select' => '',
        'sort' => 1,
    ],
];
