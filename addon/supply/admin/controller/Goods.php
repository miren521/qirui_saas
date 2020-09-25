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


namespace addon\supply\admin\controller;

use addon\supply\model\goods\Goods as GoodsModel;
use addon\supply\model\Config as GoodsConfigModel;
use addon\supply\model\goods\GoodsEvaluate;

/**
 * 商品管理 控制器
 */
class Goods extends BaseSupply
{
    /******************************* 正常商品列表及相关操作 ***************************/

    /**
     * 商品列表
     */
    public function lists()
    {
        $goods_model = new GoodsModel();
        if (request()->isAjax()) {
            $page_index       = input('page', 1);
            $page_size        = input('page_size', PAGE_LIST_ROWS);
            $search_text      = input('search_text', "");
            $search_text_type = input('search_text_type', "goods_name");
            $goods_state      = input('goods_state', "");
            $verify_state     = input('verify_state', "");
            $category_id      = input('category_id', "");
            $brand_id         = input('goods_brand', '');
            $goods_attr_class = input("goods_attr_class", "");
            $site_id          = input("site_id", "");
            $goods_class      = input('goods_class', "");

            $condition = [['is_delete', '=', 0]];
            if (!empty($search_text)) {
                $condition[] = [$search_text_type, 'like', '%' . $search_text . '%'];
            }

            if ($goods_class !== "") {
                $condition[] = ['goods_class', '=', $goods_class];
            }
            if ($goods_state !== '') {
                $condition[] = ['goods_state', '=', $goods_state];
            }
            if ($verify_state !== '') {
                $condition[] = ['verify_state', '=', $verify_state];
            }
            if (!empty($category_id)) {
                $condition[] = ['category_id|category_id_1|category_id_2|category_id_3', '=', $category_id];
            }
            if ($brand_id) {
                $condition[] = ['brand_id', '=', $brand_id];
            }
            if ($goods_attr_class) {
                $condition[] = ['goods_attr_class', '=', $goods_attr_class];
            }
            if (!empty($site_id)) {
                $condition[] = ['site_id', '=', $site_id];
            }

            $res = $goods_model->getGoodsPageList($condition, $page_index, $page_size);
            return $res;
        } else {
            $verify_state = $goods_model->getVerifyState();
            $arr          = [];
            foreach ($verify_state as $k => $v) {
                // 过滤已审核状态
                if ($k != 1) {
                    $total = $goods_model->getGoodsTotalCount([['verify_state', '=', $k]]);
                    $total = $total['data'];
                    $arr[] = [
                        'state' => $k,
                        'value' => $v,
                        'count' => $total
                    ];
                }
            }
            $verify_state = $arr;
            $this->assign("verify_state", $verify_state);
            return $this->fetch('goods/lists');
        }
    }

    /**
     * 商品ＳＫＵ列表
     *
     * @return void
     */
    public function skulists()
    {
        $page           = input('page', 1);
        $page_size      = input('page_size', '');
        $site_id        = input('site_id', 0);
        $category_id    = input('category_id', 0);
        $category_level = input('category_level', 0);
        $brand_id       = input('brand_id', 0);
        $min_price      = input('min_price', "");
        $max_price      = input('max_price', "");

        $condition = [];
        $field     = 'gs.goods_id,gs.sku_id,gs.sku_name,gs.sku_image,gs.introduction,gs.stock,gs.site_id,gs.market_price,gs.min_price,gs.max_price,gs.min_num,gs.price_json';

        $alias = 'gs';
        $join  = [
            ['goods g', 'gs.sku_id = g.sku_id', 'inner']
        ];

        if (!empty($site_id)) {
            $condition[] = ['gs.site_id', '=', $site_id];
        }

        if (!empty($category_id) && !empty($category_level)) {
            $condition[] = ['gs.category_id_' . $category_level, '=', $category_id];
        }

        if (!empty($brand_id)) {
            $condition[] = ['gs.brand_id', '=', $brand_id];
        }

        if ($min_price != "" && $max_price != "") {
            $condition[] = ['gs.price', 'between', [$min_price, $max_price]];
        } elseif ($min_price != "") {
            $condition[] = ['gs.price', '>=', $min_price];
        } elseif ($max_price != "") {
            $condition[] = ['gs.price', '<=', $max_price];
        }

        $condition[] = ['gs.goods_state', '=', 1];
        $condition[] = ['gs.verify_state', '=', 1];
        $condition[] = ['gs.is_delete', '=', 0];


        $goods = new GoodsModel();
        $list  = $goods->getGoodsSkuPageList($condition, $page, PAGE_LIST_ROWS, '', $field, $alias, $join);
        return $list;
    }


    /**
     * 刷新审核状态商品数量
     */
    public function refreshVerifyStateCount()
    {
        if (request()->isAjax()) {
            $goods_model  = new GoodsModel();
            $verify_state = $goods_model->getVerifyState();
            $arr          = [];
            foreach ($verify_state as $k => $v) {
                // 过滤已审核状态
                if ($k != 1) {
                    $total = $goods_model->getGoodsTotalCount([['verify_state', '=', $k], ['is_delete', '=', 0]]);
                    $total = $total['data'];
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
            $goods_id    = input("goods_id", 0);
            $goods_model = new GoodsModel();
            $res         = $goods_model->getGoodsSkuList([['goods_id', '=', $goods_id]], 'sku_id,sku_name,price,price_json,stock,sale_num,sku_image,spec_name,min_price,max_price,min_num');
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
            $goods_ids           = input("goods_ids", 0);
            $goods_model         = new GoodsModel();
            $res                 = $goods_model->lockup([['goods_id', 'in', $goods_ids]], $verify_state_remark);
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
            $goods_id    = input("goods_id", 0);
            $goods_model = new GoodsModel();
            $res         = $goods_model->getGoodsInfo([['goods_id', '=', $goods_id], ['verify_state', 'in', [-2, 10]]], 'verify_state_remark');
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
            $goods_ids           = input("goods_ids", 0);
            $verify_state        = input("verify_state", -2);
            $verify_state_remark = input("verify_state_remark", '');
            $goods_model         = new GoodsModel();
            $res                 = $goods_model->modifyVerifyState($goods_ids, $verify_state, $verify_state_remark);
            return $res;
        }
    }


    /******************************* 商品评价列表及相关操作 ***************************/

    /**
     * 商品评价
     */
    public function evaluateList()
    {
        if (request()->isAjax()) {
            $page_index   = input('page', 1);
            $page_size    = input('limit', PAGE_LIST_ROWS);
            $site_id      = input("site_id", "");
            $explain_type = input('explain_type', ''); //1好评2中评3差评
            $search_text  = input('search_text', "");
            $search_type  = input('search_type', "sku_name");
            $condition    = [];
            //评分类型
            if ($explain_type != "") {
                $condition[] = ["explain_type", "=", $explain_type];
            }
            if (!empty($search_text)) {
                if (!empty($search_type)) {
                    $condition[] = [$search_type, 'like', '%' . $search_text . '%'];
                } else {
                    $condition[] = ['sku_name', 'like', '%' . $search_text . '%'];
                }
            }
            if (!empty($site_id)) {
                $condition[] = ['site_id', '=', $site_id];
            }

            $evaluate_model = new GoodsEvaluate();
            $res            = $evaluate_model->getEvaluatePageList($condition, $page_index, $page_size);
            return $res;
        } else {
            return $this->fetch('goods/evaluate_list');
        }
    }

    /**
     * 评价删除
     */
    public function deleteEvaluate()
    {
        if (request()->isAjax()) {
            $id             = input('id', '');
            $evaluate_model = new GoodsEvaluate();
            $res            = $evaluate_model->deleteEvaluate($id);
            $this->addLog("删除商品评价id:" . $id);
            return $res;
        }
    }
}
