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

use addon\fenxiao\model\FenxiaoApply;
use app\api\controller\BaseApi;

/**
 * 申请分销商
 */
class Apply extends BaseApi
{	
	/**
	 * 判断分销商名称是否存在
	 */
	public function existFenxiaoName()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$fenxiao_name = isset($this->params['fenxiao_name']) ? $this->params['fenxiao_name'] : '';//分销商名称
		if (empty($fenxiao_name)) {
			return $this->response($this->error('', 'REQUEST_FENXIAO_NAME'));
		}
		
		$apply_model = new FenxiaoApply();
		$res = $apply_model->existFenxiaoName($fenxiao_name);
		
		return $this->response($res);
	}
	
	/**
	 * 申请成为分销商
	 */
	public function applyFenxiao()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$fenxiao_name = isset($this->params['fenxiao_name']) ? $this->params['fenxiao_name'] : '';//分销商名称
		$mobile = isset($this->params['mobile']) ? $this->params['mobile'] : '';//联系电话
		
		if (empty($fenxiao_name)) {
			return $this->response($this->error('', 'REQUEST_FENXIAO_NAME'));
		}
		
		if (empty($mobile)) {
			return $this->response($this->error('', 'REQUEST_MOBILE'));
		}
		
		$apply_model = new FenxiaoApply();
		$res = $apply_model->applyFenxiao($this->member_id, $fenxiao_name, $mobile);
		
		return $this->response($res);
	}
	
	public function info()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$apply_model = new FenxiaoApply();
		$apply_model->getFenxiaoInfo([ [ 'member_id', '=', $this->member_id ] ], 'apply_id,fenxiao_name,parent,member_id,mobile,nickname,headimg,level_id,level_name,status');
	}
	
	/**
	 * 获取申请分销商状态
	 * @return false|string
	 */
	public function status()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$apply_model = new FenxiaoApply();
		$res = $apply_model->getFenxiaoApplyInfo([ [ 'member_id', '=', $this->member_id ] ], 'status');
		return $this->response($res);
	}
	
}