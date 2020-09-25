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


declare(strict_types=1);

namespace addon\servicer\event;

/**
 * 活动展示
 */
class ShowPromotion
{
    /**
     * 活动展示
     * @return array
     */
    public function handle()
    {
        $data = [
            'admin' => [
                [
                    //插件名称
                    'name' => 'servicer',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type' => 'tool',
                    //展示主题
                    'title' => '客服',
                    //展示介绍
                    'description' => '客服在线服务',
                    //展示图标
                    'icon' => 'addon/servicer/icon.png',
                    //跳转链接
                    'url' => 'servicer://admin/servicer/index',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name' => 'servicer',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type' => 'tool',
                    //展示主题
                    'title' => '客服',
                    //展示介绍
                    'description' => '客服在线服务',
                    //展示图标
                    'icon' => 'addon/servicer/icon.png',
                    //跳转链接
                    'url' => 'servicer://shop/servicer/index',
                ]
            ]
        ];
        return $data;
    }
}
