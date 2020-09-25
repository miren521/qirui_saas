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

use app\model\express\ExpressTemplate as ExpressTemplateModel;
use app\model\goods\GoodsExport;
use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsAttribute as GoodsAttributeModel;
use app\model\goods\GoodsBrand as GoodsBrandModel;
use app\model\goods\GoodsCategory as GoodsCategoryModel;
use app\model\goods\GoodsImport;
use app\model\goods\GoodsShopCategory as GoodsShopCategoryModel;
use app\model\goods\GoodsEvaluate as GoodsEvaluateModel;
use app\model\goods\GoodsShopCategory;
use addon\supply\model\Supplier as SupplierModel;

/**
 * 实物商品
 * Class Goods
 * @package app\shop\controller
 */
class Goods extends BaseShop
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
    public function lists()
    {
        $goods_model = new GoodsModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', "");
            $goods_state = input('goods_state', "");
            $verify_state = input('verify_state', "");
            $start_sale = input('start_sale', 0);
            $end_sale = input('end_sale', 0);
            $start_price = input('start_price', 0);
            $end_price = input('end_price', 0);
            $goods_shop_category_ids = input('goods_shop_category_ids', '');
            $goods_class = input('goods_class', "");

            $condition = [['is_delete', '=', 0], ['site_id', '=', $this->site_id]];

            if (!empty($search_text)) {
                $condition[] = ['goods_name', 'like', '%' . $search_text . '%'];
            }

            if ($goods_class !== "") {
                $condition[] = ['goods_class', '=', $goods_class];
            }

            // 上架状态
            if ($goods_state !== '') {
                $condition[] = ['goods_state', '=', $goods_state];
            }

            // 审核状态
            if ($verify_state !== '') {
                $condition[] = ['verify_state', '=', $verify_state];
            }
            if (!empty($start_sale)) $condition[] = ['sale_num', '>=', $start_sale];
            if (!empty($end_sale)) $condition[] = ['sale_num', '<=', $end_sale];
            if (!empty($start_price)) $condition[] = ['price', '>=', $start_price];
            if (!empty($end_price)) $condition[] = ['price', '<=', $end_price];
            if (!empty($goods_shop_category_ids)) $condition[] = ['goods_shop_category_ids', 'like', [$goods_shop_category_ids, '%' . $goods_shop_category_ids . ',%', '%' . $goods_shop_category_ids, '%,' . $goods_shop_category_ids . ',%'], 'or'];
            $res = $goods_model->getGoodsPageList($condition, $page_index, $page_size);
            return $res;
        } else {
            $verify_state = $goods_model->getVerifyState();
            $arr = [];
            foreach ($verify_state as $k => $v) {
                // 过滤已审核状态
                if ($k != 1) {
                    $total = $goods_model->getGoodsTotalCount([['verify_state', '=', $k], ['site_id', "=", $this->site_id]]);
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

            //获取店内分类
            $goods_shop_category_model = new GoodsShopCategoryModel();
            $goods_shop_category_list = $goods_shop_category_model->getShopCategoryTree([['site_id', "=", $this->site_id]], 'category_id,category_name,pid,level');
            $goods_shop_category_list = $goods_shop_category_list[ 'data' ];
            $this->assign("goods_shop_category_list", $goods_shop_category_list);
            return $this->fetch("goods/lists");
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
                    $total = $goods_model->getGoodsTotalCount([['verify_state', '=', $k], ['is_delete', '=', 0], ['site_id', "=", $this->site_id]]);
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
     * 添加商品
     * @return mixed
     */
    public function addGoods()
    {
        if (request()->isAjax()) {
            $goods_model = new GoodsModel();
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
            $is_free_shipping = input("is_free_shipping", 1);// 是否免邮
            $shipping_template = input("shipping_template", 0);// 指定运费模板
            $goods_spec_format = input("goods_spec_format", "");// 商品规格格式
            $goods_attr_format = input("goods_attr_format", "");// 商品属性格式
            $introduction = input("introduction", "");// 促销语
            $keywords = input("keywords", "");// 关键词
            $unit = input("unit", "");// 单位
            $sort = input("sort", 0);// 排序
            $video_url = input("video_url", "");// 视频
            $goods_sku_data = input("goods_sku_data", "");// SKU商品数据
            $supplier_id = input("supplier_id", "");// 供应商id

            $max_buy = input("max_buy", 0);// 限购
            $min_buy = input("min_buy", 0);// 起售

            //单规格需要
            $price = input("price", 0);// 商品价格（取第一个sku）
            $market_price = input("market_price", 0);// 市场价格（取第一个sku）
            $cost_price = input("cost_price", 0);// 成本价（取第一个sku）
            $sku_no = input("sku_no", "");// 商品sku编码
            $weight = input("weight", "");// 重量
            $volume = input("volume", "");// 体积

            $data = [
                'goods_name' => $goods_name,
                'goods_attr_class' => $goods_attr_class,
                'goods_attr_name' => $goods_attr_name,
                'site_id' => $this->site_id,
                'website_id' => $this->website_id,
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
                'weight' => $weight,
                'volume' => $volume,
                'goods_stock' => $goods_stock,
                'goods_stock_alarm' => $goods_stock_alarm,
                'is_free_shipping' => $is_free_shipping,
                'shipping_template' => $shipping_template,
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
            $res = $goods_model->addGoods($data);
            return $res;
        } else {

            //获取一级商品分类
            $goods_category_model = new GoodsCategoryModel();
            $condition = [
                ['pid', '=', 0]
            ];

            $goods_category_list = $goods_category_model->getCategoryList($condition, 'category_id,category_name,level,commission_rate');
            $goods_category_list = $goods_category_list[ 'data' ];
            $this->assign("goods_category_list", $goods_category_list);

            //获取品牌;
            $goods_brand_model = new GoodsBrandModel();
            $brand_list = $goods_brand_model->getBrandList([['site_id', 'in', ("0,$this->site_id")]], "brand_id, brand_name");
            $brand_list = $brand_list[ 'data' ];
            $this->assign("brand_list", $brand_list);

            //获取店内分类
            $goods_shop_category_model = new GoodsShopCategoryModel();
            $goods_shop_category_list = $goods_shop_category_model->getShopCategoryTree([['site_id', "=", $this->site_id]], 'category_id,category_name,pid,level');
            $goods_shop_category_list = $goods_shop_category_list[ 'data' ];
            $this->assign("goods_shop_category_list", $goods_shop_category_list);

            //获取运费模板
            $express_template_model = new ExpressTemplateModel();
            $express_template_list = $express_template_model->getExpressTemplateList([['site_id', "=", $this->site_id]], 'template_id,template_name', 'is_default desc');
            $express_template_list = $express_template_list[ 'data' ];
            $this->assign("express_template_list", $express_template_list);

            //获取商品类型
            $goods_attr_model = new GoodsAttributeModel();
            $attr_class_list = $goods_attr_model->getAttrClassList([['site_id', 'in', ("0,$this->site_id")]], 'class_id,class_name');
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

            return $this->fetch("goods/add_goods");
        }
    }

    /**
     * 编辑商品
     * @return mixed
     */
    public function editGoods()
    {
        $goods_model = new GoodsModel();
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
            $is_free_shipping = input("is_free_shipping", 1);// 是否免邮
            $shipping_template = input("shipping_template", 0);// 指定运费模板
            $goods_spec_format = input("goods_spec_format", "");// 商品规格格式
            $goods_attr_format = input("goods_attr_format", "");// 商品属性格式
            $introduction = input("introduction", "");// 促销语
            $keywords = input("keywords", "");// 关键词
            $unit = input("unit", "");// 单位
            $sort = input("sort", 0);// 排序
            $video_url = input("video_url", "");// 视频
            $goods_sku_data = input("goods_sku_data", "");// SKU商品数据
            $supplier_id = input("supplier_id", "");// 供应商id

            $max_buy = input("max_buy", 0);// 限购
            $min_buy = input("min_buy", 0);// 起售

            //单规格需要
            $price = input("price", 0);// 商品价格（取第一个sku）
            $market_price = input("market_price", 0);// 市场价格（取第一个sku）
            $cost_price = input("cost_price", 0);// 成本价（取第一个sku）
            $sku_no = input("sku_no", "");// 商品sku编码
            $weight = input("weight", "");// 重量
            $volume = input("volume", "");// 体积

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
                'weight' => $weight,
                'volume' => $volume,
                'goods_stock' => $goods_stock,
                'goods_stock_alarm' => $goods_stock_alarm,
                'is_free_shipping' => $is_free_shipping,
                'shipping_template' => $shipping_template,
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
            $res = $goods_model->editGoods($data);
            return $res;
        } else {

            $goods_id = input("goods_id", 0);
            $goods_info = $goods_model->getGoodsInfo([['goods_id', '=', $goods_id], ['site_id', '=', $this->site_id]]);
            $goods_info = $goods_info[ 'data' ];

            $goods_sku_list = $goods_model->getGoodsSkuList([['goods_id', '=', $goods_id], ['site_id', '=', $this->site_id]], "sku_id,sku_name,sku_no,sku_spec_format,price,market_price,cost_price,stock,weight,volume,sku_image,sku_images,goods_spec_format,spec_name", '');
            $goods_sku_list = $goods_sku_list[ 'data' ];
            $goods_info[ 'sku_list' ] = $goods_sku_list;
            $this->assign("goods_info", $goods_info);

            //获取一级商品分类
            $goods_category_model = new GoodsCategoryModel();
            $condition = [
                ['pid', '=', 0]
            ];

            $goods_category_list = $goods_category_model->getCategoryList($condition, 'category_id,category_name,level,commission_rate');
            $goods_category_list = $goods_category_list[ 'data' ];
            $this->assign("goods_category_list", $goods_category_list);

            //获取品牌;
            $goods_brand_model = new GoodsBrandModel();
            $brand_list = $goods_brand_model->getBrandList([['site_id', 'in', ("0,$this->site_id")]], "brand_id, brand_name");
            $brand_list = $brand_list[ 'data' ];
            $this->assign("brand_list", $brand_list);

            //获取店内分类
            $goods_shop_category_model = new GoodsShopCategoryModel();
            $goods_shop_category_list = $goods_shop_category_model->getShopCategoryTree([['site_id', "=", $this->site_id]], 'category_id,category_name,pid,level');
            $goods_shop_category_list = $goods_shop_category_list[ 'data' ];
            $this->assign("goods_shop_category_list", $goods_shop_category_list);

            //获取运费模板
            $express_template_model = new ExpressTemplateModel();
            $express_template_list = $express_template_model->getExpressTemplateList([['site_id', "=", $this->site_id]], 'template_id,template_name', 'is_default desc');
            $express_template_list = $express_template_list[ 'data' ];
            $this->assign("express_template_list", $express_template_list);

            //获取商品类型
            $goods_attr_model = new GoodsAttributeModel();
            $attr_class_list = $goods_attr_model->getAttrClassList([['site_id', 'in', ("0,$this->site_id")]], 'class_id,class_name');
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

            return $this->fetch("goods/edit_goods");
        }
    }

    /**
     * 删除商品
     */
    public function deleteGoods()
    {
        if (request()->isAjax()) {
            $goods_ids = input("goods_ids", 0);
            $goods_model = new GoodsModel();
            $res = $goods_model->modifyIsDelete($goods_ids, 1, $this->site_id);
            return $res;
        }

    }

    /**
     * 商品回收站
     */
    public function recycle()
    {
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_keys = input('search_keys', "");
            $condition = [['is_delete', '=', 1], ['site_id', "=", $this->site_id]];
            if (!empty($search_keys)) {
                $condition[] = ['goods_name', 'like', '%' . $search_keys . '%'];
            }
            $goods_model = new GoodsModel();
            $res = $goods_model->getGoodsPageList($condition, $page_index, $page_size);
            return $res;
        } else {
            return $this->fetch("goods/recycle");
        }
    }

    /**
     * 商品回收站商品删除
     */
    public function deleteRecycleGoods()
    {
        if (request()->isAjax()) {
            $goods_ids = input("goods_ids", 0);
            $goods_model = new GoodsModel();
            $res = $goods_model->deleteRecycleGoods($goods_ids, $this->site_id);
            return $res;
        }
    }

    /**
     * 商品回收站商品恢复
     */
    public function recoveryRecycle()
    {
        if (request()->isAjax()) {
            $goods_ids = input("goods_ids", 0);
            $goods_model = new GoodsModel();
            $res = $goods_model->modifyIsDelete($goods_ids, 0, $this->site_id);
            return $res;
        }

    }

    /**
     * 商品下架
     */
    public function offGoods()
    {
        if (request()->isAjax()) {
            $goods_ids = input("goods_ids", 0);
            $goods_state = input("goods_state", 0);
            $goods_model = new GoodsModel();
            $res = $goods_model->modifyGoodsState($goods_ids, $goods_state, $this->site_id);
            return $res;
        }

    }

    /**
     * 商品上架
     */
    public function onGoods()
    {
        if (request()->isAjax()) {
            $goods_ids = input("goods_ids", 0);
            $goods_state = input("goods_state", 0);
            $goods_model = new GoodsModel();
            $res = $goods_model->modifyGoodsState($goods_ids, $goods_state, $this->site_id);
            return $res;
        }
    }

    /**
     * 编辑商品库存
     * @return multitype:number unknown
     */
    public function editGoodsStock()
    {
        if (request()->isAjax()) {
            $sku_list = input("sku_list", '');
            $model = new GoodsModel;
            $res = $model->editGoodsStock($sku_list);
            return $res;
        }
    }

    /**
     * 获取商品分类列表
     * @return \multitype
     */
    public function getCategoryList()
    {
        if (request()->isAjax()) {
            $category_id = input("category_id", 0);
            $goods_category_model = new GoodsCategoryModel();
            $condition = [
                ['pid', '=', $category_id]
            ];

            $goods_category_list = $goods_category_model->getCategoryList($condition, 'category_id,category_name,level,commission_rate');
            return $goods_category_list;
        }
    }

    /**
     * 获取商品规格列表
     * @return \multitype
     */
    public function getSpecList()
    {
        if (request()->isAjax()) {

            $attr_id = input("attr_id", "");//排除已存在的规格项
            $search_text = input("search_text", "");
            $condition = [['is_spec', '=', 1], ['site_id', 'in', ("0,$this->site_id")]];
            if (!empty($attr_id)) {
                $condition[] = ['attr_id', 'not in', $attr_id];
            }
            if (!empty($search_text)) {
                $condition[] = ['attr_name', 'like', '%' . $search_text . '%'];
            }
            $goods_attr_model = new GoodsAttributeModel();
            $spec_list = $goods_attr_model->getSpecList($condition, 'attr_id,attr_name,attr_class_name', 'attr_id desc', 50);
            return $spec_list;
        }
    }

    public function getSupplierPageList()
    {
        if (request()->isAjax()) {
            $is_install_supply = addon_is_exit("supply");
            if ($is_install_supply) {
                $supplier_model = new SupplierModel();
                $page_index = input('page_index', 1);
                $page_size = input('page_size', PAGE_LIST_ROWS);
                $search_text = input('search_text', '');

                $condition = [];
                if (!empty($search_text)) {
                    $condition[] = ['title|desc|keywords|supplier_phone', 'LIKE', "%{$search_text}%"];
                }
                $res = $supplier_model->getSupplierPageList($condition, $page_index, $page_size, 'supplier_id desc', 'supplier_id,title');
                return $res;
            }
        }
    }

    /**
     * 获取商品规格值列表
     * @return \multitype
     */
    public function getSpecValueList()
    {
        if (request()->isAjax()) {

            $attr_id = input("attr_id", 0);
            $search_text = input("search_text", "");
            $condition = [];
            if (!empty($attr_id)) {
                $condition[] = ['attr_id', '=', $attr_id];
            }
            if (!empty($search_text)) {
                $condition[] = ['attr_value_name', 'like', '%' . $search_text . '%'];
            }

            $goods_attr_model = new GoodsAttributeModel();
            $spec_list = $goods_attr_model->getSpecValueList($condition, 'attr_value_id,attr_value_name');
            return $spec_list;
        }
    }

    /**
     * 获取商品属性列表
     * @return \multitype
     */
    public function getAttributeList()
    {

        if (request()->isAjax()) {
            $goods_attr_model = new GoodsAttributeModel();
            $attr_class_id = input('attr_class_id', 0);// 商品类型id
            $attribute_list = $goods_attr_model->getAttributeList([['attr_class_id', '=', $attr_class_id], ['is_spec', '=', 0], ['site_id', 'in', ("0,$this->site_id")]], 'attr_id,attr_name,attr_class_id,attr_class_name,attr_type,attr_value_format');
            if (!empty($attribute_list[ 'data' ])) {
                foreach ($attribute_list[ 'data' ] as $k => $v) {
                    if (!empty($v[ 'attr_value_format' ])) {
                        $attribute_list[ 'data' ][ $k ][ 'attr_value_format' ] = json_decode($v[ 'attr_value_format' ], true);
                    }
                }
            }

            return $attribute_list;
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
            $res = $goods_model->getGoodsSkuList([['goods_id', '=', $goods_id], ['site_id', '=', $this->site_id]], 'sku_id,sku_name,price,market_price,cost_price,stock,weight,volume,sku_no,sale_num,sku_image,spec_name,goods_id');
            return $res;
        }
    }

    /**
     * 获取违规下架原因
     * @return \multitype
     */
    public function getVerifyStateRemark()
    {
        if (request()->isAjax()) {
            $goods_id = input("goods_id", 0);
            $goods_model = new GoodsModel();
            $res = $goods_model->getGoodsInfo([['goods_id', '=', $goods_id], ['verify_state', 'in', [-2, 10]], ['site_id', '=', $this->site_id]], 'verify_state_remark');
            return $res;
        }
    }

    /**
     * 商品选择组件
     * @return \multitype
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
                $goods_list = event('GoodsListPromotion', ['page' => $page, 'page_size' => $page_size, 'site_id' => $this->site_id, 'promotion' => $promotion, 'pintuan_name' => $pintuan_name, 'goods_name' => $goods_name], true);
            } else {
                $condition = [
                    ['is_delete', '=', 0],
                    ['goods_state', '=', 1],
                    ['site_id', '=', $this->site_id]
                ];


                if (!empty($goods_name)) {
                    $condition[] = ['goods_name', 'like', '%' . $goods_name . '%'];
                }
                if ($is_virtual !== "") {
                    $condition[] = ['is_virtual', '=', $is_virtual];
                }
                if (!empty($goods_id)) {
                    $condition[] = ['goods_id', '=', $goods_id];
                }
                if (!empty($category_id)) {
                    $condition[] = ['category_id', 'like', [$category_id, '%' . $category_id . ',%', '%' . $category_id, '%,' . $category_id . ',%'], 'or'];
                }

                if (!empty($promotion_type)) {
                    $condition[] = ['promotion_addon', 'like', "%{$promotion_type}%"];
                }


                if ($goods_class !== "") {
                    $condition[] = ['goods_class', '=', $goods_class];
                }

                if ($min_price != "" && $max_price != "") {
                    $condition[] = ['price', 'between', [$min_price, $max_price]];
                } elseif ($min_price != "") {
                    $condition[] = ['price', '<=', $min_price];
                } elseif ($max_price != "") {
                    $condition[] = ['price', '>=', $max_price];
                }

                $order = 'create_time desc';
                $goods_model = new GoodsModel();
                $field = 'goods_id,goods_name,goods_class_name,goods_image,price,goods_stock,create_time,is_virtual';
                $goods_list = $goods_model->getGoodsPageList($condition, $page, $page_size, $order, $field);

                if (!empty($goods_list[ 'data' ][ 'list' ])) {
                    foreach ($goods_list[ 'data' ][ 'list' ] as $k => $v) {
                        $goods_sku_list = $goods_model->getGoodsSkuList([['goods_id', '=', $v[ 'goods_id' ]], ['site_id', '=', $this->site_id]], 'sku_id,sku_name,price,stock,sku_image,goods_id,goods_class_name');
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
                ['pid', '=', 0],
                ['level', '=', 1],
            ];
            $list = $goods_category_list = $goods_category_model->getCategoryByParent($condition, $field);
            $list = $list[ 'data' ];
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    $two_list = $goods_category_list = $goods_category_model->getCategoryByParent(
                        [
                            ['pid', '=', $v[ 'category_id' ]],
                            ['level', '=', 2],
                        ],
                        $field
                    );

                    $two_list = $two_list[ 'data' ];
                    if (!empty($two_list)) {

                        foreach ($two_list as $two_k => $two_v) {
                            $three_list = $goods_category_list = $goods_category_model->getCategoryByParent(
                                [
                                    ['pid', '=', $two_v[ 'category_id' ]],
                                    ['level', '=', 3],
                                ],
                                $field
                            );
                            $two_list[ $two_k ][ 'children' ] = $three_list[ 'data' ];
                        }
                    }

                    $list[ $k ][ 'children' ] = $two_list;
                }
            }

            $this->assign("category_list", $list);
            return $this->fetch("goods/goods_select");
        }
    }
    /***********************************************************商品评价**************************************************/

    /**
     * 商品评价
     */
    public function evaluate()
    {
        $goods_evaluate = new GoodsEvaluateModel();

        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $explain_type = input('explain_type', ''); //1好评2中评3差评
            $is_show = input('is_show', ''); //1显示 0隐藏
            $search_text = input('search_text', ''); //搜索值
            $search_type = input('search_type', ''); //搜索类型
            $start_time = input('start_time', '');
            $end_time = input('end_time', '');
            $condition = [
                ["site_id", "=", $this->site_id]
            ];
            //评分类型
            if ($explain_type != "") {
                $condition[] = ["explain_type", "=", $explain_type];
            }
            if ($is_show != "") {
                $condition[] = ["is_show", "=", $is_show];
            }

            if (!empty($search_text)) {
                if (!empty($search_type)) {
                    $condition[] = [$search_type, 'like', '%' . $search_text . '%'];
                } else {
                    $condition[] = ['sku_name', 'like', '%' . $search_text . '%'];
                }
            }
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ["create_time", ">=", date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }
            $list = $goods_evaluate->getEvaluatePageList($condition, $page_index, $page_size, "create_time desc");
            return $list;
        } else {
            return $this->fetch("goods/evaluate");
        }


    }

    /**
     * 商品评价删除
     */
    public function deleteEvaluate()
    {

        if (request()->isAjax()) {
            $goods_evaluate = new GoodsEvaluateModel();
            $evaluate_id = input("evaluate_id", 0);
            return $goods_evaluate->deleteEvaluate($evaluate_id);
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
        $goods_sku_info = $goods_model->getGoodsSkuInfo([['goods_id', '=', $goods_id]], 'sku_id,goods_name');
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
        $goods_sku_info = $goods_model->getGoodsSkuInfo([['goods_id', '=', $goods_id]], 'sku_id,goods_name');
        $goods_sku_info = $goods_sku_info[ 'data' ];
        $res = $goods_model->qrcode($goods_sku_info[ 'sku_id' ], $goods_sku_info[ 'goods_name' ]);
        return $res;
    }

    /**
     * 商品评价回复
     */
    public function evaluateApply()
    {
        if (request()->isAjax()) {
            $goods_evaluate = new GoodsEvaluateModel();
            $evaluate_id = input("evaluate_id", 0);
            $explain = input("explain", 0);
            $is_first_explain = input("is_first_explain", 0);// 是否第一次回复
            $data = [
                'evaluate_id' => $evaluate_id
            ];
            if ($is_first_explain == 0) {
                $data[ 'explain_first' ] = $explain;
            } elseif ($is_first_explain == 1) {
                $data[ 'again_explain' ] = $explain;
            }

            return $goods_evaluate->evaluateApply($data);
        }
    }

    /**
     * 商品复制
     * @return array
     */
    public function copyGoods(){
        if (request()->isAjax()) {
            $goods_id = input('goods_id', 0);
            $goods_model = new GoodsModel();
            $result = $goods_model->copyGoods([['goods_id', '=', $goods_id], ['site_id', '=', $this->site_id]]);
            return $result;
        }
    }

    /**
     * 商品导出
     */
    public function export(){
        $export_model = new GoodsExport();
        if (request()->isAjax()) {

            $condition = array(
                ['site_id', '=', $this->site_id]
            );
            $result = $export_model->getExport($condition, '*', 'create_time desc');
            return $result;
        }else{


            return $this->fetch("goods/export");
        }
    }

    /**
     * 导出商品操作
     */
    public function exportGoods(){
        $export_model = new GoodsExport();

        $search_text = input('search_text', "");
        $goods_state = input('goods_state', "");
        $verify_state = input('verify_state', "");
        $start_sale = input('start_sale', 0);
        $end_sale = input('end_sale', 0);
        $start_price = input('start_price', 0);
        $end_price = input('end_price', 0);
        $goods_shop_category_ids = input('goods_shop_category_ids', '');
        $goods_class = input('goods_class', "");

        $condition = [['is_delete', '=', 0], ['site_id', '=', $this->site_id]];

        //条件数组的陈诉
        $condition_desc = [];
        if (!empty($search_text)) {
            $condition[] = ['goods_name', 'like', '%' . $search_text . '%'];
        }
        $condition_desc[] = ['name' => '商品名称', 'value' => $search_text];
        $goods_class_value = '';
        if ($goods_class !== "") {
            $condition[] = ['goods_class', '=', $goods_class];
            $goods_class_array = ['1' => '实物商品', '2' => '虚拟商品3', '3' => '卡券商品'];
            $goods_class_value = $goods_class_array[$goods_class];
        }
        $condition_desc[] = ['name' => '商品类型', 'value' => $goods_class_value];

        // 上架状态
        $goods_state_value = '';
        if ($goods_state !== '') {
            $condition[] = ['goods_state', '=', $goods_state];
            $goods_state_array = [1=>'正常', 0 => '下架'];
            $goods_state_value = $goods_state_array[$goods_state];
        }
        $condition_desc[] = ['name' => '商品状态', 'value' => $goods_state_value];

        // 审核状态
        $verify_state_value = '';
        if ($verify_state !== '') {
            $condition[] = ['verify_state', '=', $verify_state];
            $verify_state_array =  [1=>'已审核', 0 => '待审核', 10 => '违规下架', -1 => '审核中', -2 => '审核失败'];
            $verify_state_value = $verify_state_array[$verify_state];
        }
        $condition_desc[] = ['name' => '审核状态', 'value' => $verify_state_value];

        if (!empty($start_sale)){
            $condition[] = ['sale_num', '>=', $start_sale];
            $condition_desc[] = ['name' => '销量', 'value' => "≥".$start_sale];
        }
        if (!empty($end_sale)){
            $condition[] = ['sale_num', '<=', $end_sale];
            $condition_desc[] = ['name' => '销量', 'value' => "≤".$start_sale];
        }
        if (!empty($start_price)){
            $condition[] = ['price', '>=', $start_price];
            $condition_desc[] = ['name' => '价格', 'value' => "≥".$start_price];
        }
        if (!empty($end_price)){
            $condition[] = ['price', '<=', $end_price];
            $condition_desc[] = ['name' => '价格', 'value' => "≤".$end_price];
        }
        if (!empty($goods_shop_category_ids)){
            $condition[] = ['goods_shop_category_ids', 'like', [$goods_shop_category_ids, '%' . $goods_shop_category_ids . ',%', '%' . $goods_shop_category_ids, '%,' . $goods_shop_category_ids . ',%'], 'or'];

            $goods_shop_category_model = new GoodsShopCategory();
            $goods_shop_category_list = $goods_shop_category_model->getShopCategoryList([['site_id', '=', $this->site_id], ['category_id', 'in', $goods_shop_category_ids]])['data'] ?? [];
            $goods_shop_category_ids = array_column($goods_shop_category_list, 'category_name');
            $condition_desc[] = ['name' => '店内分类', 'value' => implode(',', $goods_shop_category_ids)];
        }


        $result = $export_model->exportData($condition, $condition_desc);
        return $result;
//        $this->redirect(addon_url('shop/goods/export'));

    }

    /**
     * 导入商品数据
     */
    public function import()
    {
        $import_model = new GoodsImport();
        if (request()->isAjax()) {
            $file = request()->file('csv');
            if (empty($file)) {
                return $import_model->error();
            }

            $tmp_name = $file->getPathname();//获取上传缓存文件
            $csv_list = readCsv($tmp_name);
            if (empty($csv_list)) {
                return $import_model->error();
            }

            $first_list = $csv_list[0];
            //分析第一列数据
            foreach ($first_list as $k => $v) {
                $$k = $v;
            }
            $table_head = $csv_list[1];
            $table_line = $csv_list[0];
            unset($csv_list[0]);
            unset($csv_list[1]);
            $last_list = $csv_list;
            $list      = [];
            foreach ($last_list as $last_k => $last_v) {
                $item_list = [];
                foreach ($last_v as $item_k => $item_v) {
                    $item_list[$$item_k] = trim($item_v);
                }
                $list[] = $item_list;
            }
            $result = $import_model->importGoods($list, $table_head, $table_line, $this->site_id);
            return $result;
        } else {
            return $this->fetch("goods/import");
        }
    }

    /**
     * 导出错误数据
     */
    public function exportError(){
        $import_model = new GoodsImport();
        $key = input('key', '');
        $import_model->exportError($key);
    }
}
