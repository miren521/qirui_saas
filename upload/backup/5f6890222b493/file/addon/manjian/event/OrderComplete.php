<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */


namespace addon\manjian\event;

use addon\manjian\model\Order;

/**
 * 订单完成
 */
class OrderComplete
{

	public function handle($params)
	{
	    if (isset($params['order_id'])) {
            $order = new Order();
            $result = $order->orderComplete($params['order_id']);
            return $result;
        }
	}
}