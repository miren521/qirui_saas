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

namespace addon\memberrecharge\event;
use addon\memberrecharge\model\MemberrechargeOrder;

/**
 * 充值订单回调
 */
class MemberRechargeOrderPayNotify
{

    public function handle($data)
    {
        $model = new MemberrechargeOrder();
        $res = $model->orderPay($data);
        return $res;
    }

}