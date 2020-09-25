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

namespace addon\topic\model;

use app\model\BaseModel;

/**
 * 专题活动
 */
class TopicGoods extends BaseModel
{
	
	/**
	 * 添加专题商品
	 * @param unknown $topic_id
	 * @param unknown $site_id
	 * @param unknown $sku_ids
	 * @return multitype:string
	 */
	public function addTopicGoods($topic_id, $site_id, $sku_ids)
	{
		$sku_list = model("goods_sku")->getList([
			[ 'sku_id', 'in', $sku_ids ],
			[ 'site_id', '=', $site_id ],
		], 'goods_id, sku_id, price');
		$topic_info = model("promotion_topic")->getInfo([ [ 'topic_id', '=', $topic_id ] ], 'start_time, end_time');
		$data = [];
		foreach ($sku_list as $val) {
			$goods_count = model("promotion_topic_goods")->getCount([ 'topic_id' => $topic_id, 'sku_id' => $val['sku_id'] ]);
			if (empty($goods_count)) {
				$data[] = [
					'topic_id' => $topic_id,
					'site_id' => $site_id,
					'sku_id' => $val['sku_id'],
					'topic_price' => $val['price'],
					'start_time' => $topic_info['start_time'],
					'end_time' => $topic_info['end_time']
				];
			}
		}
		model("promotion_topic_goods")->addList($data);
		
		return $this->success();
	}
	
	/**
	 * 修改专题商品
	 * @param unknown $topic_id
	 * @param unknown $site_id
	 * @param unknown $sku_id
	 * @param unknown $price
	 * @return multitype:string
	 */
	public function editTopicGoods($topic_id, $site_id, $sku_id, $price)
	{
		$data = [
			'topic_id' => $topic_id,
			'site_id' => $site_id,
			'sku_id' => $sku_id,
			'topic_price' => $price
		];
		model("promotion_topic_goods")->update($data, [ [ 'topic_id', '=', $topic_id ], [ 'sku_id', '=', $sku_id ], [ 'site_id', '=', $site_id ] ]);
		return $this->success();
	}
	
	/**
	 * 删除专题商品
	 * @param unknown $topic_id
	 * @param unknown $site_id
	 * @param unknown $sku_id
	 * @return multitype:string
	 */
	public function deleteTopicGoods($topic_id, $site_id, $sku_id)
	{
		model("promotion_topic_goods")->delete([ [ 'topic_id', '=', $topic_id ], [ 'sku_id', '=', $sku_id ], [ 'site_id', '=', $site_id ] ]);
		return $this->success();
	}
	
	/**
	 * 获取专题商品详情
	 * @param int $id
	 * @return mixed
	 */
	public function getTopicGoodsDetail($id)
	{
		$condition = [
			[ 'ptg.id', '=', $id ],
			[ 'pt.status', '=', 2 ]
		];
		$alias = 'ptg';
		$join = [
			[ 'goods_sku sku', 'ptg.sku_id = sku.sku_id', 'inner' ],
			[ 'promotion_topic pt', 'pt.topic_id = ptg.topic_id', 'inner' ],
		];
		
		$info = model('promotion_topic_goods')->getInfo($condition, 'sku.goods_id,sku.sku_id,sku.sku_name,sku.sku_spec_format,sku.price,sku.promotion_type,sku.stock,sku.click_num,sku.sale_num,sku.collect_num,sku.sku_image,sku.sku_images,sku.site_id,sku.goods_content,sku.goods_state,sku.verify_state,sku.is_virtual,sku.is_free_shipping,sku.goods_spec_format,sku.goods_attr_format,sku.introduction,sku.unit,sku.video_url,sku.evaluate,sku.category_id,sku.category_id_1,sku.category_id_2,sku.category_id_3,sku.category_name,ptg.id,ptg.topic_id,ptg.start_time,ptg.end_time,ptg.topic_price,pt.topic_name', $alias, $join);
		return $this->success($info);
	}
	
	/**
	 * 获取专题商品列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return mixed
	 */
	public function getTopicGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '', $alias = '', $join = '')
	{
		if (empty($field)) {
			$field = 'ngs.sku_id, ngs.sku_name, ngs.sku_no, ngs.sku_spec_format, ngs.price, ngs.market_price,
	        ngs.cost_price, ngs.discount_price, ngs.promotion_type, ngs.stock,
	        ngs.weight, ngs.volume, ngs.click_num, ngs.sale_num, ngs.collect_num, ngs.sku_image,
	        ngs.sku_images, ngs.goods_id, ngs.goods_class, ngs.goods_class_name, ngs.goods_attr_class,
	        ngs.goods_attr_name, ngs.goods_name, ngs.site_id, ngs.site_name, ngs.website_id, ngs.category_id,
	        ngs.category_id_1, ngs.category_id_2, ngs.category_id_3, ngs.category_name, ngs.brand_id, ngs.brand_name,
	        ngs.goods_content, ngs.is_own, ngs.goods_state, ngs.verify_state, ngs.verify_state_remark, ngs.goods_stock_alarm,
	        ngs.is_virtual, ngs.virtual_indate, ngs.is_free_shipping, ngs.shipping_template, ngs.goods_spec_format,
	        ngs.goods_attr_format, ngs.is_delete, ngs.introduction, ngs.keywords, ngs.unit, ngs.sort,npt.topic_name,
	        npt.topic_adv, npt.status, nptg.start_time, nptg.end_time, nptg.topic_price, npt.topic_id';
		}

		$list = model('promotion_topic_goods')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
}