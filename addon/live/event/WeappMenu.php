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



namespace addon\live\event;

/**
 * 小程序菜单
 */
class WeappMenu
{

    /**
     * 小程序菜单
     * 
     * @return multitype:number unknown
     */
	public function handle()
	{
        $data = [
            'title' => '小程序直播',
            'description' => '实现直播互动与商品销售闭环',
            'url' => 'live://admin/room/index',
            'icon' => 'addon/live/icon.png'
        ];
	    return $data;
	}
}