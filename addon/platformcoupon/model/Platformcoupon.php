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


namespace addon\platformcoupon\model;

use app\model\BaseModel;

/**
 * 优惠券
 */
class Platformcoupon extends BaseModel
{
	/**
	 * 获取编码
	 */
	public function getCode()
	{
		return random_keys(8);
	}

    /**
     * 获取优惠券
     * @param $platformcoupon_type_id
     * @param $member_id
     * @param $get_type
     * @param int $is_stock
     * @param int $is_limit
     * @return array
     */
	public function receivePlatformcoupon($platformcoupon_type_id, $member_id, $get_type, $is_stock = 0, $is_limit = 1)
	{
		
		// 用户已领取数量
		if (empty($member_id)) {
			return $this->error('', '请先进行登录');
		}
		$platformcoupon_type_info = model('promotion_platformcoupon_type')->getInfo([ 'platformcoupon_type_id' => $platformcoupon_type_id ]);
		if (!empty($platformcoupon_type_info)) {
			
			if ($platformcoupon_type_info['count'] == $platformcoupon_type_info['lead_count'] && $is_stock == 0) {
				return $this->error('', '来迟了该优惠券已被领取完了');
			}
			
			if ($platformcoupon_type_info['max_fetch'] != 0) {
				//限制领取
				$member_receive_num = model('promotion_platformcoupon')->getCount([
					'platformcoupon_type_id' => $platformcoupon_type_id,
					'member_id' => $member_id
				]);
				if ($member_receive_num >= $platformcoupon_type_info['max_fetch'] && $is_limit == 1) {
					return $this->error('', '该优惠券领取已达到上限');
				}
				
			}
			
			$data = [
				'platformcoupon_type_id' => $platformcoupon_type_id,
				'platformcoupon_code' => $this->getCode(),
				'member_id' => $member_id,
				'money' => $platformcoupon_type_info['money'],
				'state' => 1,
				'get_type' => $get_type,
				'fetch_time' => time(),
				'platformcoupon_name' => $platformcoupon_type_info['platformcoupon_name'],
				'at_least' => $platformcoupon_type_info['at_least'],

                      'use_scenario' => $platformcoupon_type_info['use_scenario'],
                      'group_ids' => $platformcoupon_type_info['group_ids'],
                      'platform_split_rare' => $platformcoupon_type_info['platform_split_rare'],
                      'shop_split_rare' => $platformcoupon_type_info['shop_split_rare']
			];
			
			if ($platformcoupon_type_info['validity_type'] == 0) {
				$data['end_time'] = $platformcoupon_type_info['end_time'];
			} else {
				$data['end_time'] = (time() + $platformcoupon_type_info['fixed_term'] * 86400);
			}
			$res = model('promotion_platformcoupon')->add($data);
			if ($is_stock == 0) {
				model('promotion_platformcoupon_type')->setInc([ 'platformcoupon_type_id' => $platformcoupon_type_id ], 'lead_count');
			}
			return $this->success($res);
			
		} else {
			return $this->error('', '未查找到该优惠券');
		}
	}
	
	/**
	 * 使用优惠券
	 * @param $data
	 */
	public function usePlatformcoupon($platformcoupon_id, $member_id, $use_order_id)
	{
		$data = array( 'use_order_id' => $use_order_id, 'use_time' => time(), 'state' => 2 );
		$condition = array(
			[ 'platformcoupon_id', '=', $platformcoupon_id ],
			[ 'member_id', '=', $member_id ],
			[ 'state', '=', 1 ]
		);
		$result = model("promotion_platformcoupon")->update($data, $condition);
		return $this->success($result);
	}
	
	/**
	 * 退还优惠券
	 * @param $platformcoupon_id
	 * @param $member_id
	 */
	public function refundPlatformcoupon($platformcoupon_id, $member_id)
	{
		$result = model("promotion_platformcoupon")->update([ 'use_time' => 0, 'state' => 1 ], [ [ 'platformcoupon_id', '=', $platformcoupon_id ], [ 'member_id', '=', $member_id ], [ 'state', '=', 2 ] ]);
		return $this->success($result);
	}
	
	/**
	 * 获取优惠券信息
	 * @param unknown $platformcoupon_code 优惠券编码
	 * @param unknown $field
	 */
	public function getPlatformcouponInfo($condition, $field = "*")
	{
		$info = model("promotion_platformcoupon")->getInfo($condition, $field);
		return $this->success($info);
	}
	
	/**
	 * 获取优惠券列表
	 * @param array $condition
	 * @param bool $field
	 * @param string $order
	 * @param null $limit
	 */
	public function getPlatformcouponList($condition = [], $field = true, $order = '', $limit = null)
	{
		$list = model("promotion_platformcoupon")->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取优惠券列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getPlatformcouponPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'fetch_time desc', $field = 'platformcoupon_id,platformcoupon_type_id,platformcoupon_code,member_id,use_order_id,at_least,money,state,get_type,fetch_time,use_time,end_time')
	{
		$list = model('promotion_platformcoupon')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	
	/**
	 * 获取会员优惠券列表
	 * @param array $condition
	 * @param number $page
	 * @param number $page_size
	 */
	public function getMemberPlatformcouponPageList($condition, $page = 1, $page_size = PAGE_LIST_ROWS)
	{
		$field = 'npc.platformcoupon_name,npc.use_order_id,npc.platformcoupon_id,npc.platformcoupon_type_id,npc.platformcoupon_code,npc.member_id,
		npc.at_least,npc.money,npc.state,npc.get_type,npc.fetch_time,npc.use_time,npc.end_time,
		mem.username,on.order_no,npc.use_scenario';
		$alias = 'npc';
		$join = [

			[
				'member mem',
				'npc.member_id = mem.member_id',
				'inner'
			],
			[
				'order on',
				'npc.use_order_id = on.order_id',
				'left'
			]
		];
		$list = model("promotion_platformcoupon")->pageList($condition, $field, 'fetch_time desc', $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
	/**
	 * 获取优惠券信息
	 * @param array $condition
	 * @param unknown $field
	 */
	public function getPlatformcouponTypeInfo($condition, $field = 'platformcoupon_type_id,platformcoupon_name,money,count,lead_count,max_fetch,at_least,end_time,image,validity_type,fixed_term,status')
	{
		$info = model("promotion_platformcoupon_type")->getInfo($condition, $field);
		return $this->success($info);
	}
	
	/**
	 * 获取优惠券列表
	 * @param array $condition
	 * @param bool $field
	 * @param string $order
	 * @param null $limit
	 */
	public function getPlatformcouponTypeList($condition = [], $field = true, $order = '', $limit = null)
	{
		$list = model("promotion_platformcoupon_type")->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取优惠券分页列表
	 * @param $condition
	 * @param int $page
	 * @param int $page_size
	 * @return \multitype
	 */
	public function getPlatformcouponTypePageList($condition, $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'platformcoupon_type_id desc', $field = 'platformcoupon_type_id,platformcoupon_name,money,max_fetch,at_least,end_time,image,validity_type,fixed_term,status,is_show,use_scenario')
	{
		$list = model("promotion_platformcoupon_type")->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	
	/**
	 * 获取会员已领取优惠券优惠券
	 * @param array $member_id
	 */
	public function getMemberPlatformcouponList($member_id, $state, $money = 0, $order = "fetch_time desc")
	{
		$condition = array(
			[ "member_id", "=", $member_id ],
			[ "state", "=", $state ],
//            [ "end_time", ">", time()]
		);
		if ($money > 0) {
//            $condition[] = [ "at_least", "=", 0 ];
			$condition[] = [ "at_least", "<=", $money ];
		}
		$list = model("promotion_platformcoupon")->getList($condition, "*", $order, '', '', '', 0);
		return $this->success($list);
	}
	
	public function getMemberPlatformcouponCount($condition)
	{
		$list = model("promotion_platformcoupon")->getCount($condition);
		return $this->success($list);
	}
	
	/**
	 * 增加库存
	 * @param $param
	 */
	public function incStock($param)
	{
		$condition = array(
			[ "platformcoupon_type_id", "=", $param["platformcoupon_type_id"] ]
		);
		$num = $param["num"];
		$platformcoupon_info = model("promotion_platformcoupon_type")->getInfo($condition, "count,lead_count");
		if (empty($platformcoupon_info))
			return $this->error(-1, "");
		
		//编辑优惠券库存
		$result = model("promotion_platformcoupon_type")->setDec($condition, "lead_count", $num);
		return $this->success($result);
	}
	
	/**
	 * 减少库存
	 * @param $param
	 */
	public function decStock($param)
	{
		$condition = array(
			[ "platformcoupon_type_id", "=", $param["platformcoupon_type_id"] ]
		);
		$num = $param["num"];
		$platformcoupon_info = model("promotion_platformcoupon_type")->getInfo($condition, "count,lead_count");
		if (empty($platformcoupon_info))
			return $this->error(-1, "找不到可发放的优惠券");
		
		//编辑sku库存
		if (($platformcoupon_info["count"] - $platformcoupon_info["lead_count"]) < $num)
			return $this->error(-1, "库存不足");
		
		$result = model("promotion_platformcoupon_type")->setInc($condition, "lead_count", $num);
		if ($result === false)
			return $this->error();
		
		return $this->success($result);
	}
	
	
	/**
	 * 定时关闭
	 * @return mixed
	 */
	public function cronPlatformcouponEnd()
	{
		$res = model("promotion_platformcoupon")->update([ 'state' => 3 ], [ [ 'state', '=', 1 ], [ 'end_time', '<=', time() ] ]);
		return $res;
	}
}