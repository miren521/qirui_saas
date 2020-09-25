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

namespace addon\cards\event;

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
                    'name' => 'cards',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'member',
                    //展示主题
                    'title' => '刮刮乐',
                    //展示介绍
                    'description' => '刮刮乐活动',
                    //展示图标
                    'icon' => 'addon/cards/icon.png',
                    //跳转链接
                    'url' => 'cards://admin/cards/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name' => 'cards',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'member',
                    //展示主题
                    'title' => '刮刮乐',
                    //展示介绍
                    'description' => '刮刮乐活动',
                    //展示图标
                    'icon' => 'addon/cards/icon.png',
                    //跳转链接
                    'url' => 'cards://shop/cards/lists',
                ]
            ]

        ];
	    return $data;
	}
}