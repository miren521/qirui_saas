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
use app\model\system\Cron;


class StoreGoodsSku extends BaseModel
{
	/**
	 * 添加店铺sku
	 * @param unknown $pintuan_data
	 * @param unknown $sku_list
	 */
	public function addStoreGoodsSku($data)
	{
		$res = model('store_goods_sku')->add($data);
		//判断当前有没有store_goods
		$store_goods_info = model('store_goods')->getInfo([ [ 'goods_id', '=', $data['goods_id'] ], [ 'store_id', '=', $data['store_id'] ] ], 'id');
		if (empty($store_goods_info)) {
			$store_goods_data = [
				'goods_id' => $data['goods_id'],
				'store_id' => $data['store_id'],
				'create_time' => time(),
			];
			model('store_goods')->add($store_goods_data);
		}
		
		return $this->success($res);
	}
	
	
	/**
	 * 获取门店商品sku详情
	 * @param $condition
	 * @param string $field
	 * @return array
	 */
	public function getStoreGoodsSkuInfo($condition, $field = '*')
	{
		$res = model('store_goods_sku')->getInfo($condition, $field);
		return $this->success($res);
	}
	
	/**
	 * 增加库存
	 * @param param
	 */
	public function incStock($param)
	{
		
		$condition = array(
			[ 'store_id', '=', $param['store_id'] ],
			[ 'sku_id', '=', $param['sku_id'] ]
		);
		$num = $param['store_stock'];
		$store_sku_info = model("store_goods_sku")->getInfo($condition, 'id, goods_id');
		if (empty($store_sku_info))
			return $this->error(-1, "");
		
		//编辑sku库存
		$result = model("store_goods_sku")->setInc($condition, 'store_stock', $num);
		
		model('store_goods')->setInc([ [ 'goods_id', '=', $store_sku_info['goods_id'] ], [ 'store_id', '=', $param['store_id'] ] ], 'store_goods_stock', $num);
		
		return $this->success($result);
	}
	
	/**
	 * 减少库存
	 * @param param
	 */
	public function decStock($param)
	{
		$condition = array(
			[ 'store_id', '=', $param['store_id'] ],
			[ 'sku_id', '=', $param['sku_id'] ]
		);
		$num = $param['store_stock'];
		$store_sku_info = model("store_goods_sku")->getInfo($condition, 'id, goods_id, store_stock');
		if (empty($store_sku_info)) {
			return $this->error(-1, '库存不足！');
		}
		if (($store_sku_info['store_stock'] - $num) < 0) {
			return $this->error(-1, '库存不足！');
		}
		//编辑sku库存
		$result = model("store_goods_sku")->setDec($condition, 'store_stock', $num);
		model('store_goods')->setDec([ [ 'goods_id', '=', $store_sku_info['goods_id'] ], [ 'store_id', '=', $param['store_id'] ] ], 'store_goods_stock', $num);
		return $this->success($result);
	}
	
	/**
	 * 编辑门店商品库存信息
	 * @param $goods_sku_array
	 */
	public function editStock($store_goods_sku_array)
	{
		model('store_goods_sku')->startTrans();
		try {
			foreach ($store_goods_sku_array as $item) {
				$sku_info_result = $this->getStoreGoodsSkuInfo([ [ "store_id", "=", $item["store_id"] ], [ "sku_id", "=", $item["sku_id"] ] ], "sku_id");
				$sku_info = $sku_info_result["data"];
				if (empty($sku_info)) {
					$sku_data = array(
						"goods_id" => $item["goods_id"],
						"sku_id" => $item["sku_id"],
						"store_id" => $item["store_id"],
					);
					$this->addStoreGoodsSku($sku_data);
				}
				
				if ($item["store_stock"] > 0) {
					$item_result = $this->incStock($item);
				} else {
					$item["store_stock"] = abs($item["store_stock"]);
					$item_result = $this->decStock($item);
				}
				if ($item_result["code"] < 0) {
					model('store_goods_sku')->rollback();
					return $item_result;
				}
			}
			model('store_goods_sku')->commit();
			return $this->success();
		} catch (\Exception $e) {
			model('store_goods_sku')->rollback();
			return $this->error($e->getMessage());
		}
	}
	
	/**
	 * 获取商品列表
	 * @param array $condition
	 */
	public function getGoodsSkuList($condition = [], $store_id = 0)
	{
		$alias = 'gs';
		$join = [
			[
				'store_goods_sku sgs',
				'sgs.sku_id = gs.sku_id and (sgs.store_id is null or sgs.store_id = ' . $store_id . ')',
				'left'
			]
		];
		$field = 'gs.*,sgs.store_stock,sgs.store_sale_num';
		$list = model('goods_sku')->getList($condition, $field, '', $alias, $join);
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
	public function getGoodsSkuPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'gs.create_time desc', $field = '*')
	{
		
		$alias = 'gs';
		$join = [
			[
				'store_goods_sku sgs',
				'sgs.sku_id = gs.sku_id',
				'inner'
			]
		];
		$field = 'gs.*,sgs.store_stock,sgs.store_sale_num';
		
		$list = model('goods_sku')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
}