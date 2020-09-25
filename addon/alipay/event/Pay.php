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

declare (strict_types = 1);

namespace addon\alipay\event;

use addon\alipay\model\Pay as PayModel;
/**
 * 生成支付
 */
class Pay
{
	/**
	 * 支付方式及配置
	 */
	public function handle($param)
	{

        if($param["pay_type"] == "alipay"){
            if (in_array($param["app_type"], [ "h5", "app", "pc", "aliapp" ])) {
                $pay_model = new PayModel();
                $res = $pay_model->pay($param);
                return $res;
            }
        }
	}
}