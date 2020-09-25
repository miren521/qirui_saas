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

namespace addon\fenxiao\model;

use app\model\BaseModel;


/**
 * 分销
 */
class FenxiaoGoodsSku extends BaseModel
{
	/**
	 * 添加分销商品
	 * @param $data
	 * @return array
	 */
	public function addSku($data)
	{
		$res = model('fenxiao_goods_sku')->add($data);
		return $this->success($res);
	}
	
	
	/**
	 * 编辑分销商品
	 * @param $data
	 * @param array $condition
	 * @return array
	 */
	public function editSku($data, $condition = [])
	{
		$data['update_time'] = time();
		$res = model('fenxiao_goods_sku')->update($data, $condition);
		
		return $this->success($res);
	}
	
	
	/**
	 * 删除分销商品
	 * @param array $condition
	 * @return array
	 */
	public function deleteSku($condition = [])
	{
		$res = model('fenxiao_goods_sku')->delete($condition);
		return $this->success($res);
	}
	
	/**
	 * 获取分销商品详情
	 * @param array $condition
	 * @param string $field
	 */
	public function getFenxiaoGoodsSkuDetail($condition = [], $field = 'fgs.goods_sku_id,fgs.goods_id,fgs.sku_id,fgs.level_id,fgs.one_rate,fgs.one_money,fgs.two_rate,fgs.two_money,fgs.three_rate,fgs.three_money,gs.discount_price')
	{
		$alias = 'fgs';
		$join = [
			[ 'goods_sku gs', 'fgs.sku_id = gs.sku_id', 'inner' ],
		];
		$list = model('fenxiao_goods_sku')->getInfo($condition, $field, $alias, $join);
		return $this->success($list);
	}
	
	
	/**
	 * 获取分销sku列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getSkuList($condition = [], $field = '*', $order = '', $limit = null)
	{
		$list = model('fenxiao_goods_sku')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取分销商品分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @param string $order
	 * @return array
	 */
	public function getFenxiaoGoodsSkuPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'fgs.goods_sku_id desc', $field = 'fgs.goods_sku_id,fgs.goods_id,fgs.sku_id,fgs.level_id,fgs.one_rate,fgs.one_money,fgs.two_rate,fgs.two_money,fgs.three_rate,fgs.three_money,gs.sku_name,gs.discount_price,gs.stock,gs.sale_num,gs.sku_image,gs.site_id', $alias = '', $join = '')
	{

		$list = model('fenxiao_goods_sku')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
	
	/**
	 * 批量添加分销商品
	 * @param $data
	 */
	public function addSkuList($data)
	{
		$re = model('fenxiao_goods_sku')->addList($data);
		return $this->success($re);
	}
}