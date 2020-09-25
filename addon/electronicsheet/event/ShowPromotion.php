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



namespace addon\electronicsheet\event;

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
                    'name' => 'electronicsheet',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'tool',
                    //展示主题
                    'title' => '电子面单',
                    //展示介绍
                    'description' => '物流电子面单打印',
                    //展示图标
                    'icon' => 'addon/electronicsheet/icon.png',
                    //跳转链接
                    'url' => 'electronicsheet://shop/electronicsheet/lists',
                ]
            ]

        ];
	    return $data;
	}
}