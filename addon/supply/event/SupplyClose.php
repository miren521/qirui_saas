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

namespace addon\supply\event;

use addon\supply\model\goods\Goods;

/**
 * 供应商关闭
 * @author Administrator
 *
 */
class SupplyClose
{
    public function handle($data)
    {
        $site_id = $data["site_id"];
        //将供应商下的商品全部下架
        $goods_model = new Goods();
        $goods_result = $goods_model->lockup([["site_id", "=", $site_id]], "供应商关闭");
        return $goods_result;
    }
}
