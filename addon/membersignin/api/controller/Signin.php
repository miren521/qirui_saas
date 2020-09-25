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


namespace addon\membersignin\api\controller;

use app\api\controller\BaseApi;
use addon\membersignin\model\Signin as SigninModel;

/**
 * 会员签到
 */
class Signin extends BaseApi
{
	
	/**
	 * 配置信息
	 */
	public function config()
	{
		$signin_model = new SigninModel();
        $result = $signin_model->getConfig();

		return $this->response($result);
	}
	
}