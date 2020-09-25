<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
declare (strict_types = 1);

namespace addon\alipay\event;

use addon\alipay\model\Pay as PayModel;
/**
 * 原路退款
 */
class PayRefund
{
    /**
     * 关闭支付
     */
    public function handle($params)
    {
        if($params["pay_info"]["pay_type"] == "alipay"){
            $pay_model = new PayModel();
            $result = $pay_model->refund($params);
            return $result;
        }
    }
}