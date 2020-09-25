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



namespace addon\goodscircle\event;

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
                    'name' => 'goodscircle',
                    //店铺端展示分类  shop:营销活动   member:互动营销
                    'show_type' => 'tool',
                    //展示主题
                    'title' => '微信圈子',
                    //展示介绍
                    'description' => '朋友间的好物分享',
                    //展示图标
                    'icon' => 'addon/goodscircle/icon.png',
                    //跳转链接
                    'url' => 'goodscircle://admin/config/index',
                ]
            ]
        ];
	    return $data;
	}
}