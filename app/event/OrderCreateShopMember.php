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

use app\model\order\Order as OrderModel;
use app\model\shop\ShopMember as ShopMemberModel;

/**
 * 创建订单后添加店铺关注
 */
class OrderCreateShopMember
{
	/**
	 * 传入订单信息
	 * @param unknown $data
	 */
	public function handle($data)
	{
		$order_model = new OrderModel();
		$order_info = $order_model->getOrderInfo([ [ 'order_id', '=', $data['order_id'] ] ], 'site_id,member_id');
		$order_info = $order_info['data'];
		if (!empty($order_info)) {
			//添加店铺关注记录
			$shop_member_model = new ShopMemberModel();
			$res = $shop_member_model->addShopMember($order_info['site_id'], $order_info['member_id'], 0);
			return $res;
		}
	}
	
}