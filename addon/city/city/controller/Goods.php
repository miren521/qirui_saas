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


namespace addon\city\city\controller;

use app\model\goods\Goods as GoodsModel;
use app\model\goods\Config as GoodsConfigModel;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\goods\GoodsEvaluate;

/**
 * 商品管理 控制器
 */
class Goods extends BaseCity
{
	/******************************* 正常商品列表及相关操作 ***************************/

	/**
	 * 商品列表
	 */
	public function lists()
	{
		$goods_model = new GoodsModel();
		if (request()->isAjax()) {
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', "");
			$search_text_type = input('search_text_type', "goods_name");
			$goods_state = input('goods_state', "");
			$verify_state = input('verify_state', "");
			$category_id = input('category_id', "");
			$brand_id = input('goods_brand', '');
			$goods_attr_class = input("goods_attr_class", "");
			$site_id = input("site_id", "");
			$goods_class = input('goods_class', "");

			$condition[] = [ 'website_id', '=', $this->site_id ];
			if (!empty($search_text)) {
				$condition[] = [ $search_text_type, 'like', '%' . $search_text . '%' ];
			}

			if ($goods_class !== "") {
				$condition[] = [ 'goods_class', '=', $goods_class ];
			}
			if ($goods_state !== '') {
				$condition[] = [ 'goods_state', '=', $goods_state ];
			}
			if ($verify_state !== '') {
				$condition[] = [ 'verify_state', '=', $verify_state ];
			}
			if (!empty($category_id)) {
				$condition[] = [ 'category_id|category_id_1|category_id_2|category_id_3', '=', $category_id ];
			}
			if ($brand_id) {
				$condition[] = [ 'brand_id', '=', $brand_id ];
			}
			if ($goods_attr_class) {
				$condition[] = [ 'goods_attr_class', '=', $goods_attr_class ];
			}
			if (!empty($site_id)) {
				$condition[] = [ 'site_id', '=', $site_id ];
			}

			$res = $goods_model->getGoodsPageList($condition, $page_index, $page_size);
			return $res;
		} else {
			$verify_state = $goods_model->getVerifyState();
			$arr = [];
			foreach ($verify_state as $k => $v) {
				// 过滤已审核状态
				if ($k != 1) {
					$total = $goods_model->getGoodsTotalCount([ [ 'verify_state', '=', $k ], [ 'website_id', '=', $this->site_id ] ]);
					$total = $total[ 'data' ];
					$arr[] = [
						'state' => $k,
						'value' => $v,
						'count' => $total
					];
				}
			}
			$verify_state = $arr;
			$this->assign("verify_state", $verify_state);
			return $this->fetch('goods/lists', [], $this->replace);
		}
	}

	/**
	 * 刷新审核状态商品数量
	 */
	public function refreshVerifyStateCount()
	{
		if (request()->isAjax()) {
			$goods_model = new GoodsModel();
			$verify_state = $goods_model->getVerifyState();
			$arr = [];
			foreach ($verify_state as $k => $v) {
				// 过滤已审核状态
				if ($k != 1) {
					$total = $goods_model->getGoodsTotalCount([ [ 'verify_state', '=', $k ], [ 'is_delete', '=', 0 ], [ 'website_id', '=', $this->site_id ] ]);
					$total = $total[ 'data' ];
					$arr[] = [
						'state' => $k,
						'value' => $v,
						'count' => $total
					];
				}
			}
			$verify_state = $arr;
			return $verify_state;
		}
	}

	/**
	 * 获取SKU商品列表
	 * @return \multitype
	 */
	public function getGoodsSkuList()
	{
		if (request()->isAjax()) {
			$goods_id = input("goods_id", 0);
			$goods_model = new GoodsModel();
			$res = $goods_model->getGoodsSkuList([ [ 'goods_id', '=', $goods_id ] ], 'sku_id,sku_name,price,stock,sale_num,sku_image,spec_name');
			return $res;
		}
	}

	/******************************* 违规下架商品列表及相关操作 ***************************/

	/**
	 * 违规下架
	 */
	public function lockup()
	{
		if (request()->isAjax()) {
			$verify_state_remark = input("verify_state_remark", 0);
			$goods_ids = input("goods_ids", 0);
			$goods_model = new GoodsModel();
			$res = $goods_model->lockup([ [ 'goods_id', 'in', $goods_ids ] ], $verify_state_remark);
			$this->addLog("商品违规下架id:" . $goods_ids . "原因:" . $verify_state_remark);
			return $res;
		}
	}

	/**
	 * 获取商品违规或审核失败说明
	 * @return \multitype
	 */
	public function getVerifyStateRemark()
	{
		if (request()->isAjax()) {
			$goods_id = input("goods_id", 0);
			$goods_model = new GoodsModel();
			$res = $goods_model->getGoodsInfo([ [ 'goods_id', '=', $goods_id ], [ 'verify_state', 'in', [ -2, 10 ] ] ], 'verify_state_remark');
			return $res;
		}
	}

	/******************************* 待审核商品列表及相关操作 ***************************/

	/**
	 * 商品审核
	 */
	public function verifyOn()
	{
		if (request()->isAjax()) {
			$goods_ids = input("goods_ids", 0);
			$verify_state = input("verify_state", -2);
			$verify_state_remark = input("verify_state_remark", '');
			$goods_model = new GoodsModel();
			$res = $goods_model->modifyVerifyState($goods_ids, $verify_state, $verify_state_remark);
			return $res;
		}

	}

	/**
	 * 审核设置
	 */
	public function verifyConfig()
	{
		if (request()->isAjax()) {

			$is_open = input("is_open", 0);
			$data = [
				'is_open' => $is_open
			];
			$goods_config = new GoodsConfigModel();
			$res = $goods_config->setVerifyConfig($data);
			return $res;
		} else {
			$goods_config = new GoodsConfigModel();
			$goods_verify_info = $goods_config->getVerifyConfig();
			$goods_verify_info = $goods_verify_info[ 'data' ];
			$this->assign("goods_verify_info", $goods_verify_info[ 'value' ]);
			return $this->fetch('goods/verify_config', [], $this->replace);
		}
	}

	/******************************* 商品评价列表及相关操作 ***************************/

	/**
	 * 商品评价
	 */
	public function evaluateList()
	{
		if (request()->isAjax()) {
			$page_index = input('page', 1);
			$page_size = input('limit', PAGE_LIST_ROWS);
			$site_id = input("site_id", "");
			$explain_type = input('explain_type', ''); //1好评2中评3差评
			$search_text = input('search_text', "");
			$search_type = input('search_type', "sku_name");

			$condition[] = [ 'website_id', '=', $this->site_id ];
			//评分类型
			if ($explain_type != "") {
				$condition[] = [ "explain_type", "=", $explain_type ];
			}
			if (!empty($search_text)) {
				if (!empty($search_type)) {
					$condition[] = [ $search_type, 'like', '%' . $search_text . '%' ];
				} else {
					$condition[] = [ 'sku_name', 'like', '%' . $search_text . '%' ];
				}
			}
			if (!empty($site_id)) {
				$condition[] = [ 'site_id', '=', $site_id ];
			}

			$evaluate_model = new GoodsEvaluate();
			$res = $evaluate_model->getEvaluatePageList($condition, $page_index, $page_size);
			return $res;
		} else {
			return $this->fetch('goods/evaluate_list', [], $this->replace);
		}
	}

	/**
	 * 评价删除
	 */
	public function deleteEvaluate()
	{
		if (request()->isAjax()) {
			$id = input('id', '');
			$evaluate_model = new GoodsEvaluate();
			$res = $evaluate_model->deleteEvaluate($id);
			$this->addLog("删除商品评价id:" . $id);
			return $res;
		}
	}

	/**
	 * 商品推广
	 * return
	 */
	public function goodsUrl()
	{
		$goods_id = input('goods_id', '');
		$goods_model = new GoodsModel();
		$goods_sku_info = $goods_model->getGoodsSkuInfo([ [ 'goods_id', '=', $goods_id ] ], 'sku_id,goods_name');
		$goods_sku_info = $goods_sku_info[ 'data' ];
		$res = $goods_model->qrcode($goods_sku_info[ 'sku_id' ], $goods_sku_info[ 'goods_name' ]);
		return $res;
	}

	/**
	 * 商品预览
	 * return
	 */
	public function goodsPreview()
	{
		$goods_id = input('goods_id', '');
		$goods_model = new GoodsModel();
		$goods_sku_info = $goods_model->getGoodsSkuInfo([ [ 'goods_id', '=', $goods_id ] ], 'sku_id,goods_name');
		$goods_sku_info = $goods_sku_info[ 'data' ];
		$res = $goods_model->qrcode($goods_sku_info[ 'sku_id' ], $goods_sku_info[ 'goods_name' ]);
		return $res;
	}


    /**
     * 商品选择组件
     * @return array|mixed|void
     */
    public function goodsSelect()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $goods_name = input('goods_name', '');
            $goods_id = input('goods_id', 0);
            $is_virtual = input('is_virtual', '');// 是否虚拟类商品（0实物1.虚拟）
            $min_price = input('min_price', 0);
            $max_price = input('max_price', 0);
            $goods_class = input('goods_class', "");// 商品类型，实物、虚拟
            $category_id = input('category_id', "");// 商品分类id
            $promotion = input('promotion', '');//营销活动标识：pintuan、groupbuy、fenxiao、bargain
            $promotion_type = input('promotion_type', "");

            if (!empty($promotion) && addon_is_exit($promotion)) {
                $pintuan_name = input('pintuan_name', '');//拼团活动
                $goods_list = event('GoodsListPromotion', ['page' => $page, 'page_size' => $page_size, 'website_id' => $this->site_id, 'promotion' => $promotion, 'pintuan_name' => $pintuan_name, 'goods_name' => $goods_name], true);
            } else {
                $condition = [
                    ['g.is_delete', '=', 0],
                    ['g.goods_state', '=', 1],
                ];

                //只查看处于开启状态的店铺
                $alias = 'g';
                $join = [
                    [ 'shop s', 's.site_id = g.site_id', 'inner'],
                ];
                $condition[] = ['s.shop_status', '=', 1];

                if(!empty($this->site_id)) $condition[] = ['g.website_id', '=', $this->site_id];
                if (!empty($goods_name)) {
                    $condition[] = ['g.goods_name', 'like', '%' . $goods_name . '%'];
                }
                if ($is_virtual !== "") {
                    $condition[] = ['g.is_virtual', '=', $is_virtual];
                }
                if (!empty($goods_id)) {
                    $condition[] = ['g.goods_id', '=', $goods_id];
                }
                if (!empty($category_id)) {
                    $condition[] = ['g.category_id', 'like', [$category_id, '%' . $category_id . ',%', '%' . $category_id, '%,' . $category_id . ',%'], 'or'];
                }

                if (!empty($promotion_type)) {
                    $condition[] = ['g.promotion_addon', 'like', "%{$promotion_type}%"];
                }


                if ($goods_class !== "") {
                    $condition[] = ['g.goods_class', '=', $goods_class];
                }

                if ($min_price != "" && $max_price != "") {
                    $condition[] = ['g.price', 'between', [$min_price, $max_price]];
                } elseif ($min_price != "") {
                    $condition[] = ['g.price', '<=', $min_price];
                } elseif ($max_price != "") {
                    $condition[] = ['g.price', '>=', $max_price];
                }

                $order = 'g.create_time desc';
                $goods_model = new GoodsModel();
                $field = 'g.goods_id,g.goods_name,g.goods_class_name,g.goods_image,g.price,g.goods_stock,g.create_time,g.is_virtual';

                $goods_list = $goods_model->getGoodsPageList($condition, $page, $page_size, $order, $field, $alias, $join);

                if (!empty($goods_list[ 'data' ][ 'list' ])) {
                    foreach ($goods_list[ 'data' ][ 'list' ] as $k => $v) {
                        $goods_sku_list = $goods_model->getGoodsSkuList([['goods_id', '=', $v[ 'goods_id' ]]], 'sku_id,sku_name,price,stock,sku_image,goods_id,goods_class_name');
                        $goods_sku_list = $goods_sku_list[ 'data' ];
                        $goods_list[ 'data' ][ 'list' ][ $k ][ 'sku_list' ] = $goods_sku_list;
                    }

                }
            }
            return $goods_list;
        } else {

            //已经选择的商品sku数据
            $select_id = input('select_id', '');
            $mode = input('mode', 'spu');
            $max_num = input('max_num', 0);
            $min_num = input('min_num', 0);
            $is_virtual = input('is_virtual', '');
            $disabled = input('disabled', 0);
            $promotion = input('promotion', '');//营销活动标识：pintuan、groupbuy、seckill、fenxiao

            $this->assign('select_id', $select_id);
            $this->assign('mode', $mode);
            $this->assign('max_num', $max_num);
            $this->assign('min_num', $min_num);
            $this->assign('is_virtual', $is_virtual);
            $this->assign('disabled', $disabled);
            $this->assign('promotion', $promotion);

            // 营销活动
            $goods_promotion_type = event('GoodsPromotionType');
            $this->assign('promotion_type', $goods_promotion_type);


            $goods_category_model = new GoodsCategoryModel();

            $field = 'category_id,category_name as title';
            $condition = [
                [ 'pid', '=', 0 ],
                [ 'level', '=', 1 ],
            ];
            $list = $goods_category_list = $goods_category_model->getCategoryByParent($condition, $field);
            $list = $list['data'];
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $two_list = $goods_category_list = $goods_category_model->getCategoryByParent(
                        [
                            [ 'pid', '=', $v['category_id'] ],
                            [ 'level', '=', 2 ],
                        ],
                        $field
                    );

                    $two_list = $two_list['data'];
                    if(!empty($two_list)){

                        foreach($two_list as $two_k=>$two_v){
                            $three_list = $goods_category_list = $goods_category_model->getCategoryByParent(
                                [
                                    [ 'pid', '=', $two_v['category_id'] ],
                                    [ 'level', '=', 3 ],
                                ],
                                $field
                            );
                            $two_list[$two_k]['children'] = $three_list['data'];
                        }
                    }

                    $list[$k]['children'] = $two_list;
                }
            }

            $this->assign("category_list", $list);
            return $this->fetch("goods/goods_select", [], $this->replace);
        }
    }
}