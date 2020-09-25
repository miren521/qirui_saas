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


namespace addon\fenxiao\model;

use app\model\BaseModel;
use app\model\goods\Goods as GoodsModel;

/**
 * 分销商品
 */
class FenxiaoGoods extends BaseModel
{
	
	/**
	 * @return array
	 */
	public function editGoodsFenxiao($data, $condition)
	{
		$re = model('goods')->update($data, $condition);
		return $this->success($re);
	}


    /**
     * 修改分销状态
     * @param $goods_ids
     * @param $is_fenxiao
     * @param $site_id
     * @return array
     */
	public function modifyGoodsFenxiaoStatus($goods_id, $is_fenxiao, $site_id)
	{
	    $fenxiao_goods_skus = model('fenxiao_goods_sku')->getList([ [ 'goods_id', '=', $goods_id ] ]);
        model('goods')->startTrans();
	    try {
            if (empty($fenxiao_goods_skus)) {
                $level_list = model('fenxiao_level')->getList();
                $goods_model = new GoodsModel();
                $goods_info = $goods_model->getGoodsDetail($goods_id);
                $fenxiao_goods_sku_data = [];
                foreach ($level_list as $level) {
                    foreach ($goods_info['data']['sku_data'] as $sku) {
                        $fenxiao_sku = [
                            'goods_id' => $goods_id,
                            'level_id' => $level['level_id'],
                            'sku_id' => $sku['sku_id'],
                            'one_rate' => $level['one_rate'],
                            'one_money' => 0,
                            'two_rate' => $level['two_rate'],
                            'two_money' => 0,
                            'three_rate' => $level['three_rate'],
                            'three_money' => 0,
                        ];
                        $fenxiao_goods_sku_data[] = $fenxiao_sku;
                    }
                }
                model('fenxiao_goods_sku')->addList($fenxiao_goods_sku_data);
            }

            model('goods')->update([ 'is_fenxiao' => $is_fenxiao ], [ [ 'goods_id', '=', $goods_id ], [ 'site_id', '=', $site_id ] ]);
            model('goods')->commit();
            return $this->success(1);
	    } catch (\Exception $e) {
            model('goods')->rollback();
            return $this->error($e->getMessage());
        }
	}
}