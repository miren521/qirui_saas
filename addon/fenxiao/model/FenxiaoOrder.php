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

namespace addon\fenxiao\model;

use app\model\BaseModel;

/**
 * 分销商品
 */
class FenxiaoOrder extends BaseModel
{
	
	/**
	 * 分销订单计算
	 * @param unknown $order
	 */
	public function calculate($order)
	{
		//获取分销基础配置
		$config_model = new Config();
		$fenxiao_basic_config = $config_model->getFenxiaoBasicsConfig();
		$level_config = $fenxiao_basic_config['data']['value']['level'];
		if (empty($level_config)) {
			return $this->success();
		}
		//检测分销商上级关系
		$member_info = model("member")->getInfo([ [ 'member_id', '=', $order['member_id'] ] ], 'fenxiao_id');
		//如果没有分销商直接返回不计算,没有考虑首次付款上下级绑定
		if ($member_info['fenxiao_id'] == 0) {
			return $this->success();
		}
		$fenxiao_id = $member_info['fenxiao_id'];
		$fenxiao_info = model("fenxiao")->getInfo([ [ 'fenxiao_id', '=', $fenxiao_id ] ]);
		if (empty($fenxiao_info)) {
			return $this->success();
		}
		//判断几级分销
		$parent_fenxiao_info = $level_config >= 2 ? model('fenxiao')->getInfo([ [ 'fenxiao_id', '=', $fenxiao_info['parent'] ] ], 'fenxiao_id, fenxiao_name, status, parent') : [];
		$grand_parent_fenxiao_info = $level_config >= 3 && !empty($parent_fenxiao_info['parent']) ? model('fenxiao')->getInfo([ [ 'fenxiao_id', '=', $parent_fenxiao_info['parent'] ] ], 'fenxiao_id, fenxiao_name, status') : [];
		$order_goods = model("order_goods")->getList([ [ 'order_id', '=', $order['order_id'] ] ], 'order_goods_id, goods_id, sku_id, sku_name, sku_image, sku_no, is_virtual, price, cost_price, num, goods_money, cost_money, delivery_no, delivery_status, real_goods_money, shop_money, platform_money');
		model('fenxiao_order')->delete([ [ 'order_id', '=', $order['order_id'] ] ]);
		//获取分销等级
		foreach ($order_goods as $k => $v) {
			//商品信息管理
			$goods_info = model("goods")->getInfo([ [ 'goods_id', '=', $v['goods_id'] ] ], 'is_fenxiao, fenxiao_type');
			if ($goods_info['is_fenxiao'] != 1) {
				continue;
			}
			$commission = 0;
			$commission_rate = 0;
			$order_fenxiao_data = [];
			
			$fenxiao_level = model('fenxiao_goods_sku')->getInfo([ [ 'goods_id', '=', $v['goods_id'] ], [ 'sku_id', '=', $v['sku_id'] ], [ 'level_id', '=', $fenxiao_info['level_id'] ] ]);
			if ($fenxiao_info['status'] == 1) {
				if ($fenxiao_level['one_rate'] > 0) {
					$commission_rate += $order_fenxiao_data['one_rate'] = $fenxiao_level['one_rate'];
					$commission += $order_fenxiao_data['one_commission'] = $fenxiao_level['one_rate'] * $v['real_goods_money'] / 100;
				} else {
					$commission_rate += $order_fenxiao_data['one_rate'] = round($fenxiao_level['one_money'] * $v['num'] / $v['real_goods_money'], 2);
					$commission += $order_fenxiao_data['one_commission'] = $fenxiao_level['one_money'] * $v['num'];
				}
			}
			if (!empty($parent_fenxiao_info) && $parent_fenxiao_info['status'] == 1) {
				if ($fenxiao_level['two_rate'] > 0) {
					$commission_rate += $order_fenxiao_data['two_rate'] = $fenxiao_level['two_rate'];
					$commission += $order_fenxiao_data['two_commission'] = $fenxiao_level['two_rate'] * $v['real_goods_money'] / 100;
				} else {
					$commission_rate += $order_fenxiao_data['two_rate'] = round($fenxiao_level['two_money'] * $v['num'] / $v['real_goods_money'], 2);
					$commission += $order_fenxiao_data['two_commission'] = $fenxiao_level['two_money'] * $v['num'];
				}
			}
			if (!empty($grand_parent_fenxiao_info) && $grand_parent_fenxiao_info['status'] == 1) {
				if ($fenxiao_level['three_rate'] > 0) {
					$commission_rate += $order_fenxiao_data['three_rate'] = $fenxiao_level['three_rate'];
					$commission += $order_fenxiao_data['three_commission'] = $fenxiao_level['three_rate'] * $v['real_goods_money'] / 100;
				} else {
					$commission_rate += $order_fenxiao_data['three_rate'] = round($fenxiao_level['three_money'] * $v['num'] / $v['real_goods_money'], 2);
					$commission += $order_fenxiao_data['three_commission'] = $fenxiao_level['three_money'] * $v['num'];
				}
			}
			
			//启动分销
			$data = [
				'order_id' => $order['order_id'],
				'order_no' => $order['order_no'],
				'order_goods_id' => $v['order_goods_id'],
				'site_id' => $order['site_id'],
				'site_name' => $order['site_name'],
				'goods_id' => $v['goods_id'],
				'sku_id' => $v['sku_id'],
				'sku_name' => $v['sku_name'],
				'sku_image' => $v['sku_image'],
				'price' => $v['price'],
				'num' => $v['num'],
				'real_goods_money' => $v['real_goods_money'],
				'member_id' => $order['member_id'],
				'member_name' => $order['name'],
				'member_mobile' => $order['mobile'],
				'full_address' => $order['full_address'] . $order['address'],
				'commission' => $commission,
				'commission_rate' => $commission_rate,
				'one_fenxiao_id' => $fenxiao_info['fenxiao_id'],
				'one_rate' => $order_fenxiao_data['one_rate'],
				'one_commission' => $order_fenxiao_data['one_commission'],
				'one_fenxiao_name' => $fenxiao_info['fenxiao_name'],
				'two_fenxiao_id' => !empty($parent_fenxiao_info) ? $parent_fenxiao_info['fenxiao_id'] : 0,
				'two_rate' => empty($order_fenxiao_data['two_rate']) ? 0 : $order_fenxiao_data['two_rate'],
				'two_commission' => empty($order_fenxiao_data['two_commission']) ? 0 : $order_fenxiao_data['two_commission'],
				'two_fenxiao_name' => empty($parent_fenxiao_info) ? '' : $parent_fenxiao_info['fenxiao_name'],
				'three_fenxiao_id' => empty($grand_parent_fenxiao_info) ? '' : $grand_parent_fenxiao_info['fenxiao_id'],
				'three_rate' => empty($order_fenxiao_data['three_rate']) ? 0 : $order_fenxiao_data['three_rate'],
				'three_commission' => empty($order_fenxiao_data['three_commission']) ? 0 : $order_fenxiao_data['three_commission'],
				'three_fenxiao_name' => empty($grand_parent_fenxiao_info) ? '' : $grand_parent_fenxiao_info['fenxiao_name'],
			];
			model("fenxiao_order")->add($data);
		}
		// 分销商检测升级
		event('FenxiaoUpgrade', $member_info['fenxiao_id']);
		
		return $this->success();
	}
	
	/**
	 * 订单退款
	 * @param $order_goods_id
	 * @return array
	 */
	public function refund($order_goods_id)
	{
		$res = model("fenxiao_order")->update([ 'is_refund' => 1 ], [ [ 'order_goods_id', '=', $order_goods_id ] ]);
		return $this->success($res);
	}
	
	/**
	 * 订单结算
	 * @param $order_id
	 */
	public function settlement($order_id)
	{
		//获取未退款的和未结算的分销订单
		$fenxiao_orders = model("fenxiao_order")->getList([ [ 'order_id', '=', $order_id ], [ 'is_settlement', '=', 0 ], [ 'is_refund', '=', 0 ] ], '*');
		//同时修改分销订单状态为已结算
		model("fenxiao_order")->startTrans();
		try {
			$fenxiao_account = new FenxiaoAccount();
			model('fenxiao_order')->update([ 'is_settlement' => 1 ], [ [ 'order_id', '=', $order_id ] ]);
			$time = time();
			$commission = 0;
			foreach ($fenxiao_orders as $fenxiao_order) {
				$commission += $fenxiao_order['one_commission'];
				$fenxiao_account->addAccount($fenxiao_order['one_fenxiao_id'], $fenxiao_order['one_fenxiao_name'], 'order', $fenxiao_order['one_commission'], $fenxiao_order['fenxiao_order_id']);
				model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order['one_fenxiao_id'] ] ], 'total_commission', $fenxiao_order['one_commission']);
				
				if ($fenxiao_order['two_commission'] > 0) {
					$commission += $fenxiao_order['two_commission'];
					$fenxiao_account->addAccount($fenxiao_order['two_fenxiao_id'], $fenxiao_order['two_fenxiao_name'], 'order', $fenxiao_order['two_commission'], $fenxiao_order['fenxiao_order_id']);
					model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order['two_fenxiao_id'] ] ], 'total_commission', $fenxiao_order['two_commission']);
				}
				if ($fenxiao_order['three_commission'] > 0) {
					$commission += $fenxiao_order['three_commission'];
					$fenxiao_account->addAccount($fenxiao_order['three_fenxiao_id'], $fenxiao_order['three_fenxiao_name'], 'order', $fenxiao_order['three_commission'], $fenxiao_order['fenxiao_order_id']);
					model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order['three_fenxiao_id'] ] ], 'total_commission', $fenxiao_order['three_commission']);
				}
			}
			//增加订单佣金结算
			model('order')->setInc([ [ 'order_id', '=', $order_id ] ], 'commission', $commission);
			model("fenxiao_order")->commit();
			return $this->success();
		} catch (\Exception $e) {
			model("fenxiao_order")->rollback();
			return $this->error($e->getMessage());
		}
	}
	
	/**
	 * calculateOrder 计算对应分销商的第一次订单数量&&金额
	 * @param $order_id
	 */
	public function calculateOrder($order_id)
	{
		$fenxiao_order_info = model('fenxiao_order')->getFirstData([ [ 'order_id', '=', $order_id ] ], 'one_fenxiao_id');
		if (!empty($fenxiao_order_info)) {
			$one_commission_sum = model('fenxiao_order')->getSum([ [ 'order_id', '=', $order_id ] ], 'one_commission');
			model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order_info['one_fenxiao_id'] ] ], 'one_fenxiao_order_num');
			model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_order_info['one_fenxiao_id'] ] ], 'one_fenxiao_order_money', $one_commission_sum);
			// 分销商检测升级
			event('FenxiaoUpgrade', $fenxiao_order_info['one_fenxiao_id']);
		}
		return $this->success();
	}
	
	/**
	 * 获取分销订单列表
	 * @param unknown $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 */
	public function getFenxiaoOrderPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'fo.order_id DESC')
	{
		$field = '
        fo.fenxiao_order_id,fo.order_no,fo.site_id,fo.site_name,fo.sku_id,fo.sku_name,fo.sku_image,fo.price,fo.num,fo.real_goods_money,fo.member_name,
        fo.member_mobile,fo.one_fenxiao_name,fo.is_settlement,fo.commission,fo.is_refund,
        o.order_status_name,o.create_time,fo.one_fenxiao_id,fo.two_fenxiao_id,fo.three_fenxiao_id,fo.one_commission,fo.two_commission,fo.three_commission
        ';
		
		$alias = 'fo';
		$join = [
			[
				'order o',
				'fo.order_id = o.order_id',
				'inner'
			]
		];
		$list = model('fenxiao_order')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
	/**
	 * 订单详情
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getFenxiaoOrderDetail($condition, $field = '*')
	{
		$fenxiao_order_info = model('fenxiao_order')->getInfo($condition, $field);
		return $this->success($fenxiao_order_info);
	}
}