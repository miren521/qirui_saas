<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */


namespace addon\fenxiao\event;

use app\model\goods\Goods as GoodsModel;

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
		if (empty($param[ 'promotion' ]) || $param[ 'promotion' ] != 'fenxiao') return [];

		$condition = [
			['is_delete', '=', 0],
			['is_fenxiao', '=', 1],
			['goods_state', '=', 1]
		];
		if(!empty($param['site_id'])) $condition[] = ['site_id', '=', $param['site_id']];
		if (!empty($param[ 'goods_name' ])) {
			$condition[] = ['goods_name', 'like', '%' . $param[ 'goods_name' ] . '%'];
		}
		if (!empty($param[ 'category_id' ])) {
			$condition[] = ['category_id', 'like', [$param[ 'category_id' ], '%' . $param[ 'category_id' ] . ',%', '%' . $param[ 'category_id' ], '%,' . $param[ 'category_id' ] . ',%'], 'or'];
		}

        //城市分站id
        if(!empty($param['website_id'])){
            $condition[] = ['website_id', '=', $param['website_id']];
        }
		$model = new GoodsModel();
		$list = $model->getGoodsPageList($condition, $param[ 'page' ], $param[ 'page_size' ]);
		return $list;
	}
}