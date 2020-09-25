<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\store\model;

use app\model\BaseModel;


class StoreGoods extends BaseModel
{
	
	
	/**
	 * 门店详情
	 * @param $condition
	 * @param string $fields
	 * @return array
	 */
	public function getStoreGoodsInfo($condition, $fields = '*')
	{
		$res = model('store_goods')->getInfo($condition, $fields);
		return $this->success($res);
	}
	
	/**
	 * 获取门店商品列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getStoreGoodsList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('store_goods')->getList($condition, $field, $order, '', '', '', $limit);
		foreach ($list as &$v) {
			$v['store_goods_skus'] = model('store_goods_sku')->getList([
				[ 'store_id', '=', $v['store_id'] ],
				[ 'goods_id', '=', $v['goods_id'] ]
			], 'sku_id,goods_id,store_stock,store_sale_num');
		}
		return $this->success($list);
	}
	
	
	/**
	 * 获取商品分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $store_id, $order = 'g.create_time desc', $field = '*')
	{
		
		$alias = 'g';
		$join = [
			[
				'store_goods sg',
				'sg.goods_id = g.goods_id and (sg.store_id is null or sg.store_id = ' . $store_id . ')',
				'left'
			]
		];
		$field = 'g.*,sg.store_goods_stock,sg.store_sale_num';
		
		$list = model('goods')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
}