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


namespace addon\pintuan\api\controller;

use addon\pintuan\model\PintuanGroup as PintuanGroupModel;
use app\api\controller\BaseApi;

/**
 * 拼团组
 */
class Pintuangroup extends BaseApi
{
	
	/**
	 * 列表信息
	 */
	public function lists()
	{
		$goods_id = isset($this->params['goods_id']) ? $this->params['goods_id'] : 0;
		if (empty($goods_id)) {
			return $this->response($this->error('', 'REQUEST_GOODS_ID'));
		}
		
		$pintuan_group_model = new PintuanGroupModel();
		$condition = [
			[ 'ppg.goods_id', '=', $goods_id ],
			[ 'ppg.status', '=', 2 ],// 当前状态:0未支付 1拼团失败 2.组团中3.拼团成功
		];
		$list = $pintuan_group_model->getPintuanGoodsGroupList($condition);
		return $this->response($list);
	}
	
}