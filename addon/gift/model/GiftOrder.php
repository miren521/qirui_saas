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

namespace addon\gift\model;

use app\model\BaseModel;

/**
 * 礼品订单
 */
class GiftOrder extends BaseModel
{
	/**
	 * 添加礼品订单(注意减少库存)
	 * @param unknown $data
	 */
	public function addOrder($data, $is_stock = 0)
	{
		$res = model("promotion_gift_order")->add($data);
		if ($res && $is_stock == 0) {
			model("gift")->setDec([ [ 'gift_id', '=', $data['gift_id'] ] ], 'stock', 1);
		}
		return $this->success($res);
	}
	
	/**
	 * 编辑订单数据(发货)
	 * @param unknown $data
	 * @param unknown $order_id
	 */
	public function editOrder($data, $order_id)
	{
		$res = model("promotion_gift_order")->update($data, [ [ 'order_id', '=', $order_id ] ]);
		return $this->success($res);
	}
	
	/**
	 * 获取礼品信息
	 * @param array $condition
	 * @param string $field
	 */
	public function getOrderInfo($condition, $field = '*')
	{
		$res = model('promotion_gift_order')->getInfo($condition, $field);
		return $this->success($res);
	}
	
	/**
	 * 获取订单列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getOrderList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('promotion_gift_order')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取订单分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getOrderPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$list = model('promotion_gift_order')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	
}