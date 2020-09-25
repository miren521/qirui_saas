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

use app\api\controller\BaseApi;
use addon\pintuan\model\PintuanOrderCreate as OrderCreateModel;

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
			'id' => isset($this->params['pintuan_goods_id']) ? $this->params['pintuan_goods_id'] : '',//拼团商品id
			'num' => isset($this->params['num']) ? $this->params['num'] : 1,//拼团商品数量(买几套)
            'group_id' => isset($this->params['group_id']) ? $this->params['group_id'] : 0,//拼团组id
			'member_id' => $this->member_id,
            'is_balance' => isset($this->params['is_balance']) ? $this->params['is_balance'] : 0,//是否使用余额
            'pay_password' => isset($this->params['pay_password']) ? $this->params['pay_password'] : '',//支付密码
			'order_from' => $this->params['app_type'],
			'order_from_name' => $this->params['app_type_name'],
            'buyer_message' => isset($this->params["buyer_message"]) && !empty($this->params["buyer_message"]) ? json_decode($this->params["buyer_message"], true) : [],
            'delivery' => isset($this->params["delivery"]) && !empty($this->params["delivery"]) ? json_decode($this->params["delivery"], true) : [],
			'member_address' => isset($this->params["member_address"]) && !empty($this->params["member_address"]) ? json_decode($this->params["member_address"], true) : [],

            'platform_coupon_id' => $this->params["platform_coupon_id"] ?? 0,
            'latitude' => $this->params["latitude"] ?? null,
            'longitude' => $this->params["longitude"] ?? null,

            'invoice' => isset($this->params[ "invoice" ]) && !empty($this->params[ "invoice" ]) ? json_decode($this->params[ "invoice" ], true) : [],
		];
		if (empty($data['id'])) {
			return $this->response($this->error('', '缺少必填参数商品数据'));
		}
		$res = $order_create->create($data);
		return $this->response($res);
	}
	
	/**
	 * 计算信息
	 */
	public function calculate()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		$order_create = new OrderCreateModel();
		$data = [
            'id' => isset($this->params['pintuan_goods_id']) ? $this->params['pintuan_goods_id'] : '',//拼团商品id
            'num' => isset($this->params['num']) ? $this->params['num'] : 1,//拼团商品数量(买几套)
            'group_id' => isset($this->params['group_id']) ? $this->params['group_id'] : 0,//拼团组id
			'member_id' => $this->member_id,
            'is_balance' => isset($this->params['is_balance']) ? $this->params['is_balance'] : 0,//是否使用余额
			'order_from' => $this->params['app_type'],
			'order_from_name' => $this->params['app_type_name'],
			'delivery' => isset($this->params["delivery"]) && !empty($this->params["delivery"]) ? json_decode($this->params["delivery"], true) : [],
			'member_address' => isset($this->params["member_address"]) && !empty($this->params["member_address"]) ? json_decode($this->params["member_address"], true) : [],

            'platform_coupon_id' => $this->params["platform_coupon_id"] ?? 0,
            'latitude' => $this->params["latitude"] ?? null,
            'longitude' => $this->params["longitude"] ?? null,

            'invoice' => isset($this->params[ "invoice" ]) && !empty($this->params[ "invoice" ]) ? json_decode($this->params[ "invoice" ], true) : [],
		];
		if (empty($data['id'])) {
			return $this->response($this->error('', '缺少必填参数商品数据'));
		}
		$res = $order_create->calculate($data);
		return $this->response($this->success($res));
		
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
            'id' => isset($this->params['pintuan_goods_id']) ? $this->params['pintuan_goods_id'] : '',//拼团商品id
            'num' => isset($this->params['num']) ? $this->params['num'] : 1,//拼团商品数量(买几套)
            'group_id' => isset($this->params['group_id']) ? $this->params['group_id'] : 0,//拼团组id
			'member_id' => $this->member_id,
            'is_balance' => isset($this->params['is_balance']) ? $this->params['is_balance'] : 0,//是否使用余额
			'order_from' => $this->params['app_type'],
			'order_from_name' => $this->params['app_type_name'],

            'platform_coupon_id' => $this->params["platform_coupon_id"] ?? 0,
            'latitude' => $this->params["latitude"] ?? null,
            'longitude' => $this->params["longitude"] ?? null,
		];
		if (empty($data['id'])) {
			return $this->response($this->error('', '缺少必填参数商品数据'));
		}
		$res = $order_create->orderPayment($data);
		return $this->response($res);
	}
	
}