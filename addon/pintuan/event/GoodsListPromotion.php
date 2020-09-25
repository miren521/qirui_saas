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



namespace addon\pintuan\event;

use addon\pintuan\model\Pintuan;

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
		if (empty($param['promotion']) || $param['promotion'] != 'pintuan') return [];

        $condition[] = ['pp.status', '=', 1];
        if(!empty($param['site_id'])) $condition[] = ['pp.site_id', '=', $param['site_id']];
		if (isset($param['pintuan_name']) && !empty($param['pintuan_name'])) {
			$condition[] = ['pp.pintuan_name', 'like', '%' . $param['pintuan_name'] . '%'];
		}
        //城市分站id
		if(!empty($param['website_id'])){
            $condition[] = ['g.website_id', '=', $param['website_id']];
        }
		$model = new Pintuan();

        $alias = 'pp';
        $join = [
            [ 'promotion_pintuan_goods ppg', 'pp.pintuan_id = ppg.pintuan_id', 'inner' ],
            [ 'goods g', 'ppg.sku_id = g.sku_id', 'inner' ],
            [ 'goods_sku sku', 'ppg.sku_id = sku.sku_id', 'inner' ],
        ];
        $field = 'pp.*,g.goods_name,g.goods_image,g.goods_stock,ppg.id';
        $order = 'pp.pintuan_id desc';
		$list = $model->getPintuanGoodsPageList($condition, $param['page'], $param['page_size'], $order, $field, $alias, $join);
		return $list;
	}
}