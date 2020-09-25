<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
declare (strict_types = 1);

namespace app\event;

use app\model\shop\ShopSettlement;
use Carbon\Carbon;
/**
 * 店铺账期转账
 */
class ShopWithdrawPeriodCalc
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $model = new ShopSettlement();
        $time = Carbon::today()->timestamp;

        $res = $model->shopSettlement($time);

        return $res;
	}
	
}