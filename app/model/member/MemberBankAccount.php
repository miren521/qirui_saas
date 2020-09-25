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

use app\model\BaseModel;
use think\facade\Cache;

/**
 * 会员提现账号
 */
class MemberBankAccount extends BaseModel
{
	
	/**
	 * 添加会员提现账号
	 * @param $data
	 * @return array
	 */
	public function addMemberBankAccount($data)
	{
		//微信账号只能添加一个
        if ($data['withdraw_type'] == 'wechatpay') {
            $count = model('member_bank_account')->getCount([ [ 'withdraw_type', '=', 'wechatpay' ], [ 'member_id', '=', $data['member_id'] ] ]);
            if ($count > 0) {
                return $this->error('', '只能添加一个微信账号');
            }
        }
		if ($data['is_default'] == 1) {
			model('member_bank_account')->update([ 'is_default' => 0 ], [ 'member_id' => $data['member_id'] ]);
		}
		$data['create_time'] = time();
		$id = model('member_bank_account')->add($data);
		$count = model('member_bank_account')->getCount([ 'member_id' => $data['member_id'] ]);
		if ($count == 1)
			model('member_bank_account')->update([ 'is_default' => 1 ], [ 'member_id' => $data['member_id'], 'id' => $id ]);
		Cache::tag("member_bank_account_" . $data['member_id'])->clear();
		return $this->success($id);
	}
	
	/**
	 * 修改会员提现账号
	 * @param $data
	 * @return array
	 */
	public function editMemberBankAccount($data)
	{
        if ($data['withdraw_type'] == 'wechatpay') {
            //微信账号只能添加一个
            $count = model('member_bank_account')->getCount([ [ 'withdraw_type', '=', 'wechatpay' ], [ 'member_id', '=', $data['member_id'] ] ]);
            if ($count > 0) {
                return $this->error('', '只能添加一个微信账号');
            }
        }
		if ($data['is_default'] == 1) {
			model('member_bank_account')->update([ 'is_default' => 0 ], [ 'member_id' => $data['member_id'] ]);
		}
		$data['modify_time'] = time();
		$res = model('member_bank_account')->update($data, [ 'id' => $data['id'] ]);
		Cache::tag("member_bank_account_" . $data['member_id'])->clear();
		return $this->success($res);
	}
	
	/**
	 * 删除会员提现账号
	 * @param array $condition
	 */
	public function deleteMemberBankAccount($condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$res = model('member_bank_account')->delete($condition);
		Cache::tag("member_bank_account_" . $check_condition['member_id'])->clear();
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 设置默认会员提现账号
	 * @param $id
	 * @param $member_id
	 * @return \multitype
	 */
	public function modifyDefaultAccount($id, $member_id)
	{
		model('member_bank_account')->startTrans();
		try {
			model('member_bank_account')->update([ 'is_default' => 0 ], [ 'member_id' => $member_id ]);
			$res = model('member_bank_account')->update([ 'is_default' => 1 ], [ 'member_id' => $member_id, 'id' => $id ]);
			model('member_bank_account')->commit();
			Cache::tag("member_bank_account_" . $member_id)->clear();
			return $this->success($res);
		} catch (\Exception $e) {
			model('member_bank_account')->rollback();
			return $this->error('', $e->getMessage());
		}
	}
	
	/**
	 * 获取会员提现账号信息
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getMemberBankAccountInfo($condition, $field = '*')
	{
		$check_condition = array_column($condition, 2, 0);
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::get("member_bank_account_getMemberBankAccountInfo_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('member_bank_account')->getInfo($condition, $field);
		Cache::tag("member_bank_account_" . $check_condition['member_id'])->set("member_bank_account_getMemberBankAccountInfo_" . $data, $res);
		return $this->success($res);
	}
	
	/**
	 * 获取会员提现账号分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array|\multitype
	 */
	public function getMemberBankAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'is_default desc,create_time desc', $field = '*')
	{
		$check_condition = array_column($condition, 2, 0);
		$data = json_encode([ $condition, $field, $order, $page, $page_size ]);
		$cache = Cache::get("member_bank_account_getMemberBankAccountPageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('member_bank_account')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("member_bank_account_" . $check_condition['member_id'])->set("member_bank_account_getMemberBankAccountPageList_" . $data, $list);
		return $this->success($list);
	}
	
}