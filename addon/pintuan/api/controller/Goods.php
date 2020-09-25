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


namespace addon\pintuan\api\controller;

use addon\pintuan\model\Pintuan as PintuanModel;
use app\api\controller\BaseApi;
use app\model\shop\Shop as ShopModel;
use addon\pintuan\model\Poster;
use app\model\goods\Goods as GoodsModel;

/**
 * 拼团商品
 */
class Goods extends BaseApi
{
	
	/**
	 * 基础信息
	 */
	public function info()
	{
		$sku_id = isset($this->params['sku_id']) ? $this->params['sku_id'] : 0;
		$pintuan_id = isset($this->params['pintuan_id']) ? $this->params['pintuan_id'] : 0;
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		if (empty($pintuan_id)) {
			return $this->response($this->error('', 'REQUEST_PINTUAN_ID'));
		}
		$goods = new PintuanModel();
		$condition = [
			[ 'sku.sku_id', '=', $sku_id ],
			[ 'ppg.pintuan_id', '=', $pintuan_id ],
			[ 'pp.status', '=', 1 ]
		];
		$info = $goods->getPintuanGoodsDetail($condition);
		return $this->response($info);
	}
	
	/**
	 * 拼团商品详情信息
	 */
	public function detail()
	{
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		
		$pintuan_model = new PintuanModel();
		$condition = [
			[ 'ppg.id', '=', $id ],
			[ 'pp.status', '=', 1 ]
		];
		$goods_sku_detail = $pintuan_model->getPintuanGoodsDetail($condition);
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
		
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		
		$condition = [
			[ 'pp.status', '=', 1 ],// 状态（0正常 1活动进行中  2活动已结束  3失效  4删除）
			[ 'sku.stock', '>', 0 ],
			[ 'sku.goods_state', '=', 1 ],
			[ 'sku.verify_state', '=', 1 ],
			[ 'sku.is_delete', '=', 0 ],
		];
		if (!empty($site_id)) {
			$condition[] = [ 'sku.site_id', '=', $site_id ];
		}

        $alias = 'pp';
        $join = [
            [ 'promotion_pintuan_goods ppg', 'pp.pintuan_id = ppg.pintuan_id', 'inner' ],
            [ 'goods g', 'ppg.sku_id = g.sku_id', 'inner' ],
            [ 'goods_sku sku', 'ppg.sku_id = sku.sku_id', 'inner' ],
        ];
        $field = 'pp.site_id,pp.pintuan_name,pp.is_virtual_goods,pp.pintuan_num,pp.pintuan_time,pp.is_recommend,pp.status,pp.group_num,pp.order_num,ppg.id,ppg.pintuan_id,ppg.pintuan_price,ppg.promotion_price,sku.sku_id,sku.sku_name,sku.price,sku.sku_image,g.goods_id,g.goods_name';
        $order = 'pp.is_recommend desc,pp.start_time desc';
        $pintuan_model = new PintuanModel();

        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = pp.site_id', 'inner'];
        $condition[] = ['s.shop_status', '=', 1];

		$list = $pintuan_model->getPintuanGoodsPageList($condition, $page, $page_size, $order, $field, $alias, $join);
		
		return $this->response($list);
	}
	
	/**
	 * 获取商品海报
	 */
	public function poster()
	{
		if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));
		
		$promotion_type = 'pintuan';
		$qrcode_param = json_decode($this->params['qrcode_param'], true);
		$qrcode_param['source_member'] = $qrcode_param['source_member'] ?? 0;
		$poster = new Poster();
		$res = $poster->goods($this->params['app_type'], $this->params['page'], $qrcode_param, $promotion_type);
		return $this->response($res);
	}
}