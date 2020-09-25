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

namespace addon\manjian\event;

use addon\manjian\model\Manjian;
/**
 * 启动活动
 */
class OpenManjian
{

	public function handle($params)
	{
	    $manjian = new Manjian();
	    $res = $manjian->cronOpenManjian($params['relate_id']);
        return $res;
	}
}