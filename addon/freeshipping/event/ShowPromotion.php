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



namespace addon\freeshipping\event;

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
                    'name' => 'freeshipping',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'shop',
                    //展示主题
                    'title' => '满额包邮',
                    //展示介绍
                    'description' => '满额包邮',
                    //展示图标
                    'icon' => 'addon/freeshipping/icon.png',
                    //跳转链接
                    'url' => 'freeshipping://shop/freeshipping/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name' => 'freeshipping',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'shop',
                    //展示主题
                    'title' => '满额包邮',
                    //展示介绍
                    'description' => '满额包邮',
                    //展示图标
                    'icon' => 'addon/freeshipping/icon.png',
                    //跳转链接
                    'url' => 'freeshipping://shop/freeshipping/lists',
                ]
            ]

        ];
	    return $data;
	}
}