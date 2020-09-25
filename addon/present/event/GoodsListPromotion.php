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



namespace addon\present\event;

use addon\present\model\present;
use addon\present\model\Pintuan;

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
		if (empty($param['promotion']) || $param['promotion'] != 'present') return [];

        $condition[] = ['pg.status', '=', 2];
        if(!empty($param['site_id'])) $condition[] = ['pg.site_id', '=', $param['site_id']];
		if (isset($param['goods_name']) && !empty($param['goods_name'])) {
			$condition[] = ['pg.goods_name', 'like', '%' . $param['goods_name'] . '%'];
		}
		$model = new present();
        $field = 'pg.present_id,pg.present_price,pg.sale_num,pg.site_id,
        sku.sku_id,sku.price,sku.sku_name,sku.sku_image,g.goods_id,g.goods_name,g.goods_image,g.goods_stock';

        $alias = 'pg';
        $join = [
            [ 'goods g', 'pg.sku_id = g.sku_id', 'inner' ],
            [ 'goods_sku sku', 'pg.sku_id = sku.sku_id', 'inner' ]
        ];

		$list = $model->getPresentGoodsPageList($condition, $param['page'], $param['page_size'], 'pg.present_id desc', $field, $alias, $join);
		return $list;
	}
}