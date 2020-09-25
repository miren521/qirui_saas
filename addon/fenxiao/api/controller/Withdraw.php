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


namespace addon\fenxiao\api\controller;

use addon\fenxiao\model\FenxiaoWithdraw;
use app\api\controller\BaseApi;


/**
 * 分销提现
 */
class Withdraw extends BaseApi
{
	
	/**
	 * 申请提现
	 */
	public function apply()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$member_id = $this->member_id;
		$money = isset($this->params['money']) ? $this->params['money'] : '';
		
		if (empty($money)) {
			return $this->response($this->error('', 'REQUEST_MONEY'));
		}
		
		$data = [
			'member_id' => $member_id,
			'money' => $money
		];
		
		$withdraw_model = new FenxiaoWithdraw();
		$res = $withdraw_model->addFenxiaoWithdraw($data);
		
		return $this->response($res);
	}
	
	/**
	 * 提现记录分页
	 * @return false|string
	 */
	public function page()
	{
		
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$status = isset($this->params['status']) ? $this->params['status'] : 0;// 当前状态 1待审核 2待转账 3已转账 -1 已拒绝
		
		$condition = [
			[ 'member_id', '=', $this->member_id ]
		];
		if (!empty($status)) {
			$condition[] = [ 'status', '=', $status ];
		}
		
		$order = 'id desc';
		$withdraw_model = new FenxiaoWithdraw();
		$list = $withdraw_model->getFenxiaoWithdrawPageList($condition, $page, $page_size, $order);
		return $this->response($list);
	}
	
}