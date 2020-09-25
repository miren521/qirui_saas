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

namespace addon\present\event;

use addon\present\model\Present;
use app\model\goods\Goods;

/**
 * 同步库存
 */
class SyncStock
{

	public function handle($params)
	{
	    $sku_id = $params['sku_id'] ?? 0;
        $present_model = new Present();
	    //sku库存改变后 更新库存信息
	    if($sku_id > 0){
            $condition = array(
                ['sku_id', '=', $sku_id]
            );
            $goods_model = new Goods();
            $sku_info = $goods_model->getGoodsSkuInfo($condition, 'stock')['data'] ?? [];
            if(!empty($sku_info)){
                $result = $present_model->modifyPresentStock($condition, $sku_info['stock']);
                return $result;
            }
        }
        return $this->error();
	}
}