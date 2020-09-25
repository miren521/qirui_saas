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



namespace addon\egg\event;

/**
 * 活动类型
 */
class GoodsPromotionType
{
    /**
     * 活动类型
     * @return array
     */
	public function handle()
	{
		return ["name" => "砸金蛋", "short" => "", "type" => "egg", "color" => "#4CB130", 'url' => 'egg://shop/egg/lists'];
	}
}