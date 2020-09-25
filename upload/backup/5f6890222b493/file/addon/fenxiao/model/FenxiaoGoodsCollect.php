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
 * 分销商关注商品
 */
class FenxiaoGoodsCollect extends BaseModel
{
	/**
	 * 添加分销商关注商品
	 * @param $data
	 * @return array
	 */
	public function addCollect($data)
	{
		$res = model('fenxiao_goods_collect')->getCount([ [ 'member_id', '=', $data['member_id'] ], [ 'sku_id', '=', $data['sku_id'] ] ]);
		if (empty($res)) {
			$data['create_time'] = time();
			$collect_id = model('fenxiao_goods_collect')->add($data);
			return $this->success($collect_id);
		} else {
			return $this->error('', 'GOODS_COLLECT_IS_EXIST');
		}
	}
	
	
	/**
	 * 删除分销商关注商品
	 * @param array $condition
	 * @return array
	 */
	public function deleteCollect($condition = [])
	{
		$res = model('fenxiao_goods_collect')->delete($condition);
		return $this->success($res);
	}
	
	/**
	 * 获取分销商关注商品数信息
	 * @param $condition
	 * @param $field
	 * @return array
	 */
	public function getCollectInfo($condition, $field = '*')
	{
		$res = model('fenxiao_goods_collect')->getInfo($condition, $field);
		return $this->success($res);
	}
	
	/**
	 * 获取分销商关注商品分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @param string $order
	 * @return array
	 */
	public function getCollectPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'fgc.create_time desc', $field = 'fgc.collect_id,fgc.member_id,fgc.fenxiao_id,fgc.create_time,fgs.goods_sku_id,fgs.goods_id,fgs.sku_id,fgs.level_id,fgs.one_rate,fgs.one_money,fgs.two_rate,fgs.two_money,fgs.three_rate,fgs.three_money,gs.sku_name,gs.discount_price,gs.stock,gs.sale_num,gs.sku_image,gs.site_id')
	{
		$alias = 'fgc';
		$join = [
			[ 'goods_sku gs', 'fgc.sku_id = gs.sku_id', 'inner' ],
			[ 'fenxiao_goods_sku fgs', 'fgc.sku_id = fgs.sku_id', 'inner' ],
			[ 'goods g', 'fgc.goods_id = g.goods_id', 'inner' ]
		];

        //todo  goods 收藏类的商品还是应该能看到,在商品详情内部提示就可以了
		$list = model('fenxiao_goods_collect')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
}