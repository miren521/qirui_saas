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


namespace addon\manjian\api\controller;

use app\api\controller\BaseApi;
use addon\manjian\model\Manjian as ManjianModel;

/**
 * 满减
 */
class Manjian extends BaseApi
{
	
	/**
	 * 信息
	 */
	public function info()
	{
		$goods_id = isset($this->params['goods_id']) ? $this->params['goods_id'] : 0;
		$site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0; //站点id
		if (empty($goods_id)) {
			return $this->response($this->error('', 'REQUEST_GOODS_ID'));
		}
		$manjian_model = new ManjianModel();
		$res = $manjian_model->getGoodsManjianInfo($goods_id,$site_id);
		if (!empty($res['data'])) {
			$res['data']['rule_json'] = json_decode($res['data']['rule_json'], true);
		}
		return $this->response($res);
	}
	
}