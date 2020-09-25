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

declare (strict_types = 1);

namespace addon\membersignin\event;

use app\model\member\Member as MemberModel;
use app\model\member\MemberAccount as MemberAccountModel;
use app\model\member\MemberSignin as MemberSigninModel;

/**
 * 会员签到奖励
 */
class MemberSignin
{
	/**
	 * @param $param
	 * @return string|\multitype
	 */
	public function handle($param)
	{
		$member_model = new MemberModel();
		$member_signin_model = new MemberSigninModel();
		$member_account_model = new MemberAccountModel();
		
		// 查询当前用户连签天数
		$member_info = $member_model->getMemberInfo([ [ 'member_id', '=', $param['member_id'] ] ], 'sign_days_series');
		$member_info = $member_info['data'];
		
		$award = $member_signin_model->getAward();
		$award = $award['data'];
		
		$res = [];
		if (!empty($award)) {
			
			$is_end = true;//是否超出连签天数，取最大的奖励
			
			foreach ($award as $k => $v) {
				if ($member_info['sign_days_series'] == $v['day']) {
					$is_end = false;
					$res = $v;
					break;
				}
			}
			
			if ($is_end) {
				$res = $award[ count($award) - 1 ];
			}
			
			foreach ($res as $curr_k => $curr_v) {
				if ($curr_k != 'day') {
					$adjust_num = $curr_v;
					$account_type = $curr_k;
					$remark = '签到送' . $adjust_num . $this->accountType($curr_k);
					$member_account_model->addMemberAccount($param['member_id'], $account_type, $adjust_num, 'signin', 0, $remark);
				}
			}
			
		}
		return $res;
	}
	
	private function accountType($key)
	{
		$type = [
			'point' => '积分',
			'growth' => '成长值',
			'coupon' => '优惠券'
		];
		return $type[ $key ];
	}
	
}