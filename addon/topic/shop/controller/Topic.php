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

namespace addon\topic\shop\controller;

use app\shop\controller\BaseShop;
use addon\topic\model\Topic as TopicModel;
use addon\topic\model\TopicGoods as TopicGoodsModel;

/**
 * 专题活动
 * @author Administrator
 *
 */
class Topic extends BaseShop
{
	/**
	 * 专题活动列表
	 */
	public function lists()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$topic_name = input('topic_name', '');
			
			$condition = [];
			$condition[] = [ 'topic_name', 'like', '%' . $topic_name . '%' ];
			$order = 'modify_time desc,create_time desc';
			$field = '*';
			
			$topic_model = new TopicModel();
			$res = $topic_model->getTopicPageList($condition, $page, $page_size, $order, $field);
			return $res;
		} else {
			$this->forthMenu();
			return $this->fetch("topic/lists");
		}
	}
	
	/**
	 * 专题活动商品列表
	 */
	public function goods()
	{
		$topic_id = input("topic_id", 0);
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$sku_name = input('sku_name', '');
			
			$condition = [];
			$condition[] = [ 'nptg.topic_id', '=', $topic_id ];
			$condition[] = [ 'nptg.site_id', '=', $this->site_id ];
			$condition[] = [ 'ngs.sku_name', 'like', '%' . $sku_name . '%' ];
			$order = '';
			$field = '*';
			$topic_goods_model = new TopicGoodsModel();

            $alias = 'nptg';
            $join = [
                [ 'goods_sku ngs', 'nptg.sku_id = ngs.sku_id', 'inner' ],
                [ 'promotion_topic npt', 'nptg.topic_id = npt.topic_id', 'inner' ],
            ];

			$res = $topic_goods_model->getTopicGoodsPageList($condition, $page, $page_size, $order, $field, $alias, $join);
			return $res;
		} else {
			$topic_model = new TopicModel();
			
			$topic_info = $topic_model->getTopicInfo([ 'topic_id' => $topic_id ], '*');
			$this->assign("topic_id", $topic_id);
			$this->assign("topic_info", $topic_info['data']);
			return $this->fetch("topic/goods");
		}
	}
	
	/**
	 * 添加专题活动商品
	 */
	public function addTopicGoods()
	{
		if (request()->isAjax()) {
			$topic_id = input("topic_id", 0);
			$sku_ids = input("sku_ids", '');
			$topic_goods_model = new TopicGoodsModel();
			$res = $topic_goods_model->addTopicGoods($topic_id, $this->site_id, $sku_ids);
			return $res;
		}
	}
	
	/**
	 * 编辑专题活动商品
	 */
	public function editTopicGoods()
	{
		if (request()->isAjax()) {
			$topic_id = input("topic_id", 0);
			$sku_id = input("sku_id", 0);
			$price = input("price", 0);
			$topic_goods_model = new TopicGoodsModel();
			$res = $topic_goods_model->editTopicGoods($topic_id, $this->site_id, $sku_id, $price);
			return $res;
		}
	}
	
	/**
	 * 删除专题活动商品
	 */
	public function deleteTopicGoods()
	{
		if (request()->isAjax()) {
			$topic_id = input("topic_id", 0);
			$sku_id = input("sku_id", 0);
			$topic_goods_model = new TopicGoodsModel();
			$res = $topic_goods_model->deleteTopicGoods($topic_id, $this->site_id, $sku_id);
			return $res;
		}
	}
	
	/**
	 * 专题活动商品列表
	 */
	public function goodslist()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$sku_name = input('sku_name', '');
			
			$condition = [];
			$condition[] = [ 'nptg.site_id', '=', $this->site_id ];
			$condition[] = [ 'ngs.sku_name', 'like', '%' . $sku_name . '%' ];
			$order = '';
			$field = '*';
			$topic_goods_model = new TopicGoodsModel();

            $alias = 'nptg';
            $join = [
                [ 'goods_sku ngs', 'nptg.sku_id = ngs.sku_id', 'inner' ],
                [ 'promotion_topic npt', 'nptg.topic_id = npt.topic_id', 'inner' ],
            ];

			$res = $topic_goods_model->getTopicGoodsPageList($condition, $page, $page_size, $order, $field, $alias, $join);
			return $res;
		} else {
			$this->forthMenu();
			return $this->fetch("topic/goodslist");
		}
	}
}