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


namespace app\model\goods;

use app\model\BaseModel;

/**
 * 商品库存
 */
class GoodsStock extends BaseModel
{
	
	/**
	 * 增加库存
	 * @param $param
	 */
	public function incStock($param)
	{
		$condition = array(
			[ "sku_id", "=", $param["sku_id"] ]
		);
		$num = $param["num"];
		$sku_info = model("goods_sku")->getInfo($condition, "goods_id,stock");
		if (empty($sku_info))
			return $this->error(-1, "");
		
		//编辑sku库存
		$result = model("goods_sku")->setInc($condition, "stock", $num);
		
		//编辑商品总库存(暂不考虑查询判断)
		$goods_condition = array(
			[ "goods_id", "=", $sku_info["goods_id"] ]
		);
		$res = model("goods")->setInc($goods_condition, "goods_stock", $num);
		//同步库存
		event('SyncStock', ['sku_id' => $param["sku_id"]]);
		return $this->success($result);
	}
	
	/**
	 * 减少库存
	 * @param $param
	 */
	public function decStock($param)
	{
        model("goods_sku")->startTrans();
        //循环生成多个订单
        try {
            $condition = array(
                [ "sku_id", "=", $param["sku_id"] ]
            );
            $num = $param["num"];
            //编辑sku库存
            $result = model("goods_sku")->setDec($condition, "stock", $num);
            if ($result === false){
                model("goods_sku")->rollback();
                return $this->error();
            }

            $sku_info = model("goods_sku")->getInfo($condition, "goods_id,stock,sku_name");
            if (empty($sku_info)){
                model("goods_sku")->rollback();
                return $this->error();
            }

            if ($sku_info["stock"] < 0){
                model("goods_sku")->rollback();
                return $this->error('', $sku_info["sku_name"] . "库存不足!");
            }
            //编辑商品总库存(暂不考虑查询判断)
            $goods_condition = array(
                [ "goods_id", "=", $sku_info["goods_id"] ]
            );
            $res = model("goods")->setDec($goods_condition, "goods_stock", $num);

            //同步库存
            event('SyncStock', ['sku_id' => $param["sku_id"]]);
            model("goods_sku")->commit();
            return $this->success($res);
        } catch ( \Exception $e ) {
            model("goods_sku")->rollback();
            return $this->error('', $e->getMessage());
        }
	}

}