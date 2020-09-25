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

use app\model\member\Withdraw as WithdrawModel;
use app\model\member\Member as MemberModel;

/**
 * 会员提现
 */
class Memberwithdraw extends BaseApi
{
	
	/**
	 * 会员提现信息
	 */
	public function info()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$member_model = new MemberModel();
		$member_info_result = $member_model->getMemberInfo([ [ 'member_id', '=', $token['data']['member_id'] ] ], 'balance_money,balance_withdraw_apply,balance_withdraw');
//		$withdraw_model = new WithdrawModel();
//		$transfer_type_list = $withdraw_model->getTransferType();
		$config_model = new WithdrawModel();
		$config_result = $config_model->getConfig();
		$config = $config_result["data"]['value'];
		$config['is_use'] = $config_result["data"]['is_use'];
		
		$data = array(
			"member_info" => $member_info_result["data"],
//			"transfer_type_list" => $transfer_type_list,
			"config" => $config
		);
		return $this->response($this->success($data));
		
	}
	
	/**
	 * 会员提现配置
	 */
	public function config()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$config_model = new WithdrawModel();
		$config_result = $config_model->getConfig();
		return $this->response($config_result);
	}
	
	/**
	 * 获取转账方式
	 * @return false|string
	 */
	public function transferType()
	{
		$withdraw_model = new WithdrawModel();
		$transfer_type_list = $withdraw_model->getTransferType();
		return $this->response($this->success($transfer_type_list));
	}

    /**
     * 申请提现
     * @return false|string
     */
    public function apply()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $apply_money    = isset($this->params['apply_money']) ? $this->params['apply_money'] : 0;
        $transfer_type  = isset($this->params['transfer_type']) ? $this->params['transfer_type'] : '';//提现方式
        $realname       = isset($this->params['realname']) ? $this->params['realname'] : '';//真实姓名
        $bank_name      = isset($this->params['bank_name']) ? $this->params['bank_name'] : '';//银行名称
        $account_number = isset($this->params['account_number']) ? $this->params['account_number'] : '';//账号名称
        $mobile         = isset($this->params['mobile']) ? $this->params['mobile'] : '';//手机号
        $app_type       = $this->params['app_type'];
        $withdraw_model = new WithdrawModel();
        $transfer_type_list = $withdraw_model->getTransferType();
        if (!array_key_exists($transfer_type, $transfer_type_list)) {
            return $this->response($this->error('', '提现方式未开启'));
        }
        $withdraw_model = new WithdrawModel();
        $data           = array(
            "member_id"      => $token['data']['member_id'],
            "transfer_type"  => $transfer_type,
            "realname"       => $realname,
            "bank_name"      => $bank_name,
            "account_number" => $account_number,
            "apply_money"    => $apply_money,
            "mobile"         => $mobile,
            "app_type"       => $app_type
        );
        $result         = $withdraw_model->apply($data);
        return $this->response($result);
    }

    /**
	 * 提现详情
	 * @return mixed
	 */
	public function detail()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		$condition = [
			[ "member_id", "=", $token['data']['member_id'] ],
			[ "id", "=", $id ]
		];
		$withdraw_model = new WithdrawModel();
		$info = $withdraw_model->getMemberWithdrawDetail($condition);
		return $this->response($info);
	}
	
	/**
	 * 提现记录
	 * @return mixed
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		
		$condition = [
			[ "member_id", "=", $token['data']['member_id'] ]
		];
		$withdraw_model = new WithdrawModel();
		$list = $withdraw_model->getMemberWithdrawPageList($condition, $page, $page_size, "apply_time desc", "id,withdraw_no,apply_money,apply_time,status,status_name,transfer_type_name");
		return $this->response($list);
	}
	
}