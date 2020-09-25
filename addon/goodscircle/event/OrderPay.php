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



namespace addon\goodscircle\event;

use addon\goodscircle\model\Config;
use addon\goodscircle\model\GoodsCircle;

/**
 * 活动展示
 */
class OrderPay
{

    /**
     * 订单支付
     * @param unknown $order
     * @return multitype:
     */
	public function handle($param = [])
	{
        $config_model = new Config();
        $config = $config_model->getGoodscircleConfig($param['site_id']);
        $config = $config['data'];
        if (isset($config['is_use']) && $config['is_use']) {
            $goods_circle = new GoodsCircle($param['site_id']);
            $goods_circle->importOrder($param['order_id']);
        }
	}
}