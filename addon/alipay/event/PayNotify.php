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

declare (strict_types=1);

namespace addon\alipay\event;

use addon\alipay\model\Pay as PayModel;

/**
 * 支付回调
 */
class PayNotify
{
    /**
     * 支付方式及配置
     */
    public function handle($param = [])
    {
        try {
            $pay_model = new PayModel();
            $pay_model->payNotify();
        } catch (\Exception $e) {
            return '';
        }
    }
}