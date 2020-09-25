<?php
/**
 * Shopcategory.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

namespace app\api\controller;

use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;

/**
 * 店铺商品分类
 * Class Shopcategory
 * @package app\api\controller
 */
class Shopgoodscategory extends BaseApi
{
	/**
	 * 树状结构信息
	 */
	public function tree()
	{
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		$level = isset($this->params['level']) ? $this->params['level'] : 2;// 分类等级 1 2
		$template = isset($this->params['template']) ? $this->params['template'] : 2;// 模板 1：无图，2：有图，3：有商品
		if (empty($site_id)) {
			return $this->response($this->error('', 'REQUEST_SITE_ID'));
		}
		$condition = [
			[ 'site_id', "=", $site_id ],
			[ 'is_show', '=', 1 ],
			[ 'level', '<=', $level ]
		];
		$goods_category_model = new GoodsShopCategoryModel();
		$list = $goods_category_model->getShopCategoryTree($condition);
		return $this->response($list);
	}
	
}