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


namespace app\model\goods;


use app\model\BaseModel;

/**
 * 购物车
 */
class Cart extends BaseModel
{

    /**
     * 添加购物车
     * @param $data
     * @return array
     */
    public function addCart($data)
    {
        $cart_info = model("goods_cart")->getInfo([['sku_id', '=', $data[ 'sku_id' ]], ['member_id', '=', $data[ 'member_id' ]]], 'cart_id, num');
        if (!empty($cart_info)) {
            $res = model("goods_cart")->update(['num' => $cart_info[ 'num' ] + $data[ 'num' ]], [['cart_id', '=', $cart_info[ 'cart_id' ]]]);
        } else {
            $res = model("goods_cart")->add($data);
        }
        return $this->success($res);
    }

    /**
     * 更新购物车商品数量
     * @param $data
     * @return array
     */
    public function editCart($data)
    {
        $res = model("goods_cart")->update(['num' => $data[ 'num' ]], [['cart_id', '=', $data[ 'cart_id' ]], ['member_id', '=', $data[ 'member_id' ]]]);
        return $this->success($res);
    }

    /**
     * 删除购物车商品项(可以多项)
     * @param $data
     * @return array
     */
    public function deleteCart($data)
    {
        $res = model("goods_cart")->delete([['cart_id', 'in', (string)$data[ 'cart_id' ]], ['member_id', '=', $data[ 'member_id' ]]]);
        return $this->success($res);
    }

    /**
     * 清空购物车
     * @param $data
     * @return array
     */
    public function clearCart($data)
    {
        $res = model("goods_cart")->delete([['member_id', '=', $data[ 'member_id' ]]]);
        return $this->success($res);
    }

    /**
     * 获取会员购物车
     * @param $member_id
     * @return array
     */
    public function getCart($member_id)
    {

        $field = 'ngc.cart_id, ngc.site_id, ngc.member_id, ngc.sku_id, ngc.num, ngs.sku_name,
            ngs.sku_no, ngs.sku_spec_format, ngs.price, ngs.market_price, ngs.cost_price, 
            ngs.discount_price, ngs.promotion_type, ngs.start_time, ngs.end_time, ngs.stock, 
            ngs.sku_image, ngs.sku_images, ngs.site_name, ngs.website_id, ngs.is_own, ngs.goods_state, 
            ngs.verify_state, ngs.verify_state_remark, ngs.goods_stock_alarm, ngs.is_virtual, 
            ngs.virtual_indate, ngs.is_free_shipping, ngs.shipping_template, ngs.unit, ngs.introduction, ngs.keywords, ngs.goods_id, s.shop_status';
        $alias = 'ngc';
        $join = [
            [
                'goods_sku ngs',
                'ngc.sku_id = ngs.sku_id',
                'inner'
            ],
        ];

        //只查看处于开启状态的店铺
        $join[] = [ 'shop s', 's.site_id = ngc.site_id', 'inner'];

        $list = model("goods_cart")->getList([['ngc.member_id', '=', $member_id]], $field, '', $alias, $join);
        return $this->success($list);
    }

    /**
     * 获取购物车数量
     * @param $member_id
     * @return array
     */
    public function getCartCount($member_id)
    {
        $list = model("goods_cart")->getCount([['member_id', '=', $member_id]]);
        return $this->success($list);
    }

    public function getCartItemsNum($member_id)
    {
        $num = model("goods_cart")->getSum(['member_id' => $member_id], 'num');
        return $this->success($num);
    }
}
