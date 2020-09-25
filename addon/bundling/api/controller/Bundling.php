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


namespace addon\bundling\api\controller;

use app\api\controller\BaseApi;
use addon\bundling\model\Bundling as BundlingModel;

/**
 * 组合套餐
 */
class Bundling extends BaseApi
{
	/**
	 * sku所关联有关组合套餐
	 * @return string
	 */
	public function lists()
	{
		$sku_id = isset($this->params['sku_id']) ? $this->params['sku_id'] : 0;
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		$bundling_model = new BundlingModel();
		$info = $bundling_model->getBundlingGoods($sku_id);
		return $this->response($info);
	}
	
	/**
	 * 详情信息
	 */
	public function detail()
	{
		$bl_id = isset($this->params['bl_id']) ? $this->params['bl_id'] : 0;
		if (empty($bl_id)) {
			return $this->response($this->error('', 'REQUEST_BL_ID'));
		}
		$bundling_model = new BundlingModel();
		$info = $bundling_model->getBundlingDetail([ [ 'bl_id', '=', $bl_id ] ]);
		return $this->response($info);
		
	}
	
}