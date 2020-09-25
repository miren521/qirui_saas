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


namespace addon\topic\api\controller;

use app\api\controller\BaseApi;
use addon\topic\model\Topic as TopicModel;
use addon\topic\model\TopicGoods as TopicGoodsModel;
use app\model\shop\Shop as ShopModel;
use app\model\goods\Goods as GoodsModel;

/**
 * 专题活动商品
 */
class Topicgoods extends BaseApi
{
	
	/**
	 * 详情信息
	 */
	public function detail()
	{
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		$topic_goods_model = new TopicGoodsModel();
		$goods_sku_detail = $topic_goods_model->getTopicGoodsDetail($id);
		$goods_sku_detail = $goods_sku_detail['data'];
		$res['goods_sku_detail'] = $goods_sku_detail;

        $goods_model = new GoodsModel();
        $goods_info = $goods_model->getGoodsInfo([['goods_id', '=', $goods_sku_detail['goods_id']]])['data'] ?? [];
        if (empty($goods_info)) return $this->response($this->error([], '找不到商品'));
        $res[ 'goods_info' ] = $goods_info;

//		店铺信息
		$shop_model = new ShopModel();
		$shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $goods_sku_detail['site_id'] ] ], 'site_id,site_name,is_own,logo,avatar,banner,seo_description,qq,ww,telephone,shop_desccredit,shop_servicecredit,shop_deliverycredit,shop_baozh,shop_baozhopen,shop_baozhrmb,shop_qtian,shop_zhping,shop_erxiaoshi,shop_tuihuo,shop_shiyong,shop_shiti,shop_xiaoxie,shop_sales,sub_num');
		
		$shop_info = $shop_info['data'];
		$res['shop_info'] = $shop_info;
		
		return $this->response($this->success($res));
		
	}
	
	/**
	 * 列表信息
	 */
	public function page()
	{
		$topic_id = isset($this->params['topic_id']) ? $this->params['topic_id'] : 0;
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		
		if (empty($topic_id)) {
			return $this->response($this->error('', 'REQUEST_TOPIC_ID'));
		}
		$condition = [
			[ 'nptg.topic_id', '=', $topic_id ],
			[ 'ngs.goods_state', '=', 1 ],
			[ 'ngs.verify_state', '=', 1 ],
			[ 'ngs.is_delete', '=', 0 ]
		];
		$order = '';
		$field = 'nptg.id,nptg.topic_id,nptg.start_time,nptg.end_time,nptg.site_id,nptg.topic_price,ngs.sku_id,ngs.sku_name,ngs.price,ngs.discount_price,ngs.stock,ngs.sku_image,ngs.goods_name,ngs.is_own';
		$topic_goods_model = new TopicGoodsModel();
		
		$topic_model = new TopicModel();
		$info = $topic_model->getTopicInfo([ [ "topic_id", "=", $topic_id ] ], 'bg_color,topic_adv,topic_name');
		$info = $info['data'];


        $alias = 'nptg';
        $join = [
            [ 'goods_sku ngs', 'nptg.sku_id = ngs.sku_id', 'inner' ],
            [ 'promotion_topic npt', 'nptg.topic_id = npt.topic_id', 'inner' ],
        ];
        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = ngs.site_id', 'inner'];
        $condition[] = ['s.shop_status', '=', 1];

		$res = $topic_goods_model->getTopicGoodsPageList($condition, $page, $page_size, $order, $field, $alias, $join);
		$res['data']['bg_color'] = $info['bg_color'];
		$res['data']['topic_adv'] = $info['topic_adv'];
		$res['data']['topic_name'] = $info['topic_name'];
		
		return $this->response($res);
	}
	
}