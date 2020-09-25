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
use Carbon\Carbon;

/**
 * 会员签到
 */
class MemberSignin extends BaseModel
{
	/**
	 * 获取签到奖励规则
	 */
	public function getAward()
	{
		$award = event('MemberSigninAward', [], true);
		return $this->success($award);
	}
	
	/**
	 * 判断是否已经签到
	 * @param $member_id
	 * @return array
	 */
	public function isSign($member_id)
	{
		$member_info = model("member")->getInfo([ [ 'member_id', '=', $member_id ] ], 'sign_time');
		if ($member_info['sign_time'] != 0) {
			$compare_time = Carbon::today()->timestamp;
			if ($member_info['sign_time'] < $compare_time) {
				$is_sign = 0;
			} else {
				$is_sign = 1;
			}

			//纠正连签天数
			$compare_yesterday = Carbon::yesterday()->timestamp;
			if ($compare_yesterday > $member_info[ 'sign_time' ]) {
				model("member")->update(['sign_days_series' => 0, 'sign_time' => 0], [['member_id', '=', $member_id]]);
			}

		} else {
			$is_sign = 0;
		}
		return $this->success($is_sign);
	}
	
	/**
	 * 签到
	 * @param $member_id
	 * @return array|\multitype
	 */
	public function signin($member_id)
	{
		$member_info = model("member")->getInfo([ [ 'member_id', '=', $member_id ] ], 'sign_time,sign_days_series');
		if ($member_info['sign_time'] != 0) {
			$compare_time = Carbon::today()->timestamp;
			if ($member_info['sign_time'] < $compare_time) {
				$is_sign = 0;
			} else {
				$is_sign = 1;
			}
		} else {
			$is_sign = 0;
		}
		if ($is_sign == 1) {
			return $this->error('', "SIGNED_IN");
		} else {
			$data_log = [
				'member_id' => $member_id,
				'action' => 'membersignin',
				'action_name' => '会员签到',
				'create_time' => time(),
				'remark' => '会员签到'
			];
			model("member_log")->add($data_log);
			
			//连续签到
			$compare_yesterday = Carbon::yesterday()->timestamp;
			if ($compare_yesterday < $member_info['sign_time']) {
				model("member")->setInc([ [ 'member_id', '=', $member_id ] ], 'sign_days_series');
				model("member")->update([ 'sign_time' => time() ], [ [ 'member_id', '=', $member_id ] ]);
			} else {
				model("member")->update([ 'sign_days_series' => 1, 'sign_time' => time() ], [ [ 'member_id', '=', $member_id ] ]);
			}
			
			//执行签到奖励
			$res = event("MemberSignin", [ 'member_id' => $member_id ], true);
			return $this->success($res);
		}
		
	}
	
}