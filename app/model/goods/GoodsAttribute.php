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
 * 商品类型、属性
 */
class GoodsAttribute extends BaseModel
{

	/************************************************************商品类型*********************************************/

	/**
	 * 添加商品类型
	 * @param $data
	 * @return \multitype
	 */
	public function addAttrClass($data)
	{
		$class_id = model("goods_attr_class")->add($data);
		Cache::tag("goods_attr_class")->clear();
		return $this->success($class_id);
	}

	/**
	 * 编辑商品类型
	 * @param $data
	 * @return \multitype
	 */
	public function editAttrClass($data)
	{
		$res = model("goods_attr_class")->update($data, [['class_id', '=', $data['class_id']]]);
		if (!empty($data['class_name'])) {
			//修改属性表
			model("goods_attribute")->update(['attr_class_name' => $data['class_name']], [['attr_class_id', '=', $data['class_id']]]);
		}
		//预留修改商品
		Cache::tag("goods_attr_class")->clear();
		return $this->success($res);
	}

	/**
	 * 删除商品类型
	 * @param $class_id
	 * @return \multitype
	 */
	public function deleteAttrClass($class_id)
	{
		$res = model('goods_attr_class')->delete([['class_id', '=', $class_id]]);
		if ($res) {
			// 删除商品类型品牌关联
			model('goods_attr_class_brand')->delete([['attr_class_id', '=', $class_id]]);
			Cache::tag("goods_attr_class_brand_" . $class_id)->clear();

			// 删除商品属性
			model('goods_attribute')->delete([['attr_class_id', '=', $class_id]]);
			Cache::tag("goods_attribute_" . $class_id)->clear();

			// 删除商品属性值
			model('goods_attribute_value')->delete([['attr_class_id', '=', $class_id]]);
			Cache::tag("goods_attribute_value_" . $class_id)->clear();
		}
		Cache::tag("goods_attr_class")->clear();
		return $this->success($res);
	}

	/**
	 * 修改排序
	 * @param int $sort
	 * @param int $class_id
	 */
	public function modifyAttrClassSort($sort, $class_id)
	{
		$res = model('goods_attr_class')->update(['sort' => $sort], [['class_id', '=', $class_id]]);
		Cache::tag("goods_attr_class")->clear();
		return $this->success($res);
	}

	/**
	 * 获取商品类型信息
	 * @param array $condition
	 * @param string $field
	 */
	public function getAttrClassInfo($condition, $field = 'class_id,class_name,sort')
	{
		$data = json_encode([$condition, $field]);
		$cache = Cache::get("goods_attr_class_getAttrClassInfo_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('goods_attr_class')->getInfo($condition, $field);
		Cache::tag("goods_attr_class")->set("goods_attr_class_getAttrClassInfo_" . $data, $res);
		return $this->success($res);
	}

	/**
	 * 获取商品类型列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return \multitype
	 */
	public function getAttrClassList($condition = [], $field = 'class_id,class_name,sort', $order = 'class_id desc', $limit = null)
	{
		$data = json_encode([$condition, $field, $order, $limit]);
		$cache = Cache::get("goods_attr_class_getAttrClassList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('goods_attr_class')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("goods_attr_class")->set("goods_attr_class_getAttrClassList_" . $data, $list);

		return $this->success($list);
	}

	/**
	 * 获取商品类型分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return \multitype
	 */
	public function getAttrClassPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'class_id desc', $field = 'class_id,class_name,sort')
	{
		$data = json_encode([$condition, $field, $order, $page, $page_size]);
		$cache = Cache::get("goods_attr_class_getAttrClassPageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('goods_attr_class')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("goods_attr_class")->set("goods_attr_class_getAttrClassPageList_" . $data, $list);
		return $this->success($list);
	}

	/************************************************************商品类型品牌关联*********************************************/

	/**
	 * 添加商品类型品牌关联
	 * @param $attr_class_id
	 * @param $data
	 * @return \multitype
	 */
	public function addAttrClassBrand($attr_class_id, $data)
	{
		$class_id = model("goods_attr_class_brand")->addList($data);
		Cache::tag("goods_attr_class_brand_" . $attr_class_id)->clear();
		return $this->success($class_id);
	}

	/**
	 * 删除商品类型品牌关联
	 * @param $attr_class_id
	 * @param $id
	 * @return \multitype
	 */
	public function deleteAttrClassBrand($attr_class_id, $id)
	{
		$res = model('goods_attr_class_brand')->delete([['id', 'in', $id], ['attr_class_id', '=', $attr_class_id]]);
		Cache::tag("goods_attr_class_brand_" . $attr_class_id)->clear();
		return $this->success($res);
	}

	/**
	 * 获取商品类型品牌关联列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return \multitype
	 */
	public function getAttrClassBrandList($condition = [], $field = 'ngacb.id,ngacb.attr_class_id,nsgb.brand_id,nsgb.brand_name,nsgb.image_url,nsgb.brand_initial', $order = 'ngacb.id desc', $limit = null)
	{
		$check_condition = array_column($condition, 2, 0);
		$attr_class_id = isset($check_condition['ngacb.attr_class_id']) ? $check_condition['ngacb.attr_class_id'] : '';
		if ($attr_class_id === '') {
			//			return $this->error('', 'REQUEST_GOODS_ATTRIBUTE_ID');
		}
		$data = json_encode([$condition, $field, $order, $limit]);
		$cache = Cache::get("goods_attr_class_brand_getAttrClassBrandList_" . $attr_class_id . '_' . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}

		$join = [
			[
				'goods_brand nsgb',
				'ngacb.brand_id = nsgb.brand_id',
				'left'
			]
		];

		$list = model('goods_attr_class_brand')->getList($condition, $field, $order, 'ngacb', $join, '', $limit);
		Cache::tag("goods_attr_class_brand_" . $attr_class_id)->set("goods_attr_class_brand_getAttrClassBrandList_" . $attr_class_id . '_' . $data, $list);

		return $this->success($list);
	}

	/************************************************************商品属性*********************************************/

	/**
	 * 添加商品属性
	 * @param $attr_class_id
	 * @param $data
	 * @return \multitype
	 */
	public function addAttribute($attr_class_id, $data)
	{
		$attr_id = model("goods_attribute")->add($data);
		Cache::tag("goods_attribute_" . $attr_class_id)->clear();
		return $this->success($attr_id);
	}

	/**
	 * 编辑商品属性
	 * @param $attr_class_id
	 * @param $data
	 * @return \multitype
	 */
	public function editAttribute($attr_class_id, $data)
	{
		$res = model("goods_attribute")->update($data, [['attr_id', '=', $data['attr_id']], ['attr_class_id', '=', $attr_class_id]]);
		Cache::tag("goods_attribute_" . $attr_class_id)->clear();
		return $this->success($res);
	}

	/**
	 * 删除属性
	 * @param $attr_class_id
	 * @param $attr_id
	 * @return \multitype
	 */
	public function deleteAttribute($attr_class_id, $attr_id)
	{
		$res = model('goods_attribute')->delete([['attr_id', '=', $attr_id], ['attr_class_id', '=', $attr_class_id]]);
		if ($res) {
			$res = model('goods_attribute_value')->delete([['attr_id', '=', $attr_id], ['attr_class_id', '=', $attr_class_id]]);
			Cache::tag("goods_attribute_value_" . $attr_class_id)->clear();
		}
		Cache::tag("goods_attribute_" . $attr_class_id)->clear();
		return $this->success($res);
	}

	/**
	 * 修改站点
	 * @param int $sort
	 * @param int $class_id
	 */
	public function modifyAttributeSite($site_id, $attr_class_id, $attr_id)
	{
		$res = model('goods_attribute')->update(['site_id' => $site_id], [['attr_id', '=', $attr_id]]);
		Cache::tag("goods_attribute_" . $attr_class_id)->clear();
		return $this->success($res);
	}

	/**
	 * 获取属性信息
	 * @param $condition
	 * @param string $field
	 * @return \multitype
	 */
	public function getAttributeInfo($condition, $field = 'attr_id,attr_name,attr_class_id,sort,is_query,is_spec,attr_value_list,attr_type')
	{
		$check_condition = array_column($condition, 2, 0);
		$attr_class_id = isset($check_condition['attr_class_id']) ? $check_condition['attr_class_id'] : '';
		if ($attr_class_id === '') {
			return $this->error('', 'REQUEST_GOODS_ATTRIBUTE_ID');
		}
		$data = json_encode([$condition, $field]);
		$cache = Cache::get("goods_attribute_getAttributeInfo_" . $attr_class_id . '_' . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$res = model('goods_attribute')->getInfo($condition, $field);
		Cache::tag("goods_attribute_" . $attr_class_id)->set("goods_attribute_getAttributeInfo_" . $attr_class_id . '_' . $data, $res);
		return $this->success($res);
	}


    /**
     * 获取属性信息
     * @param $condition
     * @param string $field
     * @return \multitype
     */
    public function getGoodsAttributeInfo($condition, $field = 'attr_id,attr_name,attr_class_id,sort,is_query,is_spec,attr_value_list,attr_type')
    {
        $data = json_encode([$condition, $field]);
        $cache = Cache::get("goods_attribute_getGoodsAttributeInfo_"  . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $res = model('goods_attribute')->getInfo($condition, $field);
        Cache::tag("goods_attribute" )->set("goods_attribute_getGoodsAttributeInfo_" .  $data, $res);
        return $this->success($res);
    }

	/**
	 * 获取商品属性列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return \multitype
	 */
	public function getAttributeList($condition = [], $field = 'attr_id,attr_name,attr_class_id,sort,is_query,is_spec,attr_value_list,attr_value_list,attr_type,site_id,site_name', $order = 'attr_id desc', $limit = null)
	{
		$check_condition = array_column($condition, 2, 0);
		$attr_class_id = isset($check_condition['attr_class_id']) ? $check_condition['attr_class_id'] : '';
		if ($attr_class_id === '') {
			return $this->error('', 'REQUEST_GOODS_ATTRIBUTE_ID');
		}
		$data = json_encode([$condition, $field, $order, $limit]);
		$cache = Cache::get("goods_attribute_getAttributeList_" . $attr_class_id . '_' . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}

		$list = model('goods_attribute')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("goods_attribute_" . $attr_class_id)->set("goods_attribute_getAttributeList_" . $attr_class_id . '_' . $data, $list);

		return $this->success($list);
	}

	/**
	 * 获取商品规格列表，暂时不加缓存
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return \multitype
	 */
	public function getSpecList($condition = [], $field = 'attr_id,attr_name,attr_class_id,sort,is_query,is_spec,attr_value_list,attr_value_list,attr_type,site_id', $order = 'attr_id desc', $limit = null)
	{
		$list = model('goods_attribute')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}

	/************************************************************商品属性关联*********************************************/

	/**
	 * 获取商品属性关联列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return array|\multitype
	 */
	public function getAttributeIndexList($condition = [], $field = 'id,goods_id,sku_id,attr_id,attr_value_id,attr_class_id', $order = '', $limit = null)
	{
		$list = model('goods_attr_index')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}

	/************************************************************商品属性值*********************************************/

	/**
	 * 添加属性值
	 * @param $attr_class_id
	 * @param $data
	 * @return \multitype
	 */
	public function addAttributeValue($attr_class_id, $data)
	{
		$attr_value_id = model("goods_attribute_value")->addList($data);
		if ($attr_value_id) {
			//			刷新属性值JSON格式
			$this->refreshAttrValueFormat($attr_class_id, $data[0]['attr_id']);
			Cache::tag("goods_attribute_value_" . $attr_class_id)->clear();
			return $this->success($attr_value_id);
		} else {
			return $this->error();
		}
	}

	/**
	 * 编辑商品属性值
	 * @param $attr_class_id
	 * @param $data
	 * @return \multitype
	 */
	public function editAttributeValue($attr_class_id, $data)
	{
		$res = model("goods_attribute_value")->update($data, [['attr_value_id', '=', $data['attr_value_id']]]);
		if ($res) {
			//			刷新属性值JSON格式
			$this->refreshAttrValueFormat($attr_class_id, $data['attr_id']);
			Cache::tag("goods_attribute_value_" . $attr_class_id)->clear();
			return $this->success($res);
		} else {
			return $this->error();
		}
	}

	/**
	 * 刷新属性值JSON格式
	 * @param $attr_class_id
	 * @param $attr_id
	 */
	private function refreshAttrValueFormat($attr_class_id, $attr_id)
	{
		$list = model('goods_attribute_value')->getList([['attr_id', '=', $attr_id]], 'attr_value_id,attr_value_name');
		if (!empty($list)) {
			$attr_value_format = [];
			foreach ($list as $k => $v) {
				$item = [
					'attr_value_id' => $v['attr_value_id'],
					'attr_value_name' => $v['attr_value_name']
				];
				$attr_value_format[] = $item;
			}
			$res = model("goods_attribute")->update(['attr_value_format' => json_encode($attr_value_format)], [['attr_id', '=', $attr_id], ['attr_class_id', '=', $attr_class_id]]);
			Cache::tag("goods_attribute_" . $attr_class_id)->clear();
			return $this->success($res);
		}
	}

	/**
	 * 删除属性值
	 * @param $attr_class_id
	 * @param $condition
	 * @return \multitype
	 */
	public function deleteAttributeValue($attr_class_id, $condition)
	{
		$res = model('goods_attribute_value')->delete($condition);
		Cache::tag("goods_attribute_value_" . $attr_class_id)->clear();
		return $this->success($res);
	}

	/**
	 * 获取商品属性值列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return \multitype
	 */
	public function getAttributeValueList($condition = [], $field = 'attr_value_id,attr_value_name,attr_id,attr_class_id,sort', $order = '', $limit = null)
	{

		$check_condition = array_column($condition, 2, 0);
		$attr_class_id = isset($check_condition['attr_class_id']) ? $check_condition['attr_class_id'] : '';
		if ($attr_class_id === '') {
			return $this->error('', 'REQUEST_GOODS_ATTRIBUTE_ID');
		}
		$data = json_encode([$condition, $field, $order, $limit]);
		$cache = Cache::get("goods_attribute_value_getAttributeValueList_" . $attr_class_id . '_' . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}

		$list = model('goods_attribute_value')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("goods_attribute_value_" . $attr_class_id)->set("goods_attribute_value_getAttributeValueList_" . $attr_class_id . '_' . $data, $list);

		return $this->success($list);
	}

	/**
	 * 获取商品规格值列表，暂时不加缓存
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param null $limit
	 * @return \multitype
	 */
	public function getSpecValueList($condition = [], $field = 'attr_value_id,attr_value_name,attr_id,attr_class_id,sort', $order = 'attr_value_id desc', $limit = null)
	{
        $data = json_encode([$condition, $field, $order, $limit]);
        $cache = Cache::get("goods_attribute_value_getSpecValueList_"  . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }

        $list = model('goods_attribute_value')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("goods_attribute_value")->set("goods_attribute_value_getSpecValueList_"  . $data, $list);

		return $this->success($list);
	}
}
