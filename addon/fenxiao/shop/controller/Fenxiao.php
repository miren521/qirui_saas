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


namespace addon\fenxiao\shop\controller;

use addon\fenxiao\model\FenxiaoGoods as FenxiaoGoodsModel;
use addon\fenxiao\model\FenxiaoGoodsSku as FenxiaoGoodsSkuModel;
use addon\fenxiao\model\FenxiaoLevel as FenxiaoLevelModel;
use addon\fenxiao\model\FenxiaoOrder as FenxiaoOrderModel;
use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;
use app\shop\controller\BaseShop;
use think\facade\Db;

/**
 * 分销控制器
 */
class Fenxiao extends BaseShop
{
	
	/**
	 * 分销概况
	 */
	public function index()
	{
		//获取分销的总金额
		$order_model = new FenxiaoOrderModel();
		$commission = $order_model->getFenxiaoOrderDetail([ [ 'site_id', '=', $this->site_id ], [ 'is_settlement', '=', 1 ], [ 'is_refund', '=', 0 ] ], 'sum(real_goods_money) as real_goods_money,sum(commission) as commission');
		if ($commission['data']['real_goods_money'] == null) {
			$commission['data']['real_goods_money'] = '0.00';
		}
		if ($commission['data']['commission'] == null) {
			$commission['data']['commission'] = '0.00';
		}
		$this->assign('shop_commission', $commission['data']);
		$goods_model = new GoodsModel();
		$fenxiao_goods_num = $goods_model->getGoodsInfo([ [ 'site_id', '=', $this->site_id ], [ 'is_fenxiao', '=', 1 ] ], 'count(goods_id) as fenxiao_goods_num');
		$this->assign('fenxiao_goods_num', $fenxiao_goods_num['data']['fenxiao_goods_num']);
		return $this->fetch("fenxiao/index");
	}
	
	/**
	 *  商品列表
	 */
	public function lists()
	{
		$goods_model = new GoodsModel();
		if (request()->isAjax()) {
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', "");
			$start_sale = input('start_sale', 0);
			$end_sale = input('end_sale', 0);
			$is_fenxiao = input('is_fenxiao', '');
			$goods_shop_category_ids = input('goods_shop_category_ids', '');
			$condition = [ [ 'is_delete', '=', 0 ], [ 'site_id', '=', $this->site_id ], [ 'verify_state', '=', 1 ] ];
			if (!empty($search_text)) {
				$condition[] = [ 'goods_name', 'like', '%' . $search_text . '%' ];
			}
			if ($is_fenxiao !== "") {
				$condition[] = [ 'is_fenxiao', '=', $is_fenxiao ];
			}
			if (!empty($start_sale)) $condition[] = [ 'sale_num', '>=', $start_sale ];
			if (!empty($end_sale)) $condition[] = [ 'sale_num', '<=', $end_sale ];
			if (!empty($goods_shop_category_ids)) $condition[] = [ 'goods_shop_category_ids', 'like', [ $goods_shop_category_ids, '%' . $goods_shop_category_ids . ',%', '%' . $goods_shop_category_ids, '%,' . $goods_shop_category_ids . ',%' ], 'or' ];
			$res = $goods_model->getGoodsPageList($condition, $page_index, $page_size);
			return $res;
		} else {
			//获取店内分类
			$goods_shop_category_model = new GoodsShopCategoryModel();
			$goods_shop_category_list = $goods_shop_category_model->getShopCategoryTree([ [ 'site_id', "=", $this->site_id ] ], 'category_id,category_name,pid,level');
			$goods_shop_category_list = $goods_shop_category_list['data'];
			$this->assign("goods_shop_category_list", $goods_shop_category_list);
			return $this->fetch("fenxiao/lists");
		}
	}
	
	/**
	 * 添加活动
	 */
	public function config()
	{
		$goods_id = input('goods_id');
		$goods_model = new GoodsModel();
		$fenxiao_sku_model = new FenxiaoGoodsSkuModel();
		$fenxiao_leve_model = new FenxiaoLevelModel();
		$fenxiao_level = $fenxiao_leve_model->getLevelList();
		$goods_info = $goods_model->getGoodsDetail($goods_id);
		if (request()->isAjax()) {
			Db::startTrans();
			try {
				$fenxiao_type = input('fenxiao_type', 1);
				$fenxiao_skus = input('fenxiao', []);
				$is_fenxiao = input('is_fenxiao', 0);
				$goods_data = [ 'is_fenxiao' => $is_fenxiao, 'fenxiao_type' => $fenxiao_type ];
				if ($fenxiao_type == 2) {
					$fenxiao_goods_sku_data = [];
					foreach ($fenxiao_skus as $level_id => $level_data) {
						foreach ($level_data['sku_id'] as $key => $sku_id) {
							$fenxiao_total = 0;
							$fenxiao_level = [ 'one', 'two', 'three' ];
							foreach ($fenxiao_level as $level) {
								if ($level_data[ $level . '_rate' ][ $key ] > 0) {
									$fenxiao_total += $level_data['sku_price'][ $key ] * $level_data[ $level . '_rate' ][ $key ] / 100;
								} elseif ($level_data[ $level . '_money' ][ $key ] > 0) {
									$fenxiao_total += $level_data[ $level . '_money' ][ $key ];
								}
							}
							if (empty($fenxiao_total)) {
								return error(-1, '分销金额不可以为零');
							}
							if ($level_data['sku_price'][ $key ] / $fenxiao_total < 2) {
								return error(-1, '分销总金额不能大于商品sku价格的50%！');
							}
							$fenxiao_sku = [
								'goods_id' => $goods_id,
								'level_id' => $level_id,
								'sku_id' => $sku_id,
								'one_rate' => $level_data['one_rate'][ $key ],
								'one_money' => $level_data['one_money'][ $key ],
								'two_rate' => $level_data['two_rate'][ $key ],
								'two_money' => $level_data['two_money'][ $key ],
								'three_rate' => $level_data['three_rate'][ $key ],
								'three_money' => $level_data['three_money'][ $key ],
							];
							$fenxiao_goods_sku_data[] = $fenxiao_sku;
						}
					}
					$fenxiao_sku_model->deleteSku([ 'goods_id' => $goods_id ]);
					$fenxiao_sku_model->addSkuList($fenxiao_goods_sku_data);
				}
				if ($fenxiao_type == 1) {
					$fenxiao_goods_sku_data = [];
					foreach ($fenxiao_level['data'] as $level) {
						foreach ($goods_info['data']['sku_data'] as $sku) {
							$fenxiao_sku = [
								'goods_id' => $goods_id,
								'level_id' => $level['level_id'],
								'sku_id' => $sku['sku_id'],
								'one_rate' => $level['one_rate'],
								'one_money' => 0,
								'two_rate' => $level['two_rate'],
								'two_money' => 0,
								'three_rate' => $level['three_rate'],
								'three_money' => 0,
							];
							$fenxiao_goods_sku_data[] = $fenxiao_sku;
						}
					}
					$fenxiao_sku_model->deleteSku([ 'goods_id' => $goods_id ]);
					$fenxiao_sku_model->addSkuList($fenxiao_goods_sku_data);
				}
				$fenxiao_goods_model = new FenxiaoGoodsModel();
				
				$re = $fenxiao_goods_model->editGoodsFenxiao($goods_data, [ [ 'goods_id', '=', $goods_id ], [ 'site_id', '=', $this->site_id ] ]);
				Db::commit();
				return $re;
			} catch (Exception $e) {
				Db::rollback();
				return error(-1, $e->getMessage());
			}
		}
		$fenxiao_skus = $fenxiao_sku_model->getSkuList([ 'goods_id' => $goods_id ]);
		$skus = [];
		foreach ($fenxiao_skus['data'] as $fenxiao_sku) {
			$skus[ $fenxiao_sku['level_id'] . '_' . $fenxiao_sku['sku_id'] ] = $fenxiao_sku;
		}
		$goods_info['data']['fenxiao_skus'] = $skus;
        $goods_info['data']['goods_image'] =
            !empty($goods_info['data']['goods_image'])?explode(',', $goods_info['data']['goods_image'])[0]:'';
		$this->assign('fenxiao_level', $fenxiao_level['data']);
		$this->assign('goods_info', $goods_info['data']);
		return $this->fetch("fenxiao/config");
	}
	
	/**
	 * 修改分销状态
	 */
	public function modify()
	{
		if (request()->isAjax()) {
			$fenxiao_goods_model = new FenxiaoGoodsModel();
			$goods_id = input('goods_id');
			$is_fenxiao = input('is_fenxiao', 0);
			return $fenxiao_goods_model->modifyGoodsFenxiaoStatus($goods_id, $is_fenxiao ? 0 : 1, $this->site_id);
		}
	}
}