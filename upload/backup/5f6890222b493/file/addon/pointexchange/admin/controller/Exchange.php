<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

namespace addon\pointexchange\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\pointexchange\model\Exchange as ExchangeModel;

/**
 * 积分兑换
 * @author Administrator
 *
 */
class Exchange extends BaseAdmin
{
    /**
     * 积分兑换列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $type = input('type', '');
            $state = input('state', '');
            $condition = [];
            if ($search_text) {
                $condition[] = ['name', 'like', '%' . $search_text . '%'];
            }
            if ($type) {
                $condition[] = ['type', '=', $type];
            }
            if ($state != '') {
                $condition[] = ['state', '=', $state];
            }
            $order = 'create_time desc';
            $field = '*';

            $exchange_model = new ExchangeModel();
            //礼品名称 礼品图片 礼品库存  礼品价格
            return $exchange_model->getExchangePageList($condition, $page, $page_size, $order, $field);
        }
        $this->forthMenu();
        return $this->fetch("exchange/lists");
    }

    /**
     * 添加积分兑换
     */
    public function add()
    {
        if (request()->isAjax()) {
            $type = input('type', '1');//兑换类型 1 赠品 2 优惠券 3 红包
            if ($type == 1) {
                $data = [
                    'type' => 1,//兑换类型 1 赠品 2 优惠券 3 红包
                    'gift_id' => input('gift_id', '0'),//礼品id或优惠券id
                    'point' => input('point', ''),
                    'price' => input('price', '0'),//金额
                    'pay_type' => input('pay_type', '0'),//兑换方式
                    'state' => input('state', ''),
                    'type_name' => '礼品',
                ];
            } elseif ($type == 2) {
                $data = [
                    'type' => 2,//兑换类型 1 赠品 2 优惠券 3 红包
                    'coupon_type_id' => input('coupon_type_id', '0'),//礼品id或优惠券id
                    'point' => input('point', ''),
                    'content' => input('content', ''),
                    'state' => input('state', ''),
                    'type_name' => '优惠券',
                ];
            } elseif ($type == 3) {
                $data = [
                    'type' => 3,
                    'name' => input('name', ''),
                    'image' => input('image', ''),
                    'stock' => input('stock', ''),
                    'point' => input('point', '0'),
                    'balance' => input('balance', '0'),
                    'content' => input('content', ''),
                    'state' => input('state', ''),
                    'type_name' => '红包',
                ];
            } else {
                return error(-1, '');
            }

            $exchange_model = new ExchangeModel();
            $res = $exchange_model->addExchange($data);
            return $res;
        } else {
            return $this->fetch("exchange/add");
        }
    }

    /**
     * 编辑积分兑换
     */
    public function edit()
    {
        $id = input("id", 0);
        $exchange_model = new ExchangeModel();
        if (request()->isAjax()) {
            $type = input('type', '1');//兑换类型 1 赠品 2 优惠券 3 红包
            if ($type == 1) {
                $data = [
                    'type' => 1,//兑换类型 1 赠品 2 优惠券 3 红包
                    'gift_id' => input('gift_id', '0'),//礼品id或优惠券id
                    'point' => input('point', ''),
                    'price' => input('price', '0'),//金额
                    'state' => input('state', ''),
                    'pay_type' => input('pay_type', '0'),//兑换方式
                    'id' => $id
                ];
            } elseif ($type == 2) {
                $data = [
                    'type' => 2,//兑换类型 1 赠品 2 优惠券 3 红包
                    'coupon_type_id' => input('coupon_type_id', '0'),//礼品id或优惠券id
                    'point' => input('point', ''),
                    'content' => input('content', ''),
                    'state' => input('state', ''),
                    'id' => $id
                ];
            } elseif ($type == 3) {
                $data = [
                    'type' => 3,
                    'name' => input('name', ''),
                    'image' => input('image', ''),
                    'stock' => input('stock', ''),
                    'point' => input('point', '0'),
                    'balance' => input('balance', '0'),
                    'content' => input('content', ''),
                    'state' => input('state', ''),
                    'id' => $id
                ];
            } else {
                return error(-1, '');
            }

            $res = $exchange_model->editExchange($data);
            return $res;
        } else {
            $exchange_info = $exchange_model->getExchangeInfo($id);
            if (empty($exchange_info[ 'data' ])) {
                $this->error();
            }
            $this->assign("exchange_info", $exchange_info[ 'data' ]);
            return $this->fetch("exchange/edit");
        }
    }

    /**
     *关闭积分兑换
     */
    public function delete()
    {
        $id = input("id", 0);
        $exchange_model = new ExchangeModel();
        $res = $exchange_model->deleteExchange($id);
        return $res;

    }
}