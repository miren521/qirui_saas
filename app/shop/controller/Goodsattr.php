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

use app\model\goods\GoodsAttribute as GoodsAttributeModel;
use app\model\shop\Shop;

/**
 * 商品类型/属性管理 控制器
 */
class Goodsattr extends BaseShop
{
	/**
	 * 商品类型列表
	 */
	public function lists()
	{
		if (request()->isAjax()) {
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_keys = input('search_keys', "");
			$condition = [ [ 'site_id', '=', 0 ] ];
			if (!empty($search_keys)) {
				$condition[] = [ 'class_name', 'like', '%' . $search_keys . '%' ];
			}
			$goods_attr_model = new GoodsAttributeModel();
			$list = $goods_attr_model->getAttrClassPageList($condition, $page_index, $page_size);
			return $list;
		} else {
			return $this->fetch('goodsattr/lists');
		}
	}
	
	/**
	 * 商品类型编辑
	 */
	public function editAttr()
	{
		$goods_attr_model = new GoodsAttributeModel();
		$class_id = input("class_id", 0);
		$this->assign("class_id", $class_id);
		
		//商品类型信息
		$attr_class_info = $goods_attr_model->getAttrClassInfo([ [ 'class_id', '=', $class_id ] ]);
		$attr_class_info = $attr_class_info['data'];
		$this->assign("attr_class_info", $attr_class_info);
		
		return $this->fetch('goodsattr/edit_attr');
	}
	
	/**
	 * 添加商品属性
	 */
	public function addAttribute()
	{
		if (request()->isAjax()) {
			
			$attr_name = input('attr_name', "");// 属性名称
			$attr_class_id = input('attr_class_id', 0);// 商品类型id
			$attr_class_name = input('attr_class_name', "");// 商品类型名称
			$sort = input('sort', 0);// 属性排序号
			$is_spec = input('is_spec', 0);// 是否是规格属性
			$attr_value_list = input('attr_value_list', "");// 属性值列表（'',''隔开注意键值对）
			$attr_type = input('attr_type', 0);// 属性类型  （1.单选 2.多选3. 输入 注意输入不参与筛选）
			
			$shop = new Shop();
			$shop_info = $shop->getShopInfo([ [ 'site_id', '=', $this->site_id ] ], 'site_name');
			$shop_info = $shop_info['data'];
			$site_name = $shop_info['site_name'];// 站点名称
			
			$data = [
				'attr_name' => $attr_name,
				'attr_class_id' => $attr_class_id,
				'attr_class_name' => $attr_class_name,
				'sort' => $sort,
				'is_spec' => $is_spec,
				'attr_value_list' => $attr_value_list,
				'attr_type' => $attr_type,
				'site_id' => $this->site_id,
				'site_name' => $site_name
			];
			
			$goods_attr_model = new GoodsAttributeModel();
			return $goods_attr_model->addAttribute($attr_class_id, $data);
		}
		
	}
	
	/**
	 * 修改商品属性
	 */
	public function editAttribute()
	{
		if (request()->isAjax()) {
			
			$attr_id = input('attr_id', "");// 属性id
			$attr_name = input('attr_name', "");// 属性名称
			$attr_class_id = input('attr_class_id', 0);// 商品类型id
			$attr_class_name = input('attr_class_name', "");// 商品类型名称
			$sort = input('sort', 0);// 属性排序号
			$is_spec = input('is_spec', 0);// 是否是规格属性
			$attr_value_list = input('attr_value_list', "");// 属性值列表（'',''隔开注意键值对）
			$attr_type = input('attr_type', 0);// 属性类型  （1.单选 2.多选3. 输入 注意输入不参与筛选）
			$site_id = input('site_id', $this->site_id);// 站点id
			
			$data = [
				'attr_id' => $attr_id,
				'attr_name' => $attr_name,
				'attr_class_id' => $attr_class_id,
				'attr_class_name' => $attr_class_name,
				'sort' => $sort,
				'is_spec' => $is_spec,
				'attr_value_list' => $attr_value_list,
				'attr_type' => $attr_type,
				'site_id' => $site_id
			];
			$goods_attr_model = new GoodsAttributeModel();
			return $goods_attr_model->editAttribute($attr_class_id, $data);
		}
		
	}
	
	/**
	 * 删除属性、属性值
	 * @return \multitype
	 */
	public function deleteAttribute()
	{
		if (request()->isAjax()) {
			$attr_class_id = input('attr_class_id', 0);// 商品类型id
			$attr_id = input('attr_id', 0);// 属性id
			$goods_attr_model = new GoodsAttributeModel();
			$res = $goods_attr_model->deleteAttribute($attr_class_id, $attr_id);
			return $res;
		}
	}
	
	/**
	 * 获取属性、属性值详情
	 * @return \multitype
	 */
	public function getAttributeDetail()
	{
		if (request()->isAjax()) {
			$attr_class_id = input('attr_class_id', 0);// 商品类型id
			$attr_id = input('attr_id', 0);// 属性id
			
			$goods_attr_model = new GoodsAttributeModel();
			$attr_info = $goods_attr_model->getAttributeInfo([ [ 'attr_class_id', '=', $attr_class_id ], [ 'attr_id', '=', $attr_id ] ]);
			$attr_info = $attr_info['data'];
			if (!empty($attr_info)) {
				$attr_value_list = $goods_attr_model->getAttributeValueList([ [ 'attr_class_id', '=', $attr_class_id ], [ 'attr_id', '=', $attr_id ] ]);
				$attr_value_list = $attr_value_list['data'];
				$attr_info['value'] = $attr_value_list;
				return success(0, '', $attr_info);
			} else {
				return error(-1, "未查询到属性信息");
			}
		}
	}
	
	/**
	 * 获取商品属性列表
	 * @return \multitype
	 */
	public function getAttributeList()
	{
		if (request()->isAjax()) {
			$attr_class_id = input('attr_class_id', 0);// 商品类型id
			$goods_attr_model = new GoodsAttributeModel();
			return $goods_attr_model->getAttributeList([ [ 'site_id', 'in', ("0,$this->site_id") ], [ 'attr_class_id', '=', $attr_class_id ] ]);
		}
	}
	
	/**
	 * 添加属性值
	 * @return \multitype
	 */
	public function addAttributeValue()
	{
		if (request()->isAjax()) {
			$attr_class_id = input('attr_class_id', 0);// 商品类型id
			$value = input('value', "");
			if (!empty($value)) {
				$value = json_decode($value, true);
				$data = [];
				foreach ($value as $k => $v) {
					$item = [
						'attr_value_name' => $v['attr_value_name'],
						'attr_id' => $v['attr_id'],
						'attr_class_id' => $v['attr_class_id'],
						'sort' => $v['sort']
					];
					$data[] = $item;
				}
				$goods_attr_model = new GoodsAttributeModel();
				$res = $goods_attr_model->addAttributeValue($attr_class_id, $data);
				return $res;
			}
		} else {
			return error(-1, "缺少value");
		}
	}
	
	/**
	 * 修改商品属性值
	 */
	public function editAttributeValue()
	{
		if (request()->isAjax()) {
			
			$attr_class_id = input('attr_class_id', 0);// 商品类型id
			$data = input('data', "");// 属性值数组对象
			if (!empty($data)) {
				$data = json_decode($data, true);
				$goods_attr_model = new GoodsAttributeModel();
				foreach ($data as $k => $v) {
					$item = [
						'attr_value_id' => $v['attr_value_id'],
						'attr_value_name' => $v['attr_value_name'],
						'attr_id' => $v['attr_id'],
						'attr_class_id' => $v['attr_class_id'],
						'sort' => $v['sort'],
					];
					$res = $goods_attr_model->editAttributeValue($attr_class_id, $item);
				}
				return $res;
			} else {
				return error(-1, "缺少data");
			}
			
		}
		
	}
	
	/**
	 * 删除属性值
	 * @return \multitype
	 */
	public function deleteAttributeValue()
	{
		if (request()->isAjax()) {
			$attr_class_id = input('attr_class_id', 0);// 商品类型id
			$attr_value_id_arr = input('attr_value_id_arr', 0);
			$goods_attr_model = new GoodsAttributeModel();
			$res = $goods_attr_model->deleteAttributeValue($attr_class_id, [ [ 'attr_value_id', 'in', $attr_value_id_arr ] ]);
			return $res;
		}
	}
	
}