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

namespace addon\store\model;

use app\model\BaseModel;

class StoreOrder extends BaseModel
{
	
	/**
	 * 基础支付方式(不考虑实际在线支付方式或者货到付款方式)
	 * @var unknown
	 */
	public $pay_type = [
		'OFFLINE_PAY' => '现金'
	];
	
	/**
	 * 获取支付方式
	 */
	public function getPayType()
	{
		//获取订单基础的其他支付方式
		$pay_type = $this->pay_type;
		//获取当前所有在线支付方式
		$onlinepay = event('PayType', [ "app_type" => "pc" ]);
		if (!empty($onlinepay)) {
			foreach ($onlinepay as $k => $v) {
				$pay_type[ $v['pay_type'] ] = $v['pay_type_name'];
			}
		}
		return $pay_type;
	}
	
	
	/**
	 * 订单支付后操作
	 * @param $order
	 */
	public function orderPay($order)
	{
		if (empty($order['delivery_store_id'])) {
			return $this->success();
		}
		model('store')->setInc([ [ 'store_id', '=', $order['delivery_store_id'] ] ], 'order_num');
		model('store')->setInc([ [ 'store_id', '=', $order['delivery_store_id'] ] ], 'order_money', $order['shop_money']);
		model('store_member')->setInc([ [ 'member_id', '=', $order['member_id'] ] ], 'order_num');
		model('store_member')->setInc([ [ 'member_id', '=', $order['member_id'] ] ], 'order_money', $order['shop_money']);
		//门店订单线下付款已结算
		if($order['pay_type'] == 'OFFLINE_PAY')
		{
		    model('order')->update(['shop_money' => $order['shop_money'] + $order['platform_money'], 'platform_money' => 0],[ [ 'order_id', '=', $order['order_id'] ] ]);
		}
		return $this->success();
	}
	
	/**
	 * 门店订单完成后操作
	 * @param $order
	 */
	public function orderComplete($order_id)
	{
		$order_info = model('order')->getInfo([ [ 'order_id', '=', $order_id ] ], '*');
		if (empty($order_info['delivery_store_id'])) {
			return $this->success();
		}
		//门店订单线下付款已结算
		if($order_info['pay_type'] == 'OFFLINE_PAY')
		{
		    model('order')->update(['is_settlement' => 1, 'shop_money' => $order_info['shop_money'] + $order_info['platform_money'], 'platform_money' => 0],[ [ 'order_id', '=', $order_id ] ]);
		}
		model('store')->setInc([ [ 'store_id', '=', $order_info['delivery_store_id'] ] ], 'order_complete_num');
		model('store')->setInc([ [ 'store_id', '=', $order_info['delivery_store_id'] ] ], 'order_complete_money', $order_info['shop_money']);
		model('store_member')->setInc([ [ 'member_id', '=', $order_info['member_id'] ] ], 'order_complete_num');
		model('store_member')->setInc([ [ 'member_id', '=', $order_info['member_id'] ] ], 'order_complete_money', $order_info['shop_money']);

		return $this->success();
	}
	
}