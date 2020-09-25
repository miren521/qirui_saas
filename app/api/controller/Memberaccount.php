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


namespace app\api\controller;

use app\model\member\MemberAccount as MemberAccountModel;
use app\model\member\Member as MemberModel;

class Memberaccount extends BaseApi
{
	/**
	 * 基础信息
	 */
	public function info()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$account_type = isset($this->params['account_type']) ? $this->params['account_type'] : 'balance,balance_money'; //账户类型 余额:balance，积分:point
		
		if (!in_array($account_type, [ 'point', 'balance', 'balance,balance_money' ])) return $this->response($this->error('', 'INVALID_PARAMETER'));
		
		$member_model = new MemberModel();
		$info = $member_model->getMemberInfo([ [ 'member_id', '=', $token['data']['member_id'] ] ], $account_type);
		return $this->response($info);
	}
	
	/**
	 * 列表信息
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$account_type = isset($this->params['account_type']) ? $this->params['account_type'] : 'balance';//账户类型 余额:balance，积分:point
        $condition = [
            [ 'member_id', '=', $token['data']['member_id'] ]
        ];
		if($account_type == 'balance'){
            $condition [] = [ 'account_type', 'in', ['balance', 'balance_money'] ];
        }else{
            $condition [] = [ 'account_type', '=', $account_type ];
        }
		$member_account_model = new MemberAccountModel();
		$list = $member_account_model->getMemberAccountPageList($condition, $page, $page_size);
		return $this->response($list);
	}
	
}