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

namespace addon\wholesale\event;

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
                    'name' => 'wholesale',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type' => 'tool',
                    //展示主题
                    'title' => '批发',
                    //展示介绍
                    'description' => '批发',
                    //展示图标
                    'icon' => 'addon/wholesale/icon.png',
                    //跳转链接
                    'url' => 'wholesale://admin/wholesale/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name' => 'wholesale',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'tool',
                    //展示主题
                    'title' => '批发',
                    //展示介绍
                    'description' => '批发活动',
                    //展示图标
                    'icon' => 'addon/wholesale/icon.png',
                    //跳转链接
                    'url' => 'wholesale://shop/wholesale/lists',
                ]
            ]

        ];
	    return $data;
	}
}