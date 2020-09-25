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


namespace addon\pointexchange\api\controller;

use app\api\controller\BaseApi;
use addon\pointexchange\model\Exchange as ExchangeModel;

/**
 * 积分兑换
 */
class Goods extends BaseApi
{
	
	/**
	 * 详情信息
	 */
	public function detail()
	{
		$id = isset($this->params['id']) ? $this->params['id'] : 1;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		$exchange_model = new ExchangeModel();
		$info = $exchange_model->getExchangeGoodsDetail($id);
		return $this->response($info);
	}
	
	public function page()
	{
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$type = isset($this->params['type']) ? $this->params['type'] : 1;//兑换类型，1：礼品，2：优惠券，3：红包
		$condition = [
			[ 'state', '=', 1 ],
			[ 'type', '=', $type ],
		];
		$order = 'create_time desc';
		$field = 'id,type,type_name,name,image,stock,point,market_price,price,delivery_price,balance';
		
		$exchange_model = new ExchangeModel();
		$list = $exchange_model->getExchangePageList($condition, $page, $page_size, $order, $field);
		return $this->response($list);
	}
	
}