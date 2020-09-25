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


namespace addon\fenxiao\api\controller;

use app\api\controller\BaseApi;
use addon\fenxiao\model\Fenxiao as FenxiaoModel;
use addon\fenxiao\model\FenxiaoGoodsSku as FenxiaoGoodsSkuModel;
use addon\fenxiao\model\FenxiaoGoodsCollect as FenxiaoGoodsCollectModel;
use app\model\goods\GoodsAttribute;
use addon\fenxiao\model\Config as ConfigModel;

/**
 * 分销商品
 */
class Goods extends BaseApi
{

	/**
	 * 分销商品详情
	 * @return false|string
	 */
	public function detail()
	{
		$token = $this->checkToken();
		if ($token[ 'code' ] < 0) return $this->response($token);

		$sku_id = isset($this->params[ 'sku_id' ]) ? $this->params[ 'sku_id' ] : 0;
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}

		$fenxiao_model = new FenxiaoModel();
		$fenxiao_info = $fenxiao_model->getFenxiaoInfo([ [ 'member_id', '=', $this->member_id ] ], "fenxiao_id,level_id");
		$fenxiao_info = $fenxiao_info[ 'data' ];

		$fenxiao_goods_sku_model = new FenxiaoGoodsSkuModel();

		$condition = [ [ 'fgs.sku_id', '=', $sku_id ], [ 'fgs.level_id', '=', $fenxiao_info[ 'level_id' ] ] ];
		$res = $fenxiao_goods_sku_model->getFenxiaoGoodsSkuDetail($condition);

		$config = new ConfigModel();
		$basics_config = $config->getFenxiaoBasicsConfig();
		$basics_config = $basics_config[ 'data' ][ 'value' ];

		$words_config = $config->getFenxiaoWordsConfig();
		$words_config = $words_config[ 'data' ][ 'value' ];

		if ($res[ 'data' ]) {
			$discount_price = $res[ 'data' ][ 'discount_price' ];

			// 一级佣金比例/金额
			$money = 0;
			if ($res['data']['one_rate'] > 0) {
				$money = number_format($discount_price * $res[ 'data' ][ 'one_rate' ] / 100, 2);
			}elseif ($res['data']['one_money'] > 0){
                $money = $res['data']['one_money'];
            }

//			if ($basics_config['level'] == 1) {
//				// 一级佣金比例/金额
//				$money = $res['data']['one_money'];
//				if ($res['data']['one_rate']) {
//					$money = number_format($discount_price * $res['data']['one_rate'] / 100, 2);
//				}
//			} elseif ($basics_config['level'] == 2) {
//				// 二级佣金比例/金额
//				$money = $res['data']['two_money'];
//				if ($res['data']['two_rate']) {
//					$money = number_format($discount_price * $res['data']['two_rate'] / 100, 2);
//				}
//			} elseif ($basics_config['level'] == 3) {
//				// 三级佣金比例/金额
//				$money = $res['data']['three_money'];
//				if ($res['data']['three_rate']) {
//					$money = number_format($discount_price * $res['data']['three_rate'] / 100, 2);
//				}
//			}

			$res[ 'data' ][ 'commission_money' ] = $money;
			$res[ 'data' ][ 'words_account' ] = $words_config[ 'account' ];

		}
		return $this->response($res);
	}

	/**
	 * 分销商品分页列表
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token[ 'code' ] < 0) return $this->response($token);

		$page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
		$page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
		$keyword = isset($this->params[ 'keyword' ]) ? $this->params[ 'keyword' ] : '';//关键词
		$category_id = isset($this->params[ 'category_id' ]) ? $this->params[ 'category_id' ] : 0;//分类
		$category_level = isset($this->params[ 'category_level' ]) ? $this->params[ 'category_level' ] : 0;//分类等级
		$brand_id = isset($this->params[ 'brand_id' ]) ? $this->params[ 'brand_id' ] : 0;//品牌
		$min_price = isset($this->params[ 'min_price' ]) ? $this->params[ 'min_price' ] : 0;//价格区间，小
		$max_price = isset($this->params[ 'max_price' ]) ? $this->params[ 'max_price' ] : 0;//价格区间，大
		$is_free_shipping = isset($this->params[ 'is_free_shipping' ]) ? $this->params[ 'is_free_shipping' ] : 0;//是否免邮
		$is_own = isset($this->params[ 'is_own' ]) ? $this->params[ 'is_own' ] : '';//是否自营
		$order = isset($this->params[ 'order' ]) ? $this->params[ 'order' ] : "create_time";//排序（综合、销量、价格）
		$sort = isset($this->params[ 'sort' ]) ? $this->params[ 'sort' ] : "desc";//升序、降序
		$attr = isset($this->params[ 'attr' ]) ? $this->params[ 'attr' ] : "";//属性json

		$condition = [
			[ 'g.is_fenxiao', '=', 1 ],
			[ 'gs.goods_state', '=', 1 ],
			[ 'gs.verify_state', '=', 1 ],
			[ 'gs.is_delete', '=', 0 ]
		];

		if (!empty($keyword)) {
			$condition[] = [ 'gs.sku_name|gs.keywords', 'like', '%' . $keyword . '%' ];
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

		if (!empty($is_free_shipping)) {
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
			$order_by = 'fgs.goods_sku_id desc,gs.sort desc,gs.create_time desc';
		}

//		拿到商品属性，查询sku_id
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

		if (!empty($keyword)) {
			$condition[] = [ 'gs.sku_name', 'like', '%' . $keyword . '%' ];
		}

		$fenxiao_model = new FenxiaoModel();
		$fenxiao_info = $fenxiao_model->getFenxiaoInfo([ [ 'member_id', '=', $this->member_id ] ], "fenxiao_id,level_id");
		$fenxiao_info = $fenxiao_info[ 'data' ];

		// 获取当前用户的分销等级
		$condition[] = [ 'fgs.level_id', '=', $fenxiao_info[ 'level_id' ] ];


//		$config = new ConfigModel();
//		$basics_config = $config->getFenxiaoBasicsConfig();
//		$basics_config = $basics_config[ 'data' ][ 'value' ];

		$fenxiao_goods_sku_model = new FenxiaoGoodsSkuModel();

        $alias = 'fgs';
        $join = [
            [ 'goods_sku gs', 'fgs.sku_id = gs.sku_id', 'inner' ],
            [ 'goods g', 'fgs.goods_id = g.goods_id', 'inner' ],
        ];
        //todo 商品列表不查询  所在店铺已关闭
        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = gs.site_id', 'inner'];
        $condition[] = ['s.shop_status', '=', 1];
        $field = 'fgs.goods_sku_id,fgs.goods_id,fgs.sku_id,fgs.level_id,fgs.one_rate,fgs.one_money,fgs.two_rate,fgs.two_money,fgs.three_rate,fgs.three_money,gs.sku_name,gs.discount_price,gs.stock,gs.sale_num,gs.sku_image,gs.site_id';
		$list = $fenxiao_goods_sku_model->getFenxiaoGoodsSkuPageList($condition, $page, $page_size, $order_by, $field, $alias, $join);

		$fenxiao_goods_collect_model = new FenxiaoGoodsCollectModel();

		// 计算佣金比率
		foreach ($list[ 'data' ][ 'list' ] as $k => $v) {

			$collection_info = $fenxiao_goods_collect_model->getCollectInfo([
				[ 'member_id', '=', $this->member_id ],
				[ 'sku_id', '=', $v[ 'sku_id' ] ],
				[ 'fenxiao_id', '=', $fenxiao_info[ 'fenxiao_id' ] ],
			], 'collect_id');

			// 查询是否关注该分销商品
			$collection_info = $collection_info[ 'data' ];
			if (!empty($collection_info)) {
				$list[ 'data' ][ 'list' ][ $k ][ 'is_collect' ] = 1;
				$list[ 'data' ][ 'list' ][ $k ][ 'collect_id' ] = $collection_info[ 'collect_id' ];
			} else {
				$list[ 'data' ][ 'list' ][ $k ][ 'is_collect' ] = 0;
			}

			$discount_price = $v[ 'discount_price' ];

			// 一级佣金比例/金额
            $money = 0;
			if ($v['one_rate'] > 0) {
				$money = number_format($discount_price * $v[ 'one_rate' ] / 100, 2);
            }elseif ($v['one_money'] > 0){
                $money = $v['one_money'];
            }

//			if ($basics_config['level'] == 1) {
//
//				// 一级佣金比例/金额
//				$money = $v['one_money'];
//				if ($v['one_rate']) {
//					$money = number_format($discount_price * $v['one_rate'] / 100, 2);
//				}
//			} elseif ($basics_config['level'] == 2) {
//				// 二级佣金比例/金额
//				$money = $v['two_money'];
//				if ($v['two_rate']) {
//					$money = number_format($discount_price * $v['two_rate'] / 100, 2);
//				}
//			} elseif ($basics_config['level'] == 3) {
//
//				// 三级佣金比例/金额
//				$money = $v['three_money'];
//				if ($v['three_rate']) {
//					$money = number_format($discount_price * $v['three_rate'] / 100, 2);
//				}
//			}

			$list[ 'data' ][ 'list' ][ $k ][ 'commission_money' ] = $money;

		}

		return $this->response($list);

	}

}