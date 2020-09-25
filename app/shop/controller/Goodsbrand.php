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


namespace app\shop\controller;

use app\model\goods\GoodsBrand as GoodsBrandModel;

/**
 * 商品品牌管理 控制器
 */
class Goodsbrand extends BaseShop
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
				$condition[] = [ 'ngb.brand_name', 'like', '%' . $search_keys . '%' ];
			}
            $condition[] = ['ngb.site_id', 'in', [0, $this->site_id]];
			$goods_brand_model = new GoodsBrandModel();
			$list = $goods_brand_model->getBrandPageList($condition, $page_index, $page_size,'ngb.create_time desc','ngb.brand_id,ngb.brand_name,ngb.brand_initial,ngb.image_url,ngb.sort,ngb.create_time,ngb.site_id');
			return $list;
		} else {
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
			$data = [
				'site_id' => $this->site_id,
				'brand_name' => $brand_name,
				'brand_initial' => $brand_initial,
				'image_url' => $image_url,
				'sort' => $sort
			];
			$goods_brand_model = new GoodsBrandModel();
			$res = $goods_brand_model->addBrand($data);
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
			$data = [
				'brand_id' => $brand_id,
				'brand_name' => $brand_name,
				'brand_initial' => $brand_initial,
				'image_url' => $image_url,
				'sort' => $sort
			];
			$condition = array(
                [ 'brand_id', '=', $data['brand_id'] ] ,
                [ 'site_id', '=', $this->site_id]
            );
			$res = $goods_brand_model->editBrand($data, $condition);
			return $res;
		} else {
			$brand_id = input('brand_id', 0);
			$brand_info = $goods_brand_model->getBrandInfo([ [ 'brand_id', '=', $brand_id ] ]);
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
			$brand_id = input("brand_id", 0);
			$goods_brand_model = new GoodsBrandModel();
			$condition = [
				[ "brand_id", '=', $brand_id ],
                [ 'site_id', '=', $this->site_id]
			];
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
		    ['brand_id', '=', $brand_id],
            [ 'site_id', '=', $this->site_id]
        );
		$res = $goods_brand_model->modifyBrandSort($sort, $condition);
		return $res;
	}
}