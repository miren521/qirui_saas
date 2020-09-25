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

use app\model\shop\ShopReplay;
/**
 * 店铺续签通过之后任务事件
 */
class CronShopReplay
{
    
	public function handle($data)
	{
	    $shop_replay = new ShopReplay();
	    $res = $shop_replay->cronShopReplay($data['relate_id']);
	    return $res;
	}
	
}