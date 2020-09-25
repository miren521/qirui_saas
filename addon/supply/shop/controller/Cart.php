<?php

namespace addon\supply\shop\controller;

use addon\supply\model\goods\Cart as CartModel;
use addon\supply\model\goods\Goods;


class Cart extends BaseSupplyshop
{
    public function __construct()
    {
        parent::__construct();
        $check_login_result = $this->checkLogin();
        if($check_login_result['code'] < 0){
            echo json_encode($check_login_result);
            exit();
        }
    }


    /**
     * 我的购物车
     */
    public function cart()
    {
		$count = input('count', '');
		$this->assign('count', $count);
        return $this->fetch("cart/cart", [], $this->replace);
    }

    /**
     * 添加信息
     */
    public function add()
    {

        $cart   = new CartModel();
        $sku_id = input('sku_id', 0);
        $num    = input('num', 0);
        if (empty($sku_id)) {
            return $cart->error('', 'REQUEST_SKU_ID');
        }
        if (empty($num)) {
            return $cart->error('', 'REQUEST_NUM');
        }

        $goods_model      = new Goods();
        $goods_sku_result = $goods_model->getGoodsSkuInfo([['sku_id', '=', $sku_id]], 'site_id');
        $goods_sku_info   = $goods_sku_result['data'];
        if (empty($goods_sku_info)) {
            return $cart->error('', '找不到可用的商品');
        }

        $data = [
            'site_id' => $goods_sku_info['site_id'],
            'shop_id' => $this->site_id,
            'uid'     => $this->uid,
            'sku_id'  => $sku_id,
            'num'     => $num
        ];

        $res = $cart->addCart($data);
        if ($res['code'] == 0) {
            $res = $cart->getCartCount($this->site_id);
        }
        return $res;
    }

    /**
     * 编辑信息
     */
    public function edit()
    {
        $cart_id = input('cart_id', 0);
        $num     = input('num', 0);
        $cart    = new CartModel();
        if (empty($cart_id)) {
            return $cart->error('', 'REQUEST_CART_ID');
        }
        if (empty($num)) {
            return $cart->error('', 'REQUEST_NUM');
        }


        $data = [
            'cart_id' => $cart_id,
            'shop_id' => $this->site_id,
            'num'     => $num
        ];
        $res  = $cart->editCart($data);
        return $res;
    }

    /**
     * 删除信息
     */
    public function delete()
    {
        $cart_id = input('cart_id', 0);
        $cart    = new CartModel();
        if (empty($cart_id)) {
            return $cart->error('', 'REQUEST_CART_ID');
        }
        $data = [
            'cart_id' => $cart_id,
            'shop_id' => $this->site_id
        ];
        $res  = $cart->deleteCart($data);
        return $res;
    }

    /**
     * 清空购物车
     */
    public function clear()
    {
        $cart = new CartModel();
        $data = [
            'shop_id' => $this->site_id
        ];
        $res  = $cart->clearCart($data);
        return $res;
    }

    /**
     * 列表信息
     */
    public function lists()
    {
        $cart = new CartModel();
        $list = $cart->getCart($this->site_id);
        return $list;
    }

    /**
     * 获取购物车数量
     * @return array
     */
    public function count()
    {
        $cart = new CartModel();
        $list = $cart->getCartCount($this->site_id);
        return $list;
    }
}
