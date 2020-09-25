<?php

/**
 * Goodssku.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

namespace app\api\controller;

use addon\coupon\model\CouponType;
use addon\platformcoupon\model\PlatformcouponType;
use app\model\goods\Goods;
use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsAttribute;
use app\model\shop\Shop as ShopModel;

/**
 * 商品sku
 * @author Administrator
 *
 */
class Goodssku extends BaseApi
{

	/**
	 * 基础信息
	 */
	public function info()
	{
		$sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		$goods = new Goods();
		$field = 'sku_id,sku_name,sku_spec_format,price,market_price,discount_price,promotion_type,start_time,end_time,stock,sku_image,sku_images,goods_spec_format';
		$info = $goods->getGoodsSkuInfo([ [ 'sku_id', '=', $sku_id ] ], $field);
		return $this->response($info);
	}

	/**
	 * 详情信息
	 */
	public function detail()
	{
		$sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}

		$res = [];

        $goods_model = new Goods();
		$goods_sku_detail = $goods_model->getGoodsSkuDetail($sku_id);
		$goods_sku_detail = $goods_sku_detail[ 'data' ];
		$res[ 'goods_sku_detail' ] = $goods_sku_detail;

		if (empty($goods_sku_detail)) return $this->response($this->error($res));
        $goods_info = $goods_model->getGoodsInfo([['goods_id', '=', $goods_sku_detail['goods_id']]])['data'] ?? [];
        if (empty($goods_info)) return $this->response($this->error([], '找不到商品'));
        $res[ 'goods_info' ] = $goods_info;


        if ($goods_sku_detail['max_buy'] > 0){
            $token = $this->checkToken();
            if($this->member_id > 0){
                $res[ 'goods_sku_detail' ]['purchased_num'] = $goods_model->getGoodsPurchasedNum($goods_sku_detail['goods_id'], $this->member_id);
            }
        }
		//店铺信息
		$shop_model = new ShopModel();
		$shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $goods_sku_detail[ 'site_id' ] ] ], 'site_id,site_name,is_own,logo,avatar,banner,seo_description,qq,ww,telephone,shop_desccredit,shop_servicecredit,shop_deliverycredit,shop_baozh,shop_baozhopen,shop_baozhrmb,shop_qtian,shop_zhping,shop_erxiaoshi,shop_tuihuo,shop_shiyong,shop_shiti,shop_xiaoxie,shop_sales,sub_num');

		$shop_info = $shop_info[ 'data' ];
		$res[ 'shop_info' ] = $shop_info;

		return $this->response($this->success($res));
	}

	public function lists()
	{
		$sku_ids = isset($this->params[ 'sku_ids' ]) ? $this->params[ 'sku_ids' ] : '';
		$field = 'goods_id,sku_id,sku_name,price,market_price,discount_price,stock,sale_num,sku_image,goods_name,site_id,website_id,is_own,is_free_shipping,introduction,promotion_type,is_virtual';
		$goods = new Goods();
		$condition = [
			[ 'sku_id', 'in', $sku_ids ]
		];
		$order_by = 'sort desc,create_time desc';
		$list = $goods->getGoodsSkuList($condition, $field, $order_by);
		return $this->response($list);
	}

	/**
	 * 列表信息
	 */
	public function page()
	{
		$page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
		$page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
		$site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0; //站点id
		$goods_id_arr = isset($this->params[ 'goods_id_arr' ]) ? $this->params[ 'goods_id_arr' ] : ''; //sku_id数组
		$keyword = isset($this->params[ 'keyword' ]) ? $this->params[ 'keyword' ] : ''; //关键词
		$category_id = isset($this->params[ 'category_id' ]) ? $this->params[ 'category_id' ] : 0; //分类
		$category_level = isset($this->params[ 'category_level' ]) ? $this->params[ 'category_level' ] : 0; //分类等级
		$brand_id = isset($this->params[ 'brand_id' ]) ? $this->params[ 'brand_id' ] : 0; //品牌
		$min_price = isset($this->params[ 'min_price' ]) ? $this->params[ 'min_price' ] : 0; //价格区间，小
		$max_price = isset($this->params[ 'max_price' ]) ? $this->params[ 'max_price' ] : 0; //价格区间，大
		$is_free_shipping = isset($this->params[ 'is_free_shipping' ]) ? $this->params[ 'is_free_shipping' ] : -1; //是否免邮
		$is_own = isset($this->params[ 'is_own' ]) ? $this->params[ 'is_own' ] : ''; //是否自营
		$order = isset($this->params[ 'order' ]) ? $this->params[ 'order' ] : "create_time"; //排序（综合、销量、价格）
		$sort = isset($this->params[ 'sort' ]) ? $this->params[ 'sort' ] : "desc"; //升序、降序
		$attr = isset($this->params[ 'attr' ]) ? $this->params[ 'attr' ] : ""; //属性json
		$shop_category_id = isset($this->params[ 'shop_category_id' ]) ? $this->params[ 'shop_category_id' ] : 0;//店内分类
		$condition = [];

		$field = 'gs.goods_id,gs.sku_id,gs.sku_name,gs.price,gs.market_price,gs.discount_price,gs.stock,gs.sale_num,gs.sku_image,gs.goods_name,gs.site_id,gs.website_id,gs.is_own,gs.is_free_shipping,gs.introduction,gs.promotion_type,g.goods_image,g.site_name,gs.goods_spec_format,gs.is_virtual';

		$alias = 'gs';
		$join = [
			[ 'goods g', 'gs.sku_id = g.sku_id', 'inner' ],
		];

        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = gs.site_id', 'inner'];
        $condition[] = ['s.shop_status', '=', 1];

		if (!empty($site_id)) {
			$condition[] = [ 'gs.site_id', '=', $site_id ];
		}

		if (!empty($goods_id_arr)) {
			$condition[] = [ 'gs.goods_id', 'in', $goods_id_arr ];
		}

		if (!empty($keyword)) {
			$condition[] = [ 'g.goods_name|gs.sku_name|gs.keywords', 'like', '%' . $keyword . '%' ];
		}

		if (!empty($category_id) && !empty($category_level)) {
			$condition[] = [ 'gs.category_id_' . $category_level, '=', $category_id ];
		}

		if (!empty($brand_id)) {
			$condition[] = [ 'gs.brand_id', '=', $brand_id ];
		}

		if ($min_price != "" && $max_price != "") {
			$condition[] = [ 'gs.discount_price', 'between', [ $min_price, $max_price ] ];
		} elseif ($min_price != "") {
			$condition[] = [ 'gs.discount_price', '>=', $min_price ];
		} elseif ($max_price != "") {
			$condition[] = [ 'gs.discount_price', '<=', $max_price ];
		}

		if (isset($is_free_shipping) && !empty($is_free_shipping) && $is_free_shipping > -1) {
			$condition[] = [ 'gs.is_free_shipping', '=', $is_free_shipping ];
		}

		if ($is_own !== '') {
			$condition[] = [ 'gs.is_own', '=', $is_own ];
		}

		// 非法参数进行过滤
		if ($sort != "desc" && $sort != "asc") {
			$sort = "";
		}

		// 非法参数进行过滤
		if ($order != '') {
			if ($order != "sale_num" && $order != "discount_price") {
				$order = 'gs.create_time';
			} else {
				$order = 'gs.' . $order;
			}
			$order_by = $order . ' ' . $sort;
		} else {
			$order_by = 'gs.sort desc,gs.create_time desc';
		}

		//拿到商品属性，查询sku_id
		if (!empty($attr)) {
			$attr = json_decode($attr, true);
			$attr_id = [];
			$attr_value_id = [];
			foreach ($attr as $k => $v) {
				$attr_id[] = $v[ 'attr_id' ];
				$attr_value_id[] = $v[ 'attr_value_id' ];
			}
			$goods_attribute = new GoodsAttribute();
			$attribute_condition = [
				[ 'attr_id', 'in', implode(",", $attr_id) ],
				[ 'attr_value_id', 'in', implode(",", $attr_value_id) ],
                [ 'app_module', '=', 'shop']
			];
			$attribute_list = $goods_attribute->getAttributeIndexList($attribute_condition, 'sku_id');
			$attribute_list = $attribute_list[ 'data' ];
			if (!empty($attribute_list)) {
				$sku_id = [];
				foreach ($attribute_list as $k => $v) {
					$sku_id[] = $v[ 'sku_id' ];
				}
				$condition[] = [
					[ 'gs.sku_id', 'in', implode(",", $sku_id) ]
				];
			}
		}

		$condition[] = [ 'gs.goods_state', '=', 1 ];
		$condition[] = [ 'gs.verify_state', '=', 1 ];
		$condition[] = [ 'gs.is_delete', '=', 0 ];


		// 优惠券
		$coupon = isset($this->params[ 'coupon_type' ]) ? $this->params[ 'coupon_type' ] : 0; //优惠券
		if ($coupon > 0) {
			$coupon_type = new CouponType();
			$coupon_type_info = $coupon_type->getInfo([
				[ 'coupon_type_id', '=', $coupon ],
				[ 'site_id', '=', $site_id ],
				[ 'goods_type', '=', 2 ]
			], 'goods_ids');
			$coupon_type_info = $coupon_type_info[ 'data' ];
			if (isset($coupon_type_info[ 'goods_ids' ]) && !empty($coupon_type_info[ 'goods_ids' ])) {
				$condition[] = [ 'g.goods_id', 'in', explode(',', trim($coupon_type_info[ 'goods_ids' ], ',')) ];
			}
		}
		//平台优惠券
		$platform_coupon = isset($this->params[ 'platform_coupon_type' ]) ? $this->params[ 'platform_coupon_type' ] : 0; //平台优惠券
		if ($platform_coupon > 0) {
			$platform_coupon_type = new PlatformcouponType();
			$platform_coupon_type_info = $platform_coupon_type->getInfo([
				[ 'platformcoupon_type_id', '=', $platform_coupon ],
				[ 'use_scenario', '=', 2 ]
			], 'group_ids');
			$platform_coupon_type_info = $platform_coupon_type_info[ 'data' ];
			if (!empty($platform_coupon_type_info)) {
				$condition[] = [ 's.group_id', 'in', explode(',', trim($platform_coupon_type_info[ 'group_ids' ], ',')) ];
			}
		}
		//店内分类
		if ($shop_category_id > 0) {
			$condition[] = [ 'gs.goods_shop_category_ids', 'like', [ $shop_category_id, '%' . $shop_category_id . ',%', '%' . $shop_category_id, '%,' . $shop_category_id . ',%' ], 'or' ];
		}

		$goods = new Goods();
		$list = $goods->getGoodsSkuPageList($condition, $page, $page_size, $order_by, $field, $alias, $join);
		return $this->response($list);
	}

	/**
	 * 商品推荐
	 * @return string
	 */
	public function recommend()
	{
		$page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
		$page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
		$condition = [
			[ 'gs.goods_state', '=', 1 ],
			[ 'gs.verify_state', '=', 1 ],
			[ 'gs.is_delete', '=', 0 ]
		];
		$goods = new Goods();
		$field = 'gs.goods_id,gs.sku_id,gs.sku_name,gs.price,gs.market_price,gs.discount_price,gs.stock,gs.sale_num,gs.sku_image,gs.goods_name,gs.site_id,gs.website_id,gs.is_own,gs.is_free_shipping,gs.introduction,gs.promotion_type,g.goods_image';
		$alias = 'gs';
		$join = [
			[ 'goods g', 'gs.sku_id = g.sku_id', 'inner' ]
		];
		$order_by = 'gs.sort desc,gs.create_time desc';
        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = gs.site_id', 'inner'];
        $condition[] = ['s.shop_status', '=', 1];

		$list = $goods->getGoodsSkuPageList($condition, $page, $page_size, $order_by, $field, $alias, $join);
		return $this->response($list);
	}

	/**
	 * 商品二维码
	 * return
	 */
	public function goodsQrcode()
	{
		$sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		$goods_model = new GoodsModel();
		$goods_sku_info = $goods_model->getGoodsSkuInfo([ [ 'sku_id', '=', $sku_id ] ], 'sku_id,goods_name');
		$goods_sku_info = $goods_sku_info[ 'data' ];
		$res = $goods_model->qrcode($goods_sku_info[ 'sku_id' ], $goods_sku_info[ 'goods_name' ]);
		return $this->response($res);
	}
}
