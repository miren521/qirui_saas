<?php
/**
 * Membersignin.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

namespace app\api\controller;

use app\model\member\MemberSignin as MemberSigninModel;

class Membersignin extends BaseApi
{
	
	/**
	 * 是否已签到
	 */
	public function issign()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$member_signin = new MemberSigninModel();
		$res = $member_signin->isSign($token['data']['member_id']);
		return $this->response($res);
	}
	
	/**
	 * 签到
	 */
	public function signin()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$member_signin = new MemberSigninModel();
		$res = $member_signin->signin($token['data']['member_id']);
		return $this->response($res);
	}
	
	/**
	 * 签到奖励规则
	 * @return string
	 */
	public function award()
	{
		$member_signin = new MemberSigninModel();
		$info = $member_signin->getAward();
		return $this->response($info);
	}
	
}