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

namespace addon\pintuan\model;

use app\model\BaseModel;
use app\model\member\Member as MemberModel;
use app\model\order\OrderCommon;

/**
 * 拼团订单
 */
class PintuanOrder extends BaseModel
{
	
	/**
	 * 开团/参团
	 * @param $order
	 * @param int $group_id
	 * @param $pintuan_id
	 * @return array|\multitype
	 */
	public function addPintuanOrder($order, $group_id = 0, $pintuan_id)
	{
		//获取用户头像
		$member_model = new MemberModel();
		$member_info = $member_model->getMemberInfo([ [ 'member_id', '=', $order['member_id'] ] ], 'headimg,nickname');
		//获取拼团信息
		$pintuan_model = new Pintuan();
		$pintuan = $pintuan_model->getPintuanInfo([ [ 'pintuan_id', '=', $pintuan_id ] ], 'status');
		$pintuan_info = $pintuan['data'];
		//判断拼团活动状态
		if ($pintuan_info['status'] != 1) {
			return $this->error('', '该拼团活动已结束');//该拼团活动已结束
		}
		//判断是开团还是拼团
		if ($group_id) {//拼团
			//拼团组信息
			$pintuan_group_model = new PintuanGroup();
			$pintuan_group = $pintuan_group_model->getPintuanGroupInfo(
				[ [ 'group_id', '=', $group_id ] ], 'group_id,head_id,pintuan_num,pintuan_count,status'
			);
			
			$pintuan_group_info = $pintuan_group['data'];
			
			if ($pintuan_group_info['head_id'] == $order['member_id']) {
				return $this->error('', '抱歉，您不能参与自己的团');
			}
			
			if ($pintuan_group_info['status'] != 2) {
				return $this->error('', '该拼团组已失效');
			}
			if ($pintuan_group_info['pintuan_num'] == $pintuan_group_info['pintuan_count']) {
				return $this->error('', '该拼团组已满员，请参加别的拼团或自己开团');
			}
			$pintuan_order_data = [
				'pintuan_id' => $pintuan_id,
				'order_id' => $order['order_id'],
				'order_no' => $order['order_no'],
				'group_id' => $pintuan_group_info['group_id'],
				'order_type' => $order['order_type'],
				'head_id' => $pintuan_group_info['head_id'],
				'member_id' => $order['member_id'],
				'member_img' => $member_info['data']['headimg'],
				'nickname' => $member_info['data']['nickname'],
				'pintuan_status' => 0
			];
			$res = model('promotion_pintuan_order')->add($pintuan_order_data);
		} else {//开团
			$pintuan_order_data = [
				'pintuan_id' => $pintuan_id,
				'order_id' => $order['order_id'],
				'order_no' => $order['order_no'],
				'group_id' => 0,
				'order_type' => $order['order_type'],
				'head_id' => $order['member_id'],
				'member_id' => $order['member_id'],
				'member_img' => $member_info['data']['headimg'],
				'nickname' => $member_info['data']['nickname'],
				'pintuan_status' => 0
			];
			$res = model('promotion_pintuan_order')->add($pintuan_order_data);
			
		}
		return $this->success($res);
	}
	
	/**
	 * @param unknown $data
	 */
	public function orderPay($order)
	{
		model('promotion_pintuan_order')->startTrans();
		try {
            //拼团订单在未成功之前，禁止发退款
            model('order')->update(['is_enable_refund' => 0], [['order_id', '=', $order['order_id']]]);

			//支付操作查询拼团订单，如果group_id=0,创建组，else，检测成团
			
			//获取拼团订单信息
			$pintuan_order = $this->getPintuanOrderInfo([ [ 'order_id', '=', $order['order_id'] ] ]);
			$pintuan_order_info = $pintuan_order['data'];

			//todo  先锁定订单
            $order_common_model = new OrderCommon();
            $local_result = $order_common_model->orderLock($order['order_id']);
            if ($local_result['code'] < 0) {
                return $local_result;
            }

			if ($pintuan_order_info['group_id'] == 0) {
				//开团
				//创建组
				$pintuan_group_model = new PintuanGroup();
				$group_id = $pintuan_group_model->addPintuanGroup($pintuan_order_info);
				
				//更新拼团订单组信息
				$pintuan_order_data['group_id'] = $group_id['data'];
				$pintuan_order_data['pintuan_status'] = 2;

				$res = model('promotion_pintuan_order')->update($pintuan_order_data, [ [ 'order_id', '=', $order['order_id'] ] ]);
				//更新订单营销状态名称
				model('order')->update([ 'promotion_status_name' => '拼团中', 'is_lock' => 1], [ [ 'order_id', '=', $order['order_id'] ] ]);
			} else {//参团
				
				//更新拼团订单信息
				$pintuan_order_data['pintuan_status'] = 2;
				$res = model('promotion_pintuan_order')->update($pintuan_order_data, [ [ 'order_id', '=', $order['order_id'] ] ]);
				//更新订单营销状态名称
				model('order')->update([ 'promotion_status_name' => '拼团中', 'is_lock' => 1], [ [ 'order_id', '=', $order['order_id'] ] ]);
				
				//加入组
				$pintuan_group_model = new PintuanGroup();
				$pintuan_group_model->joinPintuanGroup($pintuan_order_info);
			}
			
			model('promotion_pintuan_order')->commit();
			return $this->success($res);
		} catch (\Exception $e) {
			
			model('promotion_pintuan_order')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 获取拼团订单信息
	 * @param array $condition
	 * @param string $field
	 * @return array
	 */
	public function getPintuanOrderInfo($condition = [], $field = '*')
	{
		$order_info = model('promotion_pintuan_order')->getInfo($condition, $field);
		return $this->success($order_info);
	}
	
	/**
	 * 获取订单信息
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return array
	 */
	public function getPintuanOrderList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('promotion_pintuan_order')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取订单分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @return array
	 */
	public function getPintuanOrderPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
	{
		$field = 'ppo.*,ppgs.id as pintuan_goods_id,
        ppg.pintuan_num,ppg.pintuan_count,ppg.is_promotion,ppg.end_time as group_end_time,
        o.site_name,o.pay_time,o.pay_money,o.order_status_name,o.name,o.order_money,o.mobile,o.address,o.full_address,o.order_from_name,o.pay_type_name,
        og.sku_name,og.sku_image';
		$alias = 'ppo';
		$join = [
			[ 'order o', 'o.order_id = ppo.order_id', 'left' ],
			[ 'order_goods og', 'og.order_id = ppo.order_id', 'left' ],
			[ 'promotion_pintuan_group ppg', 'ppo.group_id = ppg.group_id', 'left' ],
			[ 'promotion_pintuan_goods ppgs', 'og.sku_id = ppgs.sku_id and ppgs.pintuan_id=ppo.pintuan_id', 'left' ]
		];
		$list = model('promotion_pintuan_order')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
	/**
	 * 拼团订单详情
	 * @param $order_id
	 * @param $member_id
	 */
	public function getPintuanOrderDetail($id, $member_id)
	{
		$field = 'ppo.*,ppgs.id as pintuan_goods_id,
        ppg.pintuan_num,ppg.pintuan_count,ppg.is_promotion,ppg.end_time as group_end_time,
        pp.group_num,pp.order_num,
        gs.discount_price,
        o.site_name,o.pay_time,o.pay_money,o.order_status_name,o.name,o.mobile,o.address,o.full_address,o.order_from_name,o.pay_type_name,
        og.sku_name,og.sku_image';
		$alias = 'ppo';
		$join = [
			[ 'order o', 'o.order_id = ppo.order_id', 'left' ],
			[ 'order_goods og', 'og.order_id = ppo.order_id', 'left' ],
			[ 'promotion_pintuan_group ppg', 'ppo.group_id = ppg.group_id', 'left' ],
			[ 'promotion_pintuan pp', 'pp.pintuan_id = ppo.pintuan_id', 'left' ],
			[ 'goods_sku gs', 'gs.sku_id = og.sku_id', 'left' ],
			[ 'promotion_pintuan_goods ppgs', 'og.sku_id = ppgs.sku_id and ppgs.pintuan_id=ppo.pintuan_id', 'left' ]
		];
		$condition = array(
			[ "ppo.id", "=", $id ],
			[ "ppo.member_id", "=", $member_id ],
		);
		$info = model('promotion_pintuan_order')->getInfo($condition, $field, $alias, $join);
		//查询参与拼单的会员
		if (!empty($info)) {
			$member_list = model('promotion_pintuan_order')->getList([ [ "group_id", "=", $info["group_id"] ] ], "member_img,nickname,member_id");
			$info["member_list"] = $member_list;
		}
		return $this->success($info);
	}
	
}