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


namespace addon\goodscircle\api\controller;

use app\api\controller\BaseApi;
use addon\goodscircle\model\Config as ConfigModel;
/**
 * 好物圈
 */
class Config extends BaseApi
{
	/**
	 * 获取好物圈配置
	 */
	public function info()
	{
		$config = new ConfigModel();
        $res = $config->getGoodscircleConfig();
        return $this->response($this->success($res['data']));
	}
	
}