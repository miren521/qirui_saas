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


namespace app\model\goods;

use think\facade\Cache;
use app\model\BaseModel;

/**
 * 商品店内分类
 */
class GoodsShopCategory extends BaseModel
{
	
	/**
	 * 添加商品店内分类
	 * @param $data
	 * @return \multitype
	 */
	public function addShopCategory($data)
	{
		$category_id = model('goods_shop_category')->add($data);
		Cache::tag("goods_shop_category")->clear();
		return $this->success($category_id);
	}
	
	/**
	 * 修改商品店内分类
	 * @param $data
	 * @return \multitype
	 */
	public function editShopCategory($data)
	{
		$res = model('goods_shop_category')->update($data, [ [ 'category_id', '=', $data['category_id'] ] ]);
		Cache::tag("goods_shop_category")->clear();
		return $this->success($res);
	}
	
	/**
	 * 删除分类
	 * @param $category_id
	 * @return \multitype
	 */
	public function deleteShopCategory($category_id)
	{
		$goods_shop_category_info = $this->getShopCategoryInfo([
			[ 'category_id', '=', $category_id ]
		], "level");
		$goods_shop_category_info = $goods_shop_category_info['data'];
		$field = "category_id_" . $goods_shop_category_info['level'];
		$res = model('goods_shop_category')->delete([ [ $field, '=', $category_id ] ]);
		
		Cache::tag("goods_shop_category")->clear();
		return $this->success($res);
	}
	
	/**
	 * 获取商品店内分类信息
	 * @param array $condition
	 * @param string $field
	 */
	public function getShopCategoryInfo($condition, $field = 'category_id,category_name,short_name,pid,level,is_show,sort,image,keywords,description,category_id_1,category_id_2,category_full_name')
	{
		
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::get("goods_shop_category_getShopCategoryInfo_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('goods_shop_category')->getInfo($condition, $field);
		Cache::tag("goods_shop_category")->set("goods_shop_category_getShopCategoryInfo_" . $data, $res);
		return $this->success($res);
	}
	
	/**
	 * 获取商品店内分类列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return \multitype
	 */
	public function getShopCategoryList($condition = [], $field = 'category_id,category_name,short_name,pid,level,is_show,sort,image,category_id_1,category_id_2', $order = '', $limit = null)
	{
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::get("goods_shop_category_getShopCategoryList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('goods_shop_category')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("goods_shop_category")->set("goods_shop_category_getShopCategoryList_" . $data, $list);
		
		return $this->success($list);
	}
	
	/**
	 * 获取商品店内分类树结构
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return \multitype
	 */
	public function getShopCategoryTree($condition = [], $field = 'category_id,category_name,short_name,pid,level,is_show,sort,image,category_id_1,category_id_2', $order = '', $limit = null)
	{
		
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::get("goods_shop_category_getShopCategoryTree_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('goods_shop_category')->getList($condition, $field, $order, '', '', '', $limit);
		
		$goods_shop_category_list = [];
		
		//遍历一级商品店内分类
		foreach ($list as $k => $v) {
			if ($v['level'] == 1) {
				$goods_shop_category_list[] = $v;
				unset($list[ $k ]);
			}
		}
		
		$list = array_values($list);
		
		//遍历二级商品店内分类
		foreach ($list as $k => $v) {
			foreach ($goods_shop_category_list as $ck => $cv) {
				if ($v['level'] == 2 && $cv['category_id'] == $v['pid']) {
					$goods_shop_category_list[ $ck ]['child_list'][] = $v;
					unset($list[ $k ]);
				}
			}
		}
		
		Cache::tag("goods_shop_category")->set("goods_shop_category_getShopCategoryTree_" . $data, $list);
		
		return $this->success($goods_shop_category_list);
	}
	
	/**
	 * 获取商品店内分类分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return \multitype
	 */
	public function getShopCategoryPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = 'category_id,category_name,short_name,pid,level,is_show,sort,image,category_id_1,category_id_2,category_full_name')
	{
		$data = json_encode([ $condition, $field, $order, $page, $page_size ]);
		$cache = Cache::get("goods_shop_category_getShopCategoryPageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('goods_shop_category')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("goods_shop_category")->set("goods_shop_category_getShopCategoryPageList_" . $data, $list);
		return $this->success($list);
	}
	
}