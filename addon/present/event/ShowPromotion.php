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

declare (strict_types = 1);

namespace addon\present\event;

/**
 * 活动展示
 */
class ShowPromotion
{

    /**
     * 活动展示
     * 
     * @return multitype:number unknown
     */
	public function handle()
	{
        $data = [
            'admin' => [
                [
                    //插件名称
                    'name' => 'present',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type' => 'shop',
                    //展示主题
                    'title' => '赠品',
                    //展示介绍
                    'description' => '赠品活动',
                    //展示图标
                    'icon' => 'addon/present/icon.png',
                    //跳转链接
                    'url' => 'present://admin/present/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name' => 'present',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'shop',
                    //展示主题
                    'title' => '赠品',
                    //展示介绍
                    'description' => '赠品活动',
                    //展示图标
                    'icon' => 'addon/present/icon.png',
                    //跳转链接
                    'url' => 'present://shop/present/lists',
                ]
            ]

        ];
	    return $data;
	}
}