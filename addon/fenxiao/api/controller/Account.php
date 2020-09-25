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

use addon\fenxiao\model\FenxiaoAccount;
use addon\fenxiao\model\Fenxiao as FenxiaoModel;
use app\api\controller\BaseApi;


/**
 * 分销商流水
 */
class Account extends BaseApi
{
	
	/**
	 * 分销商流水分页
	 * @return false|string
	 */
	public function page()
	{
		
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		
		$model = new FenxiaoModel();
		$fenxiao_info = $model->getFenxiaoInfo([ [ 'member_id', '=', $this->member_id ] ], 'fenxiao_id');
		$fenxiao_info = $fenxiao_info['data'];
		if (!empty($fenxiao_info['fenxiao_id'])) {
			$condition = [
				[ 'fenxiao_id', '=', $fenxiao_info['fenxiao_id'] ]
			];
			
			$account_model = new FenxiaoAccount();
			$list = $account_model->getFenxiaoAccountPageList($condition, $page, $page_size);
			return $this->response($list);
		}
		return $this->response($this->error('', 'FENXIAO_NOT_EXIST'));
	}
	
}