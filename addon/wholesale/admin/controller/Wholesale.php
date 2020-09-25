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


namespace addon\wholesale\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\wholesale\model\Wholesale as WholesaleModel;

/**
 * 批发控制器
 */
class Wholesale extends BaseAdmin
{
    /*
     *  批发活动列表
     */
    public function lists()
    {
        $wholesale_model = new WholesaleModel();
        $goods_name = input('goods_name', '');

        $condition[] = [ 'wg.wholesale_goods_id', '>', 0 ];
        if (request()->isAjax()) {

            $condition[] = [ 'g.goods_name', 'like', '%' . $goods_name . '%' ];
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $alias = 'g';
            $join = [
                [ 'wholesale_goods wg', 'wg.goods_id = g.goods_id', 'left' ],
                [ 'goods_sku sku', 'g.sku_id = sku.sku_id', 'left' ]
            ];
            $field = 'g.*,wg.wholesale_goods_id, wg.max_price, wg.min_price, wg.min_num, wg.status,sku.sku_id,sku.price,sku.sku_name,sku.sku_image';
            $list = $wholesale_model->getWholesaleGoodsViewPageList($condition, $page, $page_size, 'g.create_time desc', $field, $alias, $join);
            return $list;
        } else {
            return $this->fetch("wholesale/lists");
        }

    }

    /**
     * 批发商品规格列表
     */
    public function detail(){
        $goods_id = input('goods_id', 0);
        $wholesale_model = new WholesaleModel();
        $condition = array(
            ['goods_id', '=', $goods_id],
        );
        $info_result = $wholesale_model->getWholesaleGoodsDetail($condition);
        $this->assign('info', $info_result['data'] ?? []);
        return $this->fetch("wholesale/detail");
    }

    /**
     * 商品加入批发
     */
    public function delete(){
        if (request()->isAjax()) {
            $goods_id = input('goods_id', 0);
            $wholesale_model = new WholesaleModel();
            $condition = array(
                ['goods_id', '=', $goods_id]
            );
            $result = $wholesale_model->delete($condition);
            return $result;
        }
    }
	
}