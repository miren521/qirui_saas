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
use addon\pointexchange\model\OrderCreate as OrderCreateModel;

/**
 * 订单创建
 * @author Administrator
 *
 */
class Ordercreate extends BaseApi
{
	/**
	 * 创建订单
	 */
	public function create()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$order_create = new OrderCreateModel();
		$data = [
			'id' => isset($this->params['id']) ? $this->params['id'] : '',//兑换id
			'num' => isset($this->params['num']) ? $this->params['num'] : 1,//兑换数量(买几套)
			'member_id' => $this->member_id,
			'order_from' => $this->params['app_type'],
			'order_from_name' => $this->params['app_type_name'],
            'buyer_message' => $this->params['buyer_message'],
			'member_address' => isset($this->params["member_address"]) && !empty($this->params["member_address"]) ? json_decode($this->params["member_address"], true) : []
		];
		if (empty($data['id'])) {
			return $this->response($this->error('', '缺少必填参数商品数据'));
		}
		$res = $order_create->create($data);
		return $this->response($res);
	}
	

	
	/**
	 * 待支付订单 数据初始化
	 * @return string
	 */
	public function payment()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$order_create = new OrderCreateModel();
		$data = [
            'id' => isset($this->params['id']) ? $this->params['id'] : '',//兑换id
            'num' => isset($this->params['num']) ? $this->params['num'] : 1,//兑换数量(买几套)
            'member_id' => $this->member_id,
            'order_from' => $this->params['app_type'],
            'order_from_name' => $this->params['app_type_name'],
		];
		if (empty($data['id'])) {
			return $this->response($this->error('', '缺少必填参数商品数据'));
		}
		$res = $order_create->payment($data);
		return $this->response($res);
	}
	
}