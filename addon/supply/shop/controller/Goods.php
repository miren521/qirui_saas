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


namespace addon\supply\shop\controller;

use addon\supply\model\Supplier;
use app\model\goods\GoodsAttribute;
use app\model\goods\GoodsCategory;
use app\shop\controller\BaseShop;
use addon\supply\model\goods\Goods as GoodsModel;

/**
 * 供应商信息
 * Class Order
 * @package app\shop\controller
 */
class Goods extends BaseSupplyshop
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * 商品列表
     */
    public function goodslist()
    {
        if(request()->isAjax()){
            $goods_model = new GoodsModel();

            $page             = input('page', 1);
            $page_size        = input('page_size', PAGE_LIST_ROWS);
            $site_id          = input('site_id', 0); //供应商id
            $goods_id_arr     = input('goods_id_arr', ''); //sku_id数组
            $keyword          = input('keyword', ''); //关键词
            $category_id      = input('category_id', 0); //分类
            $category_level   = input('category_level', 0); //分类等级
            $brand_id         = input('brand_id', 0); //品牌
            $min_price        = input('min_price', 0); //价格区间，小
            $max_price        = input('max_price', 0); //价格区间，大
            $is_free_shipping = input('is_free_shipping', '-1'); //是否免邮
            $is_own           = input('is_own', ''); //是否自营
            $order            = input('order', 'create_time'); //排序（综合、销量、价格）
            $sort             = input('sort', 'desc'); //升序、降序
            $attr             = input('attr', ''); //属性json
            $condition        = [];

            $field = 'gs.goods_id,gs.sku_id,gs.sku_name,gs.price_json,gs.min_price,gs.max_price,gs.min_num,
        gs.market_price,gs.stock,gs.sale_num,gs.sku_image,gs.goods_name,gs.site_id,g.site_name,gs.website_id,
        gs.is_own,gs.is_free_shipping,gs.introduction,g.goods_image,gs.goods_spec_format,gs.is_virtual';

            $alias = 'gs';
            $join  = [
                ['supply_goods g', 'gs.sku_id = g.sku_id', 'inner']
            ];

            if (!empty($site_id)) {
                $condition[] = ['gs.site_id', '=', $site_id];
            }

            if (!empty($goods_id_arr)) {
                $condition[] = ['gs.goods_id', 'in', $goods_id_arr];
            }

            if (!empty($keyword)) {
                $condition[] = ['g.goods_name|gs.sku_name|gs.keywords', 'like', '%' . $keyword . '%'];
            }

            if (!empty($category_id) && !empty($category_level)) {
                $condition[] = ['gs.category_id_' . $category_level, '=', $category_id];
            }

            if (!empty($brand_id)) {
                $condition[] = ['gs.brand_id', '=', $brand_id];
            }

            //价格区间
            if ($min_price != 0 && $max_price != 0) {
                $condition[] = ['gs.min_price', '>=', $min_price];
                $condition[] = ['gs.max_price', '<=', $max_price];
            } elseif ($min_price != 0) {
                $condition[] = ['gs.min_price', '>=', $min_price];
            } elseif ($max_price != 0) {
                $condition[] = ['gs.max_price', '<=', $max_price];
            }

            if (isset($is_free_shipping) && !empty($is_free_shipping) && $is_free_shipping > -1) {
                $condition[] = ['gs.is_free_shipping', '=', $is_free_shipping];
            }

            if ($is_own !== '') {
                $condition[] = ['gs.is_own', '=', $is_own];
            }

            // 非法参数进行过滤
            if ($sort != "desc" && $sort != "asc") {
                $sort = "";
            }

            // 非法参数进行过滤
            if ($order != '') {
                if ($order != "sale_num" && $order != "min_price") {
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
                $attr          = json_decode($attr, true);
                $attr_id       = [];
                $attr_value_id = [];
                foreach ($attr as $k => $v) {
                    $attr_id[]       = $v['attr_id'];
                    $attr_value_id[] = $v['attr_value_id'];
                }
                $goods_attribute     = new GoodsAttribute();
                $attribute_condition = [
                    ['attr_id', 'in', implode(",", $attr_id)],
                    ['attr_value_id', 'in', implode(",", $attr_value_id)],
                    ['app_module', '=', 'supply']
                ];
                $attribute_list      = $goods_attribute->getAttributeIndexList($attribute_condition, 'sku_id');
                $attribute_list      = $attribute_list['data'];
                if (!empty($attribute_list)) {
                    $sku_id = [];
                    foreach ($attribute_list as $k => $v) {
                        $sku_id[] = $v['sku_id'];
                    }
                    $condition[] = [
                        ['gs.sku_id', 'in', implode(",", $sku_id)]
                    ];
                }
            }

            $condition[] = ['gs.goods_state', '=', 1];
            $condition[] = ['gs.verify_state', '=', 1];
            $condition[] = ['gs.is_delete', '=', 0];


            //只查看处于开启状态的供应商
            $join[] = [ 'supplier s', 's.supplier_site_id = gs.site_id', 'inner'];
            $condition[] = ['s.status', '=', 1];

            $list = $goods_model->getGoodsSkuPageList($condition, $page, $page_size, $order_by, $field, $alias, $join);
            return $list;
        }else{
            $keyword = input('keyword', '');
            $this->assign('keyword', $keyword);
            $category_id    = input('category_id', 0);
            $category_level = input('category_level', 0);
            $this->assign('category_id', $category_id);
            $this->assign('category_level', $category_level);

            $goods_category_model = new GoodsCategory();
            $category_id_1 = 0;
            $category_id_2 = 0;
            $category_id_3 = 0;
            $first_goods_category_list_result = $goods_category_model->getCategoryList([['pid', '=', 0], ['is_show', '=', 1]]);
            $first_goods_category_list = $first_goods_category_list_result['data'] ?? [];

            $second_goods_category_list = [];
            $third_goods_category_list = [];
            if($category_level > 0){
                $category_info_result = $goods_category_model->getCategoryInfo([['category_id', '=', $category_id]], 'category_id_1,category_id_2,category_id_3');
                $category_info = $category_info_result['data'];
                if(!empty($category_info)){
                    $category_id_1 = $category_info['category_id_1'];
                    $category_id_2 = $category_info['category_id_2'];
                    $category_id_3 = $category_info['category_id_3'];
                    if($category_id_1 > 0){
                        $second_goods_category_list_result = $goods_category_model->getCategoryList([['pid', '=', $category_id_1], ['is_show', '=', 1]]);
                        $second_goods_category_list = $second_goods_category_list_result['data'] ?? [];
                        if($category_id_2 > 0){
                            $third_goods_category_list_result = $goods_category_model->getCategoryList([['pid', '=', $category_id_2], ['is_show', '=', 1]]);
                            $third_goods_category_list = $third_goods_category_list_result['data'] ?? [];
                        }
                    }
                }
            }
            $this->assign('category_id_1', $category_id_1);
            $this->assign('category_id_2', $category_id_2);
            $this->assign('category_id_3', $category_id_3);
            $this->assign('first_goods_category_list', $first_goods_category_list);
            $this->assign('second_goods_category_list', $second_goods_category_list);
            $this->assign('third_goods_category_list', $third_goods_category_list);

            return $this->fetch("goods/lists", [], $this->replace);
        }

    }

    /**
     * 商品详情
     */
    public function detail()
    {
        $goods_model             = new GoodsModel();
        $sku_id                  = input('sku_id', 0);
        $res                     = [];
        $goods_sku_detail        = $goods_model->getGoodsSkuDetail($sku_id);
        $goods_sku_detail        = $goods_sku_detail['data'];
        $res['goods_sku_detail'] = $goods_sku_detail;

        //供应商信息 todo
        $supply_model       = new Supplier();
        $supply_info        = $supply_model->getSupplierInfo([['supplier_site_id', '=', $goods_sku_detail['site_id']]]);
        $res['supply_info'] = $supply_info['data'];

        $this->assign('detail', $res);
        return $this->fetch("goods/detail", [], $this->replace);
    }

    /**
     * 基础信息
     */
    public function info()
    {
        $goods_model = new GoodsModel();
        $sku_id      = input('sku_id', 0);
        if (empty($sku_id)) {
            return $goods_model->error('', 'REQUEST_SKU_ID');
        }

        $field = 'goods_id,sku_id,sku_name,sku_spec_format,price,market_price,stock,click_num,sale_num,
        collect_num,sku_image,sku_images,goods_id,site_id,goods_content,goods_state,verify_state,is_virtual,
        is_free_shipping,goods_spec_format,goods_attr_format,introduction,unit,video_url,evaluate,category_id,
        category_id_1,category_id_2,category_id_3,category_name, min_price,max_price,min_num,price_json';
        $info  = $goods_model->getGoodsSkuInfo([['sku_id', '=', $sku_id]], $field);
        return $info;
    }


    /**
     * 商品推荐
     * @return string
     */
    public function recommend()
    {
        $goods_model = new GoodsModel();
        $page        = input('page', 1);
        $page_size   = input('page_size', PAGE_LIST_ROWS);
        $condition   = [
            ['gs.goods_state', '=', 1],
            ['gs.verify_state', '=', 1],
            ['gs.is_delete', '=', 0]
        ];
        $field = 'gs.goods_id,gs.sku_id,gs.sku_name,gs.market_price,gs.stock,gs.sale_num,gs.sku_image,
        gs.goods_name,gs.site_id,gs.website_id,gs.is_own,gs.is_free_shipping,gs.introduction,g.goods_image,
        gs.min_price,gs.max_price,gs.min_num,gs.price_json';
        $alias = 'gs';
        $join = [
            ['supply_goods g', 'gs.sku_id = g.sku_id', 'inner']
        ];

        $order_by    = 'gs.sort desc,gs.create_time desc';


        //只查看处于开启状态的供应商
        $join[] = [ 'supplier s', 's.supplier_site_id = gs.site_id', 'inner'];
        $condition[] = ['s.status', '=', 1];

        $list = $goods_model->getGoodsSkuPageList($condition, $page, $page_size, $order_by, $field, $alias, $join);
        return $list;
    }
}
