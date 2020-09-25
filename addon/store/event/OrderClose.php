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

namespace addon\store\event;

use addon\store\model\StoreGoodsSku;
use app\model\order\OrderCommon;

/**
 * 关闭订单返还门店库存
 */
class OrderClose
{
    
        public function handle($data)
        {
            $order_model = new OrderCommon();
            $order_info_result = $order_model->getOrderInfo([ [ 'order_id', '=', $data['order_id'] ] ], 'site_id,member_id,delivery_store_id');
            $order_info = $order_info_result['data'];

            if($order_info["delivery_store_id"] > 0){
                //减少库存
                $order_goods_list_result = $order_model->getOrderGoodsList([ [ 'order_id', '=', $data['order_id'] ] ], "num,sku_id");
                $order_goods_list = $order_goods_list_result["data"];
                foreach($order_goods_list as $k => $v){
                    $store_goods_sku_model = new StoreGoodsSku();
                    $stock_result = $store_goods_sku_model->incStock(["store_id" => $order_info["delivery_store_id"], "sku_id" => $v["sku_id"], "store_stock" => $v["num"]]);
                    if($stock_result["code"] < 0){
                        return $stock_result;
                    }
                }
            }

        }
}