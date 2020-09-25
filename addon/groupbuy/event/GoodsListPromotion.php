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



namespace addon\groupbuy\event;

use addon\groupbuy\model\Groupbuy;
use addon\groupbuy\model\Pintuan;

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
		if (empty($param['promotion']) || $param['promotion'] != 'groupbuy') return [];

        $condition[] = ['pg.status', '=', 2];
        if(!empty($param['site_id'])) $condition[] = ['pg.site_id', '=', $param['site_id']];
		if (isset($param['goods_name']) && !empty($param['goods_name'])) {
			$condition[] = ['pg.goods_name', 'like', '%' . $param['goods_name'] . '%'];
		}
		$model = new Groupbuy();
        $field = 'pg.groupbuy_id,pg.groupbuy_price,pg.sell_num,pg.site_id,
        sku.sku_id,sku.price,sku.sku_name,sku.sku_image,g.goods_id,g.goods_name,g.goods_image,g.goods_stock';

        $alias = 'pg';
        $join = [
            [ 'goods g', 'pg.sku_id = g.sku_id', 'inner' ],
            [ 'goods_sku sku', 'pg.sku_id = sku.sku_id', 'inner' ]
        ];
        //城市分站id
        if(!empty($param['website_id'])){
            $condition[] = ['g.website_id', '=', $param['website_id']];
        }
		$list = $model->getGroupbuyGoodsPageList($condition, $param['page'], $param['page_size'], 'pg.groupbuy_id desc', $field, $alias, $join);
		return $list;
	}
}