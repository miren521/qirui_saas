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
 * 活动类型
 */
class PromotionType
{

	public function handle()
	{
	    return ["name" => "批发", "type" => "wholesale"];
	}
}