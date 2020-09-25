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


namespace addon\seckill\api\controller;

use app\api\controller\BaseApi;
use addon\seckill\model\Seckill as SeckillModel;
use app\model\shop\Shop as ShopModel;
use addon\seckill\model\Poster;
use app\model\goods\Goods as GoodsModel;

/**
 * 秒杀商品
 */
class Seckillgoods extends BaseApi
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
		
		$seckill_model = new SeckillModel();
		$goods_sku_detail = $seckill_model->getSeckillGoodsDetail($id);
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
	
	public function page()
	{
		$seckill_id = isset($this->params['seckill_id']) ? $this->params['seckill_id'] : 0;
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		
		if (empty($seckill_id)) {
			return $this->response($this->error('', 'REQUEST_SECKILL_ID'));
		}
		
		$condition = [
			[ 'npsg.seckill_id', '=', $seckill_id ],
			[ 'ngs.stock', '>', 0 ],
			[ 'ngs.goods_state', '=', 1 ],
			[ 'ngs.verify_state', '=', 1 ],
			[ 'ngs.is_delete', '=', 0 ],
		];
		
		if (!empty($site_id)) {
			$condition[] = [ 'ngs.site_id', '=', $site_id ];
		}

        $join = [ 'shop s', 's.site_id = npsg.site_id', 'inner'];
        $condition[] = ['s.shop_status', '=', 1];
		$seckill_model = new SeckillModel();
		$res = $seckill_model->getSeckillGoodsPageList($condition, $page, $page_size, '', '*', $join);
		$list = $res['data']['list'];
		foreach ($list as $key => $val) {
			if ($val['price'] != 0) {
				$discount_rate = floor($val['seckill_price'] / $val['price'] * 100);
			} else {
				$discount_rate = 100;
			}
			$list[ $key ]['discount_rate'] = $discount_rate;
		}
//		$res = [
//			'list' => $list
//		];
		return $this->response($res);
	}
	
	/**
	 * 获取商品海报
	 */
	public function poster()
	{
		if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));
		
		$promotion_type = 'seckill';
		$qrcode_param = json_decode($this->params['qrcode_param'], true);
		$qrcode_param['source_member'] = $qrcode_param['source_member'] ?? 0;
		$poster = new Poster();
		$res = $poster->goods($this->params['app_type'], $this->params['page'], $qrcode_param, $promotion_type);
		return $this->response($res);
	}
	
}