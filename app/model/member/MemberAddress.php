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


namespace app\model\member;


use think\facade\Cache;
use app\model\BaseModel;

/**
 * 会员地址
 */
class MemberAddress extends BaseModel
{
	
	/**
	 * 添加会员地址
	 * @param array $data
	 */
	public function addMemberAddress($data)
	{
		if ($data['is_default'] == 1) {
			model('member_address')->update([ 'is_default' => 0 ], [ 'member_id' => $data['member_id'] ]);
		}
		$res = model('member_address')->add($data);
		$count = model('member_address')->getCount([ 'member_id' => $data['member_id'] ]);
		if ($count == 1)
			model('member_address')->update([ 'is_default' => 1 ], [ 'member_id' => $data['member_id'], 'id' => $res ]);
		Cache::tag("member_address_" . $data['member_id'])->clear();
		return $this->success($res);
	}

	/**
	 * 修改会员地址
	 * @param array $params
	 */
	public function editMemberAddress($data)
	{
		if ($data['is_default'] == 1) {
			model('member_address')->update([ 'is_default' => 0 ], [ 'member_id' => $data['member_id'] ]);
		}
		$res = model('member_address')->update($data, [ 'id' => $data['id'], 'member_id' => $data['member_id'] ]);
		Cache::tag("member_address_" . $data['member_id'])->clear();
		return $this->success($res);
	}
	
	
	/**
	 * 删除收获地址
	 * @param array $condition
	 */
	public function deleteMemberAddress($condition)
	{
		$member_id = isset($condition['member_id']) ? $condition['member_id'] : 0;
		if (empty($member_id)) {
			return $this->error("", "缺少必填参数会员id");
		}
		$res = model('member_address')->delete($condition);
		Cache::tag("member_address_" . $condition['member_id'])->clear();
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		
		return $this->success($res);
	}
	
	/**
	 * 设置默认收货地址
	 * @param $id
	 * @param $member_id
	 * @return \multitype
	 */
	public function setMemberDefaultAddress($id, $member_id)
	{
		model('member_address')->startTrans();
		try {
			model('member_address')->update([ 'is_default' => 0 ], [ 'member_id' => $member_id ]);
			$res = model('member_address')->update([ 'is_default' => 1 ], [ 'member_id' => $member_id, 'id' => $id ]);
			model('member_address')->commit();
			Cache::tag("member_address_" . $member_id)->clear();
			return $this->success($res);
		} catch (\Exception $e) {
			model('member_address')->rollback();
			return $this->error('', $e->getMessage());
		}
	}
	
	/**
	 * 获取详情收获地址
	 * @param array $condition
	 */
	public function getMemberAddressInfo($condition, $field = '*')
	{
		$check_condition = array_column($condition, 2, 0);
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::get("member_address_getMemberAddressInfo_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('member_address')->getInfo($condition, $field);
		Cache::tag("member_address_" . $check_condition['member_id'])->set("member_address_getMemberAddressInfo_" . $data, $res);
		return $this->success($res);
	}
	
	/**
	 * 获取收获地址列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 * @return multitype:string mixed
	 */
	public function getMemberAddressList($condition = [], $field = '*', $order = 'is_default desc', $limit = null)
	{
		$check_condition = array_column($condition, 2, 0);
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::get("member_address_getMemberAddressList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('member_address')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("member_address_" . $check_condition['member_id'])->set("member_address_getMemberAddressList_" . $data, $list);
		return $this->success($list);
		
	}
	
	/**
	 * 获取收获地址分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return \multitype
	 */
	public function getMemberAddressPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'is_default desc,id desc', $field = 'id, member_id, name, mobile, telephone, address, full_address, is_default')
	{
		$check_condition = array_column($condition, 2, 0);
		$data = json_encode([ $condition, $page, $page_size, $order, $field ]);
		$cache = Cache::get("member_address_getMemberAddressList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('member_address')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("member_address_" . $check_condition['member_id'])->set("member_address_getMemberAddressPageList_" . $data, $list);
		return $this->success($list);
	}
}