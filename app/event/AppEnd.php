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

namespace app\event;
use app\common\model\Visit;
use think\facade\Cache;

/**
 * 应用结束
 */
class AppEnd
{
	// 行为扩展的执行入口必须是run
	public function handle()
	{
	    return success();
		
	}
}