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



namespace addon\bargain\event;

use addon\bargain\model\Bargain;

/**
 * 商品营销活动信息
 */
class GoodsListPromotion
{

	/**
	 * 商品营销活动信息
	 * @param $param
	 * @return array
	 */
	public function handle($param)
	{
		if (empty($param[ 'promotion' ]) || $param[ 'promotion' ] != 'bargain') return [];

        $condition = [
            [ 'pg.status', '=', 1 ]
        ];
        if(!empty($param['site_id'])){
            $condition[] = [ 'pg.site_id', '=', $param['site_id'] ];
        }
		if (isset($param[ 'bargain_name' ]) && !empty($param[ 'bargain_name' ])) {
			$condition[] = ['pg.bargain_name', 'like', '%' . $param[ 'bargain_name' ] . '%'];
		}

        //城市分站id
        if(!empty($param['website_id'])){
            $condition[] = ['sku.website_id', '=', $param['website_id']];
        }

        $ailas = 'pg';
        $join = [
            [ 'goods_sku sku', 'pg.sku_id = sku.sku_id', 'inner'  ],
        ];
        $field = 'pg.id,pg.bargain_id,pg.floor_price,pg.bargain_stock,pg.site_id,pg.bargain_name,sku.sku_id,sku.price,sku.sku_name,sku.sku_image';
		$model = new Bargain();
		$list = $model->getBargainGoodsPageList($condition, $field, 'id asc', $param[ 'page' ], $param[ 'page_size' ], $ailas, $join);
		return $list;
	}
}