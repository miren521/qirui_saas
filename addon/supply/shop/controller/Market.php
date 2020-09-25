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

use app\model\goods\GoodsCategory;
use addon\supply\model\web\Adv as AdvModel;
use addon\supply\model\web\AdvPosition as AdvPositionModel;
use app\shop\controller\BaseShop;
use addon\supply\model\goods\Goods as GoodsModel;
use addon\supply\model\Supplier;

/**
 * 市场
 * Class Order
 * @package app\shop\controller
 */
class Market extends BaseSupplyshop
{


    /**
     * 市场选货
     */
    public function index()
    {
        //商品分类
        $goods_category_model = new GoodsCategory();
        $condition            = [
            ['is_show', '=', 1],
            ['level', '<=', 3]
        ];
        $field = "category_id,category_name,short_name,pid,level,image,category_id_1,category_id_2,category_id_3,image_adv";
        $order = "sort desc,category_id desc";
        $list  = $goods_category_model->getCategoryTree($condition, $field, $order);

        $this->assign('category_list', $list['data'] ?? []);

        //广告图
        $this->adv('NS_SUPPLY_SHOP_INDEX');
        return $this->fetch("market/index", [], $this->replace);
    }







    /**
     * 广告位管理
     */
    public function adv($keyword)
    {
        $adv_position_model = new AdvPositionModel();
        $adv_model          = new AdvModel();
        $info               = $adv_position_model->getAdvPositionInfo([['keyword', '=', $keyword]]);
        $info               = $info['data'];
        $res                = [
            'adv_position' => $info
        ];
        $list               = $adv_model->getAdvList(
            [['ap_id', '=', $info['ap_id']]],
            $field = 'adv_id, adv_title, ap_id, adv_url, adv_image, slide_sort, background'
        );
        $list               = $list['data'];
        $res['adv_list']    = $list;
        $this->assign('adv', $res);
    }
}
