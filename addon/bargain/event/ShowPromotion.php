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


namespace addon\bargain\event;

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
            'shop' => [
                [
                    //插件名称
                    'name' => 'bargain',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'shop',
                    //展示主题
                    'title' => '砍价',
                    //展示介绍
                    'description' => '砍价活动',
                    //展示图标
                    'icon' => 'addon/bargain/icon.png',
                    //跳转链接
                    'url' => 'bargain://shop/bargain/lists',
                ]
            ],
            'admin' => [
                [
                    //插件名称
                    'name' => 'bargain',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'shop',
                    //展示主题
                    'title' => '砍价',
                    //展示介绍
                    'description' => '砍价活动',
                    //展示图标
                    'icon' => 'addon/bargain/icon.png',
                    //跳转链接
                    'url' => 'bargain://admin/bargain/lists',
                ]
            ]

        ];
	    return $data;
	}
}