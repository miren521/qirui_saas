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


namespace addon\weapp\api\controller;

use app\api\controller\BaseApi;
use addon\weapp\model\Weapp as WeappModel;

class Weapp extends BaseApi
{
	/**
	 * 获取openid
	 */
	public function authCodeToOpenid()
	{
		$weapp_model = new WeappModel();
		$res = $weapp_model->authCodeToOpenid($this->params);
		return $this->response($res);
	}
}