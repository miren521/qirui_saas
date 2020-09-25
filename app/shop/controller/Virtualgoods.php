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


namespace app\shop\controller;

use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\goods\GoodsBrand as GoodsBrandModel;
use app\model\goods\GoodsAttribute as GoodsAttributeModel;
use app\model\goods\VirtualGoods as VirtualGoodsModel;
use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;
use addon\supply\model\Supplier as SupplierModel;

/**
 * 虚拟商品
 * Class Virtualgoods
 * @package app\shop\controller
 */
class Virtualgoods extends BaseShop
{

	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
	}


	/**
	 * 添加商品
	 * @return mixed
	 */
	public function addGoods()
	{
		if (request()->isAjax()) {
			$goods_name = input("goods_name", "");// 商品名称
			$goods_attr_class = input("goods_attr_class", "");// 商品类型id
			$goods_attr_name = input("goods_attr_name", "");// 商品类型名称
			$category_id = input("category_id", 0);// 分类id
			$category_id_1 = input("category_id_1", 0);// 一级分类id
			$category_id_2 = input("category_id_2", 0);// 二级分类id
			$category_id_3 = input("category_id_3", 0);// 三级分类id
			$category_name = input("category_name", "");// 所属分类名称
			$commission_rate = input("commission_rate", 0);// 分佣比率(按照分类)
			$brand_id = input("brand_id", 0);// 品牌id
			$brand_name = input("brand_name", "");// 所属品牌名称
			$goods_shop_category_ids = input("goods_shop_category_ids", "");// 店内分类id,逗号隔开
			$goods_image = input("goods_image", "");// 商品主图路径
			$goods_content = input("goods_content", "");// 商品详情
			$goods_state = input("goods_state", "");// 商品状态（1.正常0下架）
			$goods_stock = input("goods_stock", 0);// 商品库存（总和）
			$goods_stock_alarm = input("goods_stock_alarm", 0);// 库存预警
			$goods_spec_format = input("goods_spec_format", "");// 商品规格格式
			$goods_attr_format = input("goods_attr_format", "");// 商品属性格式
			$introduction = input("introduction", "");// 促销语
			$keywords = input("keywords", "");// 关键词
			$unit = input("unit", "");// 单位
			$sort = input("sort", 0);// 排序
			$video_url = input("video_url", "");// 视频
			$goods_sku_data = input("goods_sku_data", "");// SKU商品数据
			$virtual_indate = input("virtual_indate", "");// 虚拟商品有效期
			$supplier_id = input("supplier_id", "");// 供应商id

            $max_buy = input("max_buy", 0);// 限购
            $min_buy = input("min_buy", 0);// 起售

			//单规格需要
			$price = input("price", 0);// 商品价格（取第一个sku）
			$market_price = input("market_price", 0);// 市场价格（取第一个sku）
			$cost_price = input("cost_price", 0);// 成本价（取第一个sku）
			$sku_no = input("sku_no", "");// 商品sku编码

			$data = [
				'goods_name' => $goods_name,
				'goods_attr_class' => $goods_attr_class,
				'goods_attr_name' => $goods_attr_name,
				'site_id' => $this->site_id,
				'category_id' => $category_id,
				'category_id_1' => $category_id_1,
				'category_id_2' => $category_id_2,
				'category_id_3' => $category_id_3,
				'category_name' => $category_name,
				'brand_id' => $brand_id,
				'brand_name' => $brand_name,
				'goods_image' => $goods_image,
				'goods_content' => $goods_content,
				'goods_state' => $goods_state,
				'price' => $price,
				'market_price' => $market_price,
				'cost_price' => $cost_price,
				'sku_no' => $sku_no,
				'virtual_indate' => $virtual_indate,
				'goods_stock' => $goods_stock,
				'goods_stock_alarm' => $goods_stock_alarm,
				'goods_spec_format' => $goods_spec_format,
				'goods_attr_format' => $goods_attr_format,
				'introduction' => $introduction,
				'keywords' => $keywords,
				'unit' => $unit,
				'sort' => $sort,
				'commission_rate' => $commission_rate,
				'video_url' => $video_url,
				'goods_sku_data' => $goods_sku_data,
				'goods_shop_category_ids' => $goods_shop_category_ids,
				'supplier_id' => $supplier_id,

                'max_buy' => $max_buy,
                'min_buy' => $min_buy
			];
			$virtual_goods_model = new VirtualGoodsModel();
			$res = $virtual_goods_model->addGoods($data);
			return $res;
		} else {

			//获取一级商品分类
			$goods_category_model = new GoodsCategoryModel();
			$condition = [
				[ 'pid', '=', 0 ]
			];

			$goods_category_list = $goods_category_model->getCategoryList($condition, 'category_id,category_name,level,commission_rate');
			$goods_category_list = $goods_category_list[ 'data' ];
			$this->assign("goods_category_list", $goods_category_list);

			//获取品牌;
			$goods_brand_model = new GoodsBrandModel();
			$brand_list = $goods_brand_model->getBrandList([ [ 'site_id', 'in', ( "0,$this->site_id" ) ] ], "brand_id, brand_name");
			$brand_list = $brand_list[ 'data' ];
			$this->assign("brand_list", $brand_list);

			//获取店内分类
			$goods_shop_category_model = new GoodsShopCategoryModel();
			$goods_shop_category_list = $goods_shop_category_model->getShopCategoryTree([ [ 'site_id', '=', $this->site_id ] ], 'category_id,category_name,pid,level');
			$goods_shop_category_list = $goods_shop_category_list[ 'data' ];
			$this->assign("goods_shop_category_list", $goods_shop_category_list);

			//获取商品类型
			$goods_attr_model = new GoodsAttributeModel();
			$attr_class_list = $goods_attr_model->getAttrClassList([ [ 'site_id', 'in', ( "0,$this->site_id" ) ] ], 'class_id,class_name');
			$attr_class_list = $attr_class_list[ 'data' ];
			$this->assign("attr_class_list", $attr_class_list);

			$is_install_supply = addon_is_exit("supply");
			if ($is_install_supply) {
				$supplier_model = new SupplierModel();
				$supplier_list = $supplier_model->getSupplierPageList([], 1, PAGE_LIST_ROWS, 'supplier_id DESC');
				$supplier_list = $supplier_list[ 'data' ][ 'list' ];
				$this->assign("supplier_list", $supplier_list);
			}
			$this->assign("is_install_supply", $is_install_supply);

			return $this->fetch("virtualgoods/add_goods");
		}
	}

	/**
	 * 编辑商品
	 * @return mixed
	 */
	public function editGoods()
	{
		$virtual_goods_model = new VirtualGoodsModel();
		if (request()->isAjax()) {
			$goods_id = input("goods_id", 0);// 商品id
			$goods_name = input("goods_name", "");// 商品名称
			$goods_attr_class = input("goods_attr_class", "");// 商品类型id
			$goods_attr_name = input("goods_attr_name", "");// 商品类型名称
			$category_id = input("category_id", 0);// 分类id
			$category_id_1 = input("category_id_1", 0);// 一级分类id
			$category_id_2 = input("category_id_2", 0);// 二级分类id
			$category_id_3 = input("category_id_3", 0);// 三级分类id
			$category_name = input("category_name", "");// 所属分类名称
			$commission_rate = input("commission_rate", 0);// 分佣比率(按照分类)
			$brand_id = input("brand_id", 0);// 品牌id
			$brand_name = input("brand_name", "");// 所属品牌名称
			$goods_shop_category_ids = input("goods_shop_category_ids", "");// 店内分类id,逗号隔开
			$goods_image = input("goods_image", "");// 商品主图路径
			$goods_content = input("goods_content", "");// 商品详情
			$goods_state = input("goods_state", "");// 商品状态（1.正常0下架）
			$goods_stock = input("goods_stock", 0);// 商品库存（总和）
			$goods_stock_alarm = input("goods_stock_alarm", 0);// 库存预警
			$goods_spec_format = input("goods_spec_format", "");// 商品规格格式
			$goods_attr_format = input("goods_attr_format", "");// 商品属性格式
			$introduction = input("introduction", "");// 促销语
			$keywords = input("keywords", "");// 关键词
			$unit = input("unit", "");// 单位
			$sort = input("sort", 0);// 排序
			$video_url = input("video_url", "");// 视频
			$goods_sku_data = input("goods_sku_data", "");// SKU商品数据
			$virtual_indate = input("virtual_indate", "");// 虚拟商品有效期
			$supplier_id = input("supplier_id", "");// 供应商id

            $max_buy = input("max_buy", 0);// 限购
            $min_buy = input("min_buy", 0);// 起售

			//单规格需要
			$price = input("price", 0);// 商品价格（取第一个sku）
			$market_price = input("market_price", 0);// 市场价格（取第一个sku）
			$cost_price = input("cost_price", 0);// 成本价（取第一个sku）
			$sku_no = input("sku_no", "");// 商品sku编码

			$data = [
				'goods_id' => $goods_id,
				'goods_name' => $goods_name,
				'goods_attr_class' => $goods_attr_class,
				'goods_attr_name' => $goods_attr_name,
				'site_id' => $this->site_id,
				'category_id' => $category_id,
				'category_id_1' => $category_id_1,
				'category_id_2' => $category_id_2,
				'category_id_3' => $category_id_3,
				'category_name' => $category_name,
				'brand_id' => $brand_id,
				'brand_name' => $brand_name,
				'goods_image' => $goods_image,
				'goods_content' => $goods_content,
				'goods_state' => $goods_state,
				'price' => $price,
				'market_price' => $market_price,
				'cost_price' => $cost_price,
				'sku_no' => $sku_no,
				'virtual_indate' => $virtual_indate,
				'goods_stock' => $goods_stock,
				'goods_stock_alarm' => $goods_stock_alarm,
				'goods_spec_format' => $goods_spec_format,
				'goods_attr_format' => $goods_attr_format,
				'introduction' => $introduction,
				'keywords' => $keywords,
				'unit' => $unit,
				'sort' => $sort,
				'commission_rate' => $commission_rate,
				'video_url' => $video_url,
				'goods_sku_data' => $goods_sku_data,
				'goods_shop_category_ids' => $goods_shop_category_ids,
				'supplier_id' => $supplier_id,

                'max_buy' => $max_buy,
                'min_buy' => $min_buy
			];
			$res = $virtual_goods_model->editGoods($data);
			return $res;
		} else {

			$goods_id = input("goods_id", 0);
			$goods_info = $virtual_goods_model->getGoodsInfo([ [ 'goods_id', '=', $goods_id ], [ 'site_id', '=', $this->site_id ] ]);
			$goods_info = $goods_info[ 'data' ];
			$goods_sku_list = $virtual_goods_model->getGoodsSkuList([ [ 'goods_id', '=', $goods_id ], [ 'site_id', '=', $this->site_id ] ], "sku_id,sku_name,sku_no,sku_spec_format,price,market_price,cost_price,stock,virtual_indate,sku_image,sku_images,goods_spec_format,spec_name", '');
			$goods_sku_list = $goods_sku_list[ 'data' ];
			$goods_info[ 'sku_list' ] = $goods_sku_list;
			$this->assign("goods_info", $goods_info);

			//获取一级商品分类
			$goods_category_model = new GoodsCategoryModel();
			$condition = [
				[ 'pid', '=', 0 ]
			];

			$goods_category_list = $goods_category_model->getCategoryList($condition, 'category_id,category_name,level,commission_rate');
			$goods_category_list = $goods_category_list[ 'data' ];
			$this->assign("goods_category_list", $goods_category_list);

			//获取品牌;
			$goods_brand_model = new GoodsBrandModel();
			$brand_list = $goods_brand_model->getBrandList([ [ 'site_id', 'in', ( "0,$this->site_id" ) ] ], "brand_id, brand_name");
			$brand_list = $brand_list[ 'data' ];
			$this->assign("brand_list", $brand_list);

			//获取店内分类
			$goods_shop_category_model = new GoodsShopCategoryModel();
			$goods_shop_category_list = $goods_shop_category_model->getShopCategoryTree([ [ 'site_id', '=', $this->site_id ] ], 'category_id,category_name,pid,level');
			$goods_shop_category_list = $goods_shop_category_list[ 'data' ];
			$this->assign("goods_shop_category_list", $goods_shop_category_list);

			//获取商品类型
			$goods_attr_model = new GoodsAttributeModel();
			$attr_class_list = $goods_attr_model->getAttrClassList([ [ 'site_id', 'in', ( "0,$this->site_id" ) ] ], 'class_id,class_name');
			$attr_class_list = $attr_class_list[ 'data' ];
			$this->assign("attr_class_list", $attr_class_list);

			$is_install_supply = addon_is_exit("supply");
			if ($is_install_supply) {
				$supplier_model = new SupplierModel();
				$supplier_list = $supplier_model->getSupplierPageList([], 1, PAGE_LIST_ROWS, 'supplier_id desc', 'supplier_id,title');
				$supplier_list = $supplier_list[ 'data' ][ 'list' ];
				$this->assign("supplier_list", $supplier_list);
			}
			$this->assign("is_install_supply", $is_install_supply);

			return $this->fetch("virtualgoods/edit_goods");
		}
	}

}