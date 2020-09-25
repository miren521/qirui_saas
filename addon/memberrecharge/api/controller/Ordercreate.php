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


namespace addon\memberrecharge\api\controller;

use app\api\controller\BaseApi;
use addon\memberrecharge\model\MemberrechargeOrderCreate as OrderCreateModel;

/**
 * 订单创建
 * @author Administrator
 *
 */
class Ordercreate extends BaseApi
{
    /**
     * 创建订单
     * @return string
     */
    public function create()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $order_create = new OrderCreateModel();
        $data = [
            'recharge_id' => isset($this->params['recharge_id']) ? $this->params['recharge_id'] : '',//套餐id
            'member_id' => $this->member_id,
            'order_from' => $this->params['app_type'],
            'order_from_name' => $this->params['app_type_name'],
        ];

        if (empty($data['recharge_id'])) {
            return $this->response($this->error('', '缺少必填参数商品数据'));
        }
        $res = $order_create->create($data);
        return $this->response($res);
    }

}