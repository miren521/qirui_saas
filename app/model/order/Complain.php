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


namespace app\model\order;

use app\model\system\Pay;

/**
 * 订单平台维权
 *
 * @author Administrator
 *
 */
class Complain extends OrderRefund
{
	/*********************************************************************************订单退款状态*****************************************************/
	//已申请退款
	const COMPLAIN_APPLY = 1;
	
	// 已通过
	const COMPLAIN_CONFIRM = 2;
	
	// 卖家拒绝退款
	const COMPLAIN_REFUND = -1;
	
	//撤销维权后
	const COMPLAIN_CANCEL = 0;
	
	/**
	 * 订单退款状态
	 * @var unknown
	 */
	public $complain_status = [
		self::REFUND_APPLY => [
			'status' => self::REFUND_APPLY,
			'name' => '申请维权',
			'action' => [
				[
					'event' => 'complainAgree',
					'title' => '同意',
					'color' => ''
				],
				[
					'event' => 'complainRefuse',
					'title' => '拒绝',
					'color' => ''
				],
			],
			'member_action' => [
				[
					'event' => 'complainCancel',
					'title' => '撤销维权',
					'color' => ''
				],
			]
		],
		self::COMPLAIN_CONFIRM => [
			'status' => self::REFUND_COMPLETE,
			'name' => '维权结束',
			'action' => [
			
			],
			'member_action' => [
			
			]
		],
		self::COMPLAIN_REFUND => [
			'status' => self::COMPLAIN_REFUND,
			'name' => '平台拒绝',
			'action' => [
			
			],
			'member_action' => [
				[
					'event' => 'complainCancel',
					'title' => '撤销维权',
					'color' => ''
				],
				[
					'event' => 'complainApply',
					'title' => '修改申请',
					'color' => ''
				],
			]
		],
		self::COMPLAIN_CANCEL => [
			'status' => self::COMPLAIN_CANCEL,
			'name' => '已撤销',
			'action' => [
			
			],
			'member_action' => [
				[
					'event' => 'complainApply',
					'title' => '申请维权',
					'color' => ''
				],
			
			]
		],
	];
	
	
	/****************************************************************************订单退款相关操作（开始）**********************************/
	
	
	/**
	 * 会员申请平台维权
	 * @param array $data
	 * @param array $member_info
	 * @return multitype:string mixed
	 */
	public function complainApply($data, $member_info)
	{
		$condition = array(
		    ['order_goods_id', '=', $data['order_goods_id']],
            ['member_id', '=', $member_info['member_id']]
        );
		$order_goods_info = model('order_goods')->getInfo($condition);
		if (empty($order_goods_info))
			return $this->error([], 'ORDER_GOODS_EMPTY');
		
		if ($order_goods_info['refund_status'] == 3)
			return $this->error();
		
		
		//查询是否存在正在进行的平台维权
		$complain_info_result = $this->getComplainInfo([ [ "order_goods_id", "=", $data['order_goods_id'] ] ], "*");
		$complain_info = $complain_info_result["data"];
		if (!empty($complain_info) && in_array($complain_info["complain_status"], [ 1, 2 ]))
			return $this->error();
		
		
		//订单不能已关闭或已完成
		$order_info = model('order')->getInfo([ 'order_id' => $order_goods_info['order_id'] ]);
		if (in_array($order_info["order_status"], [ -1, 10 ]))
			return $this->error();
		
		
		model('order_complain')->startTrans();
		try {
			$data = array(
				'order_id' => $order_goods_info["order_id"],
				'order_goods_id' => $data['order_goods_id'],
				'site_id' => $order_goods_info['site_id'],
				'site_name' => $order_goods_info['site_name'],
				'order_no' => $order_goods_info["order_no"],
				'member_id' => $order_goods_info['member_id'],
				'member_name' => $member_info['nickname'],
				'sku_id' => $order_goods_info['sku_id'],
				'sku_name' => $order_goods_info['sku_name'],
				'sku_image' => $order_goods_info['sku_image'],
				'sku_no' => $order_goods_info['sku_no'],
				'is_virtual' => $order_goods_info['is_virtual'],
//                 'goods_class'      => $order_goods_info['goods_class'],
//                 'goods_class_name' => $order_goods_info['goods_class_name'],
				'price' => $order_goods_info['price'],
				'num' => $order_goods_info['num'],
				'goods_money' => $order_goods_info['goods_money'],
				'goods_id' => $order_goods_info['goods_id'],
				'complain_reason' => $data["complain_reason"],
				'complain_remark' => $data["complain_remark"],
				'real_goods_money' => $order_goods_info['real_goods_money'],
			);
			$data['complain_status'] = self::COMPLAIN_APPLY;
			$data['complain_status_name'] = $this->complain_status[ self::COMPLAIN_APPLY ]["name"];
			$data['complain_status_action'] = json_encode($this->complain_status[ self::COMPLAIN_APPLY ], JSON_UNESCAPED_UNICODE);
			$data['complain_remark'] = $data["complain_remark"];
			$data["complain_apply_time"] = time();
			$pay_model = new Pay();
			$data['complain_no'] = $pay_model->createRefundNo();
			$complain_apply_money = $this->getOrderRefundMoney($data['order_goods_id']);//可退款金额 通过计算获得
			$data['complain_apply_money'] = $complain_apply_money;
			
			if (empty($complain_info)) {
				$res = model('order_complain')->add($data);
			} else {
				$res = model('order_complain')->update($data, [ 'order_goods_id' => $data['order_goods_id'] ]);
			}
			
			
			//日志
			$this->addOrderRefundLog($data['order_goods_id'], $order_goods_info["refund_status"], '买家申请平台维权', 1, $member_info['member_id'], $member_info['nickname']);
			model('order_complain')->commit();
			return $this->success($res);
		} catch (\Exception $e) {
			model('order_complain')->rollback();
			return $this->error('', $e->getMessage() . $e->getFile() . $e->getLine());
		}
	}
	
	/**
	 * 用户撤销平台申请
	 * @param array $data
	 * @param array $member_info
	 * @return string[]|mixed[]
	 */
	public function cancelComplain($data, $member_info)
	{
	    $condition = array(
            ['order_goods_id', '=', $data['order_goods_id']],
            ['member_id', '=', $member_info['member_id']]
        );
		$order_goods_info = model('order_goods')->getInfo($condition, 'order_id,refund_status,delivery_status,is_virtual,site_id');
		if (empty($order_goods_info))
			return $this->error([], 'ORDER_GOODS_EMPTY');
		
		model('order_complain')->startTrans();
		try {
			$data['complain_status'] = self::COMPLAIN_CANCEL;
			$data['complain_status_name'] = $this->complain_status[ self::COMPLAIN_CANCEL ]["name"];
			$data['complain_status_action'] = json_encode($this->complain_status[ self::COMPLAIN_CANCEL ], JSON_UNESCAPED_UNICODE);
			$res = model('order_complain')->update($data, [ 'order_goods_id' => $data['order_goods_id'] ]);
			
			//添加维权日志
			$this->addOrderRefundLog($data['order_goods_id'], $order_goods_info["refund_status"], '买家撤销平台维权退款申请', 1, $member_info['member_id'], $member_info['nickname']);
			model('order_complain')->commit();
			return $this->success();
		} catch (\Exception $e) {
			model('order_complain')->rollback();
			return $this->error('', $e->getMessage());
		}
	}
	
	/**
	 * 平台同意维权申请
	 * @param array $data
	 * @param array $user_info
	 */
	public function complainAgree($data, $user_info)
	{
		$order_goods_info = model('order_goods')->getInfo([ 'order_goods_id' => $data['order_goods_id'] ], 'order_id,order_goods_id,refund_status');
		if (empty($order_goods_info)) {
			return $this->error([], 'ORDER_GOODS_EMPTY');
		}
		//已完成的订单项不可维权
		if ($order_goods_info['refund_status'] == 3) {
			$result = $this->complainRefuse($data, $user_info);
			return $result;
		}
		
		$order_complain_info = model('order_complain')->getInfo([ 'order_goods_id' => $data['order_goods_id'] ], '*');
		if (empty($order_complain_info)) {
			return $this->error([]);
		}
		//已完成的订单项不可维权
		if ($order_complain_info['complain_status'] != 1) {
			return $this->error([]);
		}
		
		model('order_complain')->startTrans();
		try {
			$data['complain_status'] = self::COMPLAIN_CONFIRM;
			$data['complain_status_name'] = $this->complain_status[ self::COMPLAIN_CONFIRM ]["name"];
			$data['complain_status_action'] = json_encode($this->complain_status[ self::COMPLAIN_CONFIRM ], JSON_UNESCAPED_UNICODE);
			$data['complain_real_money'] = $order_complain_info["complain_apply_money"];
			$data["complain_time"] = time();
			
			$res = model('order_complain')->update($data, [ 'order_goods_id' => $data['order_goods_id'] ]);
			
			$this->addOrderRefundLog($data['order_goods_id'], $order_goods_info['refund_status'], '平台同意维权申请', 3, $user_info['uid'], $user_info['username']);
			
			//触发主动退款
			$refund_result = $this->activeOrderGoodsRefund($order_complain_info['order_goods_id'], $order_complain_info["complain_apply_money"], '平台同意维权申请', $order_complain_info["complain_reason"]);
			if ($refund_result["code"] < 0) {
				model('order_complain')->rollback();
				return $refund_result;
			}
			
			model('order_complain')->commit();
			return $this->success($res);
		} catch (\Exception $e) {
			model('order_complain')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 平台拒绝维权申请
	 * @param array $data
	 * @param array $user_info
	 */
	public function complainRefuse($data, $user_info, $refund_refuse_reason = '')
	{
		$order_goods_info = model('order_goods')->getInfo([ 'order_goods_id' => $data['order_goods_id'] ]);
		if (empty($order_goods_info)) {
			return $this->error();
		}
		
		model('order_complain')->startTrans();
		try {
			$data['complain_status'] = self::COMPLAIN_REFUND;
			$data['complain_status_name'] = $this->complain_status[ self::COMPLAIN_REFUND ]["name"];
			$data['complain_status_action'] = json_encode($this->complain_status[ self::COMPLAIN_REFUND ], JSON_UNESCAPED_UNICODE);
			$data['complain_refuse_reason'] = $refund_refuse_reason;
			$res = model('order_complain')->update($data, [ 'order_goods_id' => $data['order_goods_id'] ]);
			
			$this->addOrderRefundLog($data['order_goods_id'], $order_goods_info['refund_status'], '平台拒绝维权申请', 3, $user_info['uid'], $user_info['username']);
			
			model('order_complain')->commit();
			return $this->success();
		} catch (\Exception $e) {
			model('order_complain')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 获取平台维权订单列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getOrderComplainPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$list = model('order_complain')->pageList($condition, $field, $order, $page, $page_size);
		if (!empty($list["list"])) {
			foreach ($list["list"] as $k => $v) {
				$complain_action = empty($v["complain_status_action"]) ? [] : json_decode($v["complain_status_action"], true);
				$complain_member_action = $complain_action["member_action"] ?? [];
				$list['list'][ $k ]['complain_action'] = $complain_member_action;
			}
		}
		return $this->success($list);
	}
	
	
	/**
	 * 会员维权详情
	 * @param $order_goods_id
	 */
	public function getMemberComplainDetail($order_goods_id, $member_id)
	{
		$condition = array(
			[ "order_goods_id", "=", $order_goods_id ]
		);
		
		$condition[] = [ "member_id", "=", $member_id ];
		
		$info = model("order_complain")->getInfo($condition);
		if (!empty($info)) {
			$complain_action = empty($info["complain_status_action"]) ? [] : json_decode($info["complain_status_action"], true);
			$complain_member_action = $complain_action["member_action"] ?? [];
			$info['complain_action'] = $complain_member_action;
			//将售后日志引入
			$refund_log_list = model("order_refund_log")->getList([ [ "order_goods_id", "=", $info["order_goods_id"] ] ], "*", "action_time desc");
			$info['refund_log_list'] = $refund_log_list;
		}
		return $this->success($info);
	}
	
	/**
	 * 获取平台维权详情
	 * @param $condition
	 * @param string $field
	 */
	public function getComplainInfo($condition, $field = "*")
	{
		$info = model("order_complain")->getInfo($condition, $field);
		return $this->success($info);
	}
	
	/**
	 * 维权详情
	 * @param $order_goods_id
	 */
	public function getComplainDetail($order_goods_id)
	{
		$condition = array(
			[ "order_goods_id", "=", $order_goods_id ]
		);
		$info = model("order_complain")->getInfo($condition);
		$complain_action = empty($info["complain_status_action"]) ? [] : json_decode($info["complain_status_action"], true);
		$complain_member_action = $complain_action["action"] ?? [];
		$info['complain_action'] = $complain_member_action;
		//将售后日志引入
		$refund_log_list = model("order_refund_log")->getList([ [ "order_goods_id", "=", $info["order_goods_id"] ] ], "*", "action_time desc");
		$info['refund_log_list'] = $refund_log_list;
		return $this->success($info);
	}
	
	/**
	 * 获取平台维权数量
	 * @param $condition
	 * @param string $field
	 */
	public function getComplainCount($condition)
	{
		$info = model("order_complain")->getCount($condition);
		return $this->success($info);
	}
	/****************************************************************************订单退款相关操作（结束）**********************************/
}