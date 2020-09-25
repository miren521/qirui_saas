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

namespace addon\topic\event;

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
                    'name' => 'topic',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type' => 'admin',
                    //展示主题
                    'title' => '专题活动',
                    //展示介绍
                    'description' => '专题活动功能',
                    //展示图标
                    'icon' => 'addon/topic/icon.png',
                    //跳转链接
                    'url' => 'topic://admin/topic/lists',
                ]
            ],
            'shop' => [
                [
                    //插件名称
                    'name' => 'topic',
                    //展示分类（根据平台端设置，admin（平台营销），shop：店铺营销，member:会员营销, tool:应用工具）
                    'show_type' => 'admin',
                    //展示主题
                    'title' => '专题活动',
                    //展示介绍
                    'description' => '专题活动功能',
                    //展示图标
                    'icon' => 'addon/topic/icon.png',
                    //跳转链接
                    'url' => 'topic://shop/topic/goodslist',
                ]
            ]

        ];
	    return $data;
	}
}