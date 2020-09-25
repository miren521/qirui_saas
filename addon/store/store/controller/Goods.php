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


namespace addon\store\store\controller;

use addon\store\model\StoreGoods as StoreGoodsModel;
use addon\store\model\StoreGoodsSku as StoreGoodsSkuModel;
use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;
use app\model\goods\GoodsShopCategory;
use think\facade\Db;

/**
 * 实物商品
 * Class Goods
 * @package app\shop\controller
 */
class Goods extends BaseStore
{
	
	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
	}
	
	/**
	 * 商品列表
	 * @return mixed
	 */
	public function index()
	{
		if (request()->isAjax()) {
			$page_index = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', "");
			$start_sale = input('start_sale', 0);
			$end_sale = input('end_sale', 0);
			$goods_shop_category_ids = input('goods_shop_category_ids', '');
			$goods_class = input('goods_class', "");
			$condition = [
			    [ 'is_delete', '=', 0 ],
                      [ 'site_id', '=', $this->site_id ],
                      ['verify_state', '=', 1]];
			if (!empty($search_text)) {
				$condition[] = [ 'g.goods_name', 'like', '%' . $search_text . '%' ];
			}
			
			if ($goods_class !== "") {
				$condition[] = [ 'g.goods_class', '=', $goods_class ];
			}


			if (!empty($start_sale)) $condition[] = [ 'sg.store_sale_num', '>=', $start_sale ];
			if (!empty($end_sale)) $condition[] = [ 'sg.store_sale_num', '<=', $end_sale ];
			if (!empty($goods_shop_category_ids)) $condition[] = [ 'g.goods_shop_category_ids', 'like', [ $goods_shop_category_ids, '%' . $goods_shop_category_ids . ',%', '%' . $goods_shop_category_ids, '%,' . $goods_shop_category_ids . ',%' ], 'or' ];
			$store_goods_model = new StoreGoodsModel();
			$res = $store_goods_model->getGoodsPageList($condition, $page_index, $page_size, $this->store_id);
			return $res;
		} else {
			//获取店内分类
			$goods_shop_category_model = new GoodsShopCategoryModel();
			$goods_shop_category_list = $goods_shop_category_model->getShopCategoryTree([ [ 'site_id', "=", $this->site_id ] ], 'category_id,category_name,pid,level');
			$goods_shop_category_list = $goods_shop_category_list['data'];
			$this->assign("goods_shop_category_list", $goods_shop_category_list);
			return $this->fetch("goods/lists", [], $this->replace);
		}
	}

    /**
     * 更新门店的库存
     */
	public function saveStock() {
        $stocks = input('stock', []);
        $sku_ids = input('sku_id', []);
        $goods_ids = input('goods_id', []);
        $store_goods_sku_array = [];
        foreach ($stocks as $key => $stock) {
            $store_goods_sku_array[] = [
                'store_stock' => $stock,
                'store_id' => $this->store_id,
                'sku_id' => $sku_ids[$key],
                'goods_id' => $goods_ids[$key]
            ];
        }
        $store_goods_sku_model = new StoreGoodsSkuModel();
        $res = $store_goods_sku_model->editStock($store_goods_sku_array);

        return $res;
    }

	
	/**
	 * 获取SKU商品列表
	 * @return \multitype
	 */
	public function getGoodsSkuList()
	{
		if (request()->isAjax()) {
			$goods_id = input("goods_id", 0);
			$store_goods_sku_model = new StoreGoodsSkuModel();
			$res = $store_goods_sku_model->getGoodsSkuList([ [ 'gs.goods_id', '=', $goods_id ], [ 'gs.site_id', '=', $this->site_id ] ], $this->store_id);
			return $res;
		}
	}
}