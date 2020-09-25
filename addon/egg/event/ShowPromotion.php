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



namespace addon\egg\event;

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
                    'name' => 'egg',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'member',
                    //展示主题
                    'title' => '砸金蛋',
                    //展示介绍
                    'description' => '砸金蛋活动',
                    //展示图标
                    'icon' => 'addon/egg/icon.png',
                    //跳转链接
                    'url' => 'egg://admin/egg/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name' => 'egg',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'member',
                    //展示主题
                    'title' => '砸金蛋',
                    //展示介绍
                    'description' => '砸金蛋活动',
                    //展示图标
                    'icon' => 'addon/egg/icon.png',
                    //跳转链接
                    'url' => 'egg://shop/egg/lists',
                ]
            ]

        ];
	    return $data;
	}
}