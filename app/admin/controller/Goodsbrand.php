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


namespace app\admin\controller;

use app\model\goods\GoodsBrand as GoodsBrandModel;

/**
 * 商品品牌管理 控制器
 */
class Goodsbrand extends BaseAdmin
{
	/**
	 * 商品品牌列表
	 */
	public function lists()
	{
		if (request()->isAjax()) {
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_keys = input('search_keys', "");
			$condition = [];
			if (!empty($search_keys)) {
				$condition[] = ['ngb.brand_name', 'like', '%' . $search_keys . '%'];
			}

			$goods_brand_model = new GoodsBrandModel();
			$list = $goods_brand_model->getBrandPageList($condition, $page_index, $page_size);

			return $list;
		} else {
			$this->forthMenu();
			return $this->fetch('goodsbrand/lists');
		}
	}

	/**
	 * 商品品牌添加
	 */
	public function addBrand()
	{
		if (request()->isAjax()) {
			$brand_name = input('brand_name', '');
			$brand_initial = input('brand_initial', '');
			$image_url = input('image_url', "");
			$sort = input('sort', 0);
			$is_recommend = input('is_recommend', '');
			$data = [
				'brand_name' => $brand_name,
				'brand_initial' => $brand_initial,
				'image_url' => $image_url,
				'sort' => $sort,
				'is_recommend' => $is_recommend
			];
			$goods_brand_model = new GoodsBrandModel();
			$res = $goods_brand_model->addBrand($data);
			$this->addLog("添加商品品牌:" . $brand_name);
			return $res;
		} else {
			return $this->fetch('goodsbrand/add_brand');
		}
	}

	/**
	 * 商品品牌编辑
	 */
	public function editBrand()
	{
		$goods_brand_model = new GoodsBrandModel();
		if (request()->isAjax()) {
			$brand_id = input('brand_id', 0);
			$brand_name = input('brand_name', '');
			$brand_initial = input('brand_initial', '');
			$image_url = input('image_url', "");
			$sort = input('sort', 0);
			$is_recommend = input('is_recommend', '');
			$data = [
				'brand_id' => $brand_id,
				'brand_name' => $brand_name,
				'brand_initial' => $brand_initial,
				'image_url' => $image_url,
				'sort' => $sort,
				'is_recommend' => $is_recommend
			];
			$condition = array(
			    ['brand_id', '=', $brand_id]
            );
			$res = $goods_brand_model->editBrand($data, $condition);
			$this->addLog("编辑商品品牌:" . $brand_name);
			return $res;
		} else {
			$brand_id = input('brand_id', 0);
			$brand_info = $goods_brand_model->getBrandInfo([['brand_id', '=', $brand_id]]);

			$brand_info = $brand_info['data'];
			$this->assign("brand_info", $brand_info);
			return $this->fetch('goodsbrand/edit_brand');
		}
	}

	/**
	 * 商品品牌删除
	 */
	public function deleteBrand()
	{
		if (request()->isAjax()) {
			$brand_ids = input("brand_ids", 0);
			$goods_brand_model = new GoodsBrandModel();
			$condition = [
				["brand_id", 'in', $brand_ids]
			];
			$this->addLog("删除商品品牌id:" . $brand_ids);
			$res = $goods_brand_model->deleteBrand($condition);
			return $res;
		}
	}

	/**
	 * 修改排序
	 */
	public function modifySort()
	{
		$sort = input('sort', 0);
		$brand_id = input('brand_id', 0);
		$goods_brand_model = new GoodsBrandModel();
        $condition = array(
            ['brand_id', '=', $brand_id]
        );
		$res = $goods_brand_model->modifyBrandSort($sort, $condition);
		return $res;
	}

	/**
	 * 转移品牌
	 */
	public function modifySite()
	{
		$brand_ids = input('brand_ids', 0);
		$condition = [
			["brand_id", 'in', $brand_ids]
		];
		$goods_brand_model = new GoodsBrandModel();
		$res = $goods_brand_model->modifyBrandSite(0, $condition);
		return $res;
	}
}
