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

namespace addon\wholesale\api\controller;

use addon\wholesale\model\Wholesale as WholesaleModel;
use app\api\controller\BaseApi;
use app\model\shop\Shop as ShopModel;
use addon\wholesale\model\Poster;
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
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		
		$wholesale_model = new WholesaleModel();
		$condition = [
			[ 'gs.sku_id', '=', $sku_id ],
            [ 'wholesale_sku_id', '>', 0]
		];
		$info = $wholesale_model->getWholesaleSkuDetail($condition);
		return $this->response($info);
	}
	
	/**
	 * 拼团商品详情信息
	 */
	public function detail()
	{
        $sku_id = isset($this->params['sku_id']) ? $this->params['sku_id'] : 0;
        if (empty($sku_id)) {
            return $this->response($this->error('', 'REQUEST_SKU_ID'));
        }
		$wholesale_model = new WholesaleModel();
        $condition = [
            [ 'gs.sku_id', '=', $sku_id ],
            [ 'wholesale_sku_id', '>', 0]
        ];
		$goods_sku_detail = $wholesale_model->getWholesaleSkuDetail($condition);
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
     * 分页
     * @return false|string
     */
	public function page()
	{
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		$site_id = $this->params['site_id'] ?? 0;
		$condition = [
			[ 'sku.stock', '>', 0 ],
			[ 'sku.goods_state', '=', 1 ],
			[ 'g.verify_state', '=', 1 ],
			[ 'g.is_delete', '=', 0 ],
            [ 'wg.wholesale_goods_id', '>', 0]
		];
		if($site_id > 0){
            $condition[] = ['wg.site_id', '=', $site_id];
        }
		$wholesale_model = new WholesaleModel();

        $alias = 'g';
        $join = [
            [ 'wholesale_goods wg', 'wg.goods_id = g.goods_id', 'inner' ],
            [ 'goods_sku sku', 'g.sku_id = sku.sku_id', 'inner' ]
        ];
        $field = 'g.*,wg.wholesale_goods_id, wg.max_price, wg.min_price, wg.min_num, wg.status,sku.sku_id,sku.price,sku.sku_name,sku.sku_image';
        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = g.site_id', 'left'];
        $condition[] = ['s.shop_status', '=', 1];

		$list = $wholesale_model->getWholesaleGoodsViewPageList($condition, $page, $page_size, 'g.create_time desc', $field, $alias, $join);
		return $this->response($list);
	}

    /**
     * 获取商品海报
     */
    public function poster()
    {
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));

        $promotion_type = 'wholesale';
        $qrcode_param = json_decode($this->params['qrcode_param'], true);
        $qrcode_param['source_member'] = $qrcode_param['source_member'] ?? 0;
        $poster = new Poster();
        $res = $poster->goods($this->params['app_type'], $this->params['page'], $qrcode_param, $promotion_type);
        return $this->response($res);
    }
}