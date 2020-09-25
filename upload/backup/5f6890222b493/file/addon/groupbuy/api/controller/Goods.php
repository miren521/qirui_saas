<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

namespace addon\groupbuy\api\controller;

use addon\groupbuy\model\Groupbuy as GroupbuyModel;
use app\api\controller\BaseApi;
use app\model\shop\Shop as ShopModel;
use addon\groupbuy\model\Poster;
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
		$groupbuy_id = isset($this->params['id']) ? $this->params['id'] : 0;
		$sku_id = isset($this->params['sku_id']) ? $this->params['sku_id'] : 0;
		if (empty($groupbuy_id)) {
			return $this->response($this->error('', 'REQUEST_GROUPBUY_ID'));
		}
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		
		$groupbuy_model = new GroupbuyModel();
		$condition = [
			[ 'sku.sku_id', '=', $sku_id ],
			[ 'pg.groupbuy_id', '=', $groupbuy_id ],
		];
		$info = $groupbuy_model->getGroupbuyGoodsDetail($condition);
		return $this->response($info);
	}
	
	/**
	 * 拼团商品详情信息
	 */
	public function detail()
	{
		$groupbuy_id = isset($this->params['id']) ? $this->params['id'] : 0;
		if (empty($groupbuy_id)) {
			return $this->response($this->error('', 'REQUEST_GROUPBUY_ID'));
		}
		
		$groupbuy_model = new GroupbuyModel();
		$condition = [
			[ 'pg.groupbuy_id', '=', $groupbuy_id ],
		];
		$goods_sku_detail = $groupbuy_model->getGroupbuyGoodsDetail($condition);
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
			[ 'pg.status', '=', 2 ],// 状态（1未开始  2进行中  3已结束）
			[ 'sku.stock', '>', 0 ],
			[ 'sku.goods_state', '=', 1 ],
			[ 'sku.verify_state', '=', 1 ],
			[ 'sku.is_delete', '=', 0 ],
		];
		if (!empty($site_id)) {
			$condition[] = [ 'sku.site_id', '=', $site_id ];
		}
		$groupbuy_model = new GroupbuyModel();

        $alias = 'pg';
        $join = [
            [ 'goods g', 'pg.sku_id = g.sku_id', 'inner' ],
            [ 'goods_sku sku', 'pg.sku_id = sku.sku_id', 'inner' ]
        ];

        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = pg.site_id', 'inner'];
        $condition[] = ['s.shop_status', '=', 1];
		$list = $groupbuy_model->getGroupbuyGoodsPageList($condition, $page, $page_size, '', '*', $alias, $join);
		
		return $this->response($list);
	}
	
	/**
	 * 获取商品海报
	 */
	public function poster()
	{
		if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));
		
		$promotion_type = 'groupbuy';
		$qrcode_param = json_decode($this->params['qrcode_param'], true);
		$qrcode_param['source_member'] = $qrcode_param['source_member'] ?? 0;
		$poster = new Poster();
		$res = $poster->goods($this->params['app_type'], $this->params['page'], $qrcode_param, $promotion_type);
		return $this->response($res);
	}
}