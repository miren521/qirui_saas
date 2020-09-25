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

use addon\goodscircle\model\GoodsCircle;
use app\api\controller\BaseApi;
use addon\goodscircle\model\Config as ConfigModel;
/**
 * 好物圈
 */
class Goods extends BaseApi
{
	
	/**
	 * 将商品同步到好物圈
	 */
	public function sync()
	{
		$config_model = new ConfigModel();
        $config = $config_model->getGoodscircleConfig();
        $config = $config['data'];
        if (isset($config['is_use']) && $config['is_use']) {
            $goods_circle = new GoodsCircle();
            $res = $goods_circle->importProduct($this->params['goods_id']);
            return $this->response($res);
        } else {
            return $this->response($this->error());
        }
	}
	
}