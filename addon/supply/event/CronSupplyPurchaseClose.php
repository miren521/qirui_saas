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

namespace addon\supply\event;

use addon\supply\model\Purchase;

/**
 * 求购单自动关闭
 */
class CronSupplyPurchaseClose
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        $purchase = new Purchase();
        $result = $purchase->closePurchase([["purchase_id", "=", $data["relate_id"]]]);
        return $result;
	}
	
}