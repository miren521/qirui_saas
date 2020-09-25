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
            'title' => '微信圈子',
            'description' => '朋友间的好物分享',
            'url' => 'goodscircle://admin/config/index',
            'icon' => 'addon/goodscircle/icon.png'
        ];
	    return $data;
	}
}