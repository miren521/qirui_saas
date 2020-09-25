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

namespace app\event;

use app\model\goods\Goods;

/**
 * 店铺关闭
 * @author Administrator
 *
 */
class ShopClose
{
    public function handle($data)
    {
        $site_id = $data["site_id"];
        //将店铺下的商品全部下架
//        $goods_model = new Goods();
//        $goods_result = $goods_model->lockup([["site_id", "=", $site_id]], "店铺关闭");
//        return $goods_result;
    }
}
