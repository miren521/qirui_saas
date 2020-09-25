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
 * 活动类型
 */
class GoodsPromotionType
{
	
	/**
	 * 活动类型
	 * @return multitype:number unknown
	 */
	public function handle()
	{
		return [ "name" => "砍价", "short" => "砍", "type" => "bargain", "color" => "#F58760", 'url' => 'bargain://shop/bargain/lists' ];
	}
}