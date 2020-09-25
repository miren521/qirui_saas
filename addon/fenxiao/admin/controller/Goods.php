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


namespace addon\fenxiao\admin\controller;

use addon\fenxiao\model\FenxiaoGoodsSku as FenxiaoGoodsSkuModel;
use addon\fenxiao\model\FenxiaoLevel as FenxiaoLevelModel;
use app\model\goods\Goods as GoodsModel;
use app\admin\controller\BaseAdmin;
use app\model\goods\GoodsBrand;

/**
 *  分销商品
 */
class Goods extends BaseAdmin
{
	
	/**
	 * 分销等级列表
	 */
	public function lists()
	{
		$model = new GoodsModel();
		
		if (request()->isAjax()) {
			
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$condition = [ [ 'is_fenxiao', '=', 1 ] ];
			$search_text_type = input('search_text_type', "goods_name");//店铺名称或者商品名称
			$search_text = input('search_text', "");
			if (!empty($search_text)) {
				$condition[] = [ $search_text_type, 'like', '%' . $search_text . '%' ];
			}
			
			$goods_class = input('goods_class', "");//商品种类
			if ($goods_class !== "") {
				$condition[] = [ 'goods_class', '=', $goods_class ];
			}
			
			$goods_state = input('goods_state', "");//商品状态
			if ($goods_state !== '') {
				$condition[] = [ 'goods_state', '=', $goods_state ];
			}
			
			$category_id = input('category_id', "");//分类ID
			if (!empty($category_id)) {
				$condition[] = [ 'category_id|category_id_1|category_id_2|category_id_3', '=', $category_id ];
			}
			
			$brand_id = input('goods_brand', '');//商品品牌
			if (!empty($brand_id)) {
				$condition[] = [ 'brand_id', '=', $brand_id ];
			}
			$list = $model->getGoodsPageList($condition, $page_index, $page_size);
			return $list;
		} else {
			//商品品牌
			$goods_brand_model = new GoodsBrand();
			$list = $goods_brand_model->getBrandList('', 'brand_id,brand_name', 'sort asc');
			$this->assign('goods_brand', $list['data']);
			return $this->fetch('goods/lists');
		}
	}
	
	public function detail()
	{
		$goods_id = input('goods_id');
		$goods_model = new GoodsModel();
		$fenxiao_sku_model = new FenxiaoGoodsSkuModel();
		$fenxiao_leve_model = new FenxiaoLevelModel();
		$goods_info = $goods_model->getGoodsDetail($goods_id);
		$fenxiao_skus = $fenxiao_sku_model->getSkuList([ 'goods_id' => $goods_id ]);
		$skus = [];
		foreach ($fenxiao_skus['data'] as $fenxiao_sku) {
			$skus[ $fenxiao_sku['level_id'] . '_' . $fenxiao_sku['sku_id'] ] = $fenxiao_sku;
		}
		$goods_info['data']['fenxiao_skus'] = $skus;
		$fenxiao_level = $fenxiao_leve_model->getLevelList();
		$this->assign('fenxiao_level', $fenxiao_level['data']);
		$this->assign('goods_info', $goods_info['data']);
		return $this->fetch('goods/detail');
	}
}