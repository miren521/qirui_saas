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


namespace addon\wholesale\shop\controller;

use app\shop\controller\BaseShop;
use addon\wholesale\model\Wholesale as WholesaleModel;

/**
 * 批发控制器
 */
class Wholesale extends BaseShop
{
	
	/*
	 *  批发活动列表
	 */
	public function lists()
	{
		$model = new WholesaleModel();
		$goods_name = input('goods_name', '');
		
		$condition[] = [ 'g.site_id', '=', $this->site_id ];
		if (request()->isAjax()) {
            $condition[] = [ 'wg.wholesale_goods_id', '>', 0 ];
			$condition[] = [ 'g.goods_name', 'like', '%' . $goods_name . '%' ];
            $page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);

            $alias = 'g';
            $join = [
                [ 'wholesale_goods wg', 'wg.goods_id = g.goods_id', 'left' ],
                [ 'goods_sku sku', 'g.sku_id = sku.sku_id', 'left' ]
            ];
            $field = 'g.*,wg.wholesale_goods_id, wg.max_price, wg.min_price, wg.min_num, wg.status,sku.sku_id,sku.price,sku.sku_name,sku.sku_image';

			$list = $model->getWholesaleGoodsViewPageList($condition, $page, $page_size, 'g.create_time desc', $field, $alias, $join);
			return $list;
		} else {
            return $this->fetch("wholesale/lists");
		}

	}



    /**
     * 添加批发商品
     */
    public function add()
    {
        if (request()->isAjax()) {
            $wholesale_data = [
                'site_id' => $this->site_id,
                'site_name' => $this->shop_info['site_name'],
                'price_json' => input('price_json', ''),
                'goods_id' => input('goods_id', ''),
            ];

            $wholesale_model = new WholesaleModel();
            return $wholesale_model->addGoodsWholesale($wholesale_data);
        } else {

            return $this->fetch("wholesale/add");
        }
    }

    /**
     * 编辑批发商品
     */
    public function edit()
    {
        $goods_id = input('goods_id', 0);
        $wholesale_model = new WholesaleModel();
        $condition = array(
            ['site_id', '=', $this->site_id],
            ['goods_id', '=', $goods_id],
        );
        if (request()->isAjax()) {
            $wholesale_data = [
                'site_id' => $this->site_id,
                'site_name' => $this->shop_info['site_name'],
                'price_json' => input('price_json', ''),
                'goods_id' => $goods_id,
            ];
            return $wholesale_model->editGoodsWholesale($wholesale_data, $condition);
        } else {
            $this->assign('goods_id', $goods_id);

            $info_result = $wholesale_model->getWholesaleGoodsDetail($condition);
            $this->assign('info', $info_result['data'] ?? []);
            return $this->fetch("wholesale/edit");
        }
    }


    /**
     * 删除批发商品
     */
	public function delete(){
        if (request()->isAjax()) {
            $goods_id = input('goods_id', 0);
            $wholesale_model = new WholesaleModel();
            $condition = array(
                ['goods_id', '=', $goods_id],
                ['site_id', '=', $this->site_id]
            );
            $result = $wholesale_model->delete($condition);
            return $result;
        }
    }


}