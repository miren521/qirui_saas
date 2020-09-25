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


namespace addon\city\city\controller;

use app\model\goods\VirtualGoods as VirtualGoodsModel;

/**
 * 虚拟商品管理 控制器
 */
class Virtualgoods extends BaseCity
{
	/******************************* 正常商品列表及相关操作 ***************************/
	
	/**
	 * 商品列表
	 */
	public function lists()
	{
		$goods_model = new VirtualGoodsModel();
		if (request()->isAjax()) {
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', "");
			$search_text_type = input('search_text_type', "goods_name");
			$goods_state = input('goods_state', "");
			$verify_state = input('verify_state', "");
			$category_id = input('category_id', "");
			$brand_id = input('goods_brand', '');
			$goods_attr_class = input("goods_attr_class", "");
			$site_id = input("site_id", "");
			$condition = [ [ 'goods_class', '=', 2 ] ];
			if (!empty($search_text)) {
				$condition[] = [ $search_text_type, 'like', '%' . $search_text . '%' ];
			}
			if ($goods_state !== '') {
				$condition[] = [ 'goods_state', '=', $goods_state ];
			}
			if ($verify_state !== '') {
				$condition[] = [ 'verify_state', '=', $verify_state ];
			}
			if ($brand_id) {
				$condition[] = [ 'brand_id', '=', $brand_id ];
			}
			if ($goods_attr_class) {
				$condition[] = [ 'goods_attr_class', '=', $goods_attr_class ];
			}
			if (!empty($category_id)) {
				$condition[] = [ 'category_id|category_id_1|category_id_2|category_id_3', '=', $category_id ];
			}
			if (!empty($site_id)) {
				$condition[] = [ 'site_id', '=', $site_id ];
			}
			
			$res = $goods_model->getGoodsPageList($condition, $page_index, $page_size);
			return $res;
		} else {
			$this->forthMenu();
			$verify_state = $goods_model->getVerifyState();
			$this->assign("verify_state", $verify_state);
			return $this->fetch('virtualgoods/lists',[],$this->replace);
		}
	}
	
	/**
	 * 获取违规下架原因
	 * @return \multitype
	 */
	public function getVerifyStateRemark()
	{
		if (request()->isAjax()) {
			$goods_id = input("goods_id", 0);
			
			$goods_model = new VirtualGoodsModel();
			$res = $goods_model->getGoodsInfo([ [ 'goods_id', '=', $goods_id ], [ 'verify_state', '=', 10 ] ], 'verify_state_remark');
			return $res;
		}
	}
	
	/**
	 * 获取SKU商品列表
	 * @return \multitype
	 */
	public function getGoodsSkuList()
	{
		if (request()->isAjax()) {
			$goods_id = input("goods_id", 0);
			$goods_model = new VirtualGoodsModel();
			$res = $goods_model->getGoodsSkuList([ [ 'goods_id', '=', $goods_id ] ], 'sku_id,sku_name,price,stock,sale_num,sku_image,spec_name');
			return $res;
		}
	}
	
}