<?php
/**
 * Goodsevaluate.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

namespace addon\supply\shop\controller;

use addon\supply\model\goods\GoodsEvaluate as GoodsEvaluateModel;
use app\shop\controller\BaseShop;
use addon\supply\model\order\OrderCommon as OrderCommonModel;

/**
 * 商品评价
 * Class Goodsevaluate
 * @package app\api\controller
 */
class Goodsevaluate extends BaseSupplyshop
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
     * 添加信息·第一次评价
     */
    public function evaluate()
    {
        if (request()->isAjax()) {
            $goods_evaluate_model  = new GoodsEvaluateModel();
            $order_id              = input('order_id', 0);
            $order_no              = input('order_no', '');
            $is_anonymous          = input('is_anonymous', 0);
            $goods_evaluate        = input('goods_evaluate', '');
            $supply_desccredit     = input('supply_desccredit', 5);//描述分值
            $supply_servicecredit  = input('supply_servicecredit', 5);//服务分值
            $supply_deliverycredit = input('supply_deliverycredit', 5);//配送分值
            if (empty($order_id)) {
                return $goods_evaluate_model->error('', 'REQUEST_ORDER_ID');
            }

            if (empty($goods_evaluate)) {
                return $goods_evaluate_model->error('', 'REQUEST_GOODS_EVALUATE');
            }

            $goods_evaluate = json_decode($goods_evaluate, true);
            $data           = [
                'order_id'              => $order_id,
                'order_no'              => $order_no,
                'shop_name'             => $this->shop_info['site_name'],
                'shop_id'               => $this->site_id,
                'is_anonymous'          => $is_anonymous,
                'shop_img'              => $this->shop_info['logo'],
                'goods_evaluate'        => $goods_evaluate,
                'supply_desccredit'     => $supply_desccredit,
                'supply_servicecredit'  => $supply_servicecredit,
                'supply_deliverycredit' => $supply_deliverycredit,
            ];
            $res            = $goods_evaluate_model->addEvaluate($data);
            //计算店铺评分
            $supply_data = [
                'supply_desccredit'     => $supply_desccredit,
                'supply_servicecredit'  => $supply_servicecredit,
                'supply_deliverycredit' => $supply_deliverycredit,
            ];
            $goods_evaluate_model->supplyEvaluate($order_id, $supply_data);
            return $res;
        } else {
            $order_id           = input('order_id', 0);

            $order_common_model = new OrderCommonModel();
            if (empty($order_id)) {
                $this->error('没有找到该订单');
            }

            $order_info_result = $order_common_model->getOrderInfo([
                ['order_id', '=', $order_id],
                ['buyer_shop_id', '=', $this->site_id],
                ['order_status', 'in', ('4,10')],
                ['is_evaluate', '=', 1],
            ], 'evaluate_status,evaluate_status_name,order_status,is_evaluate,site_name');
            $detail            = $order_info_result['data'];
            if (empty($detail)) {
                $this->error('没有找到该订单');
            }

            if ($detail['evaluate_status'] == 2) {
                $this->error('该订单已评价');
            }

            $condition      = [
                ['order_id', '=', $order_id],
                ['buyer_shop_id', '=', $this->site_id],
                ['refund_status', '<>', 3],
            ];
            $list           = $order_common_model->getOrderGoodsList(
                $condition,
                'order_goods_id,order_id,order_no,site_id,site_name,goods_id,sku_id,sku_name,sku_image,price,num'
            );
            $list           = $list['data'];
            $detail['list'] = $list;

            $this->assign('detail', $detail);
            return $this->fetch("goodsevaluate/evaluate", [], $this->replace);
        }
    }

    /**
     * 追评
     * @return string
     */
    public function again()
    {
        if (request()->isAjax()) {
            $goods_evaluate_model = new GoodsEvaluateModel();
            $order_id             = input('order_id', 0);
            $goods_evaluate       = input('goods_evaluate', '');
            if (empty($order_id)) {
                return $goods_evaluate_model->error('', 'REQUEST_ORDER_ID');
            }

            if (empty($goods_evaluate)) {
                return $goods_evaluate_model->error('', 'REQUEST_GOODS_EVALUATE');
            }

            $goods_evaluate = json_decode($goods_evaluate, true);
            $data           = [
                'order_id'       => $order_id,
                'goods_evaluate' => $goods_evaluate
            ];
            $res            = $goods_evaluate_model->evaluateAgain($data);
            return $res;
        } else {
            $order_id           = input('order_id', 0);
            $order_common_model = new OrderCommonModel();
            if (empty($order_id)) {
                $this->error('没有找到该订单');
            }

            $order_info_result = $order_common_model->getOrderInfo([
                ['order_id', '=', $order_id],
                ['buyer_shop_id', '=', $this->site_id],
                ['order_status', 'in', ('4,10')],
                ['is_evaluate', '=', 1],
            ], 'evaluate_status,evaluate_status_name');
            $detail            = $order_info_result['data'];
            if (empty($detail)) {
                $this->error('没有找到该订单');
            }

            if ($detail['evaluate_status'] == 2) {
                $this->error('该订单已评价');
            }

            $condition      = [
                ['order_id', '=', $order_id],
                ['buyer_shop_id', '=', $this->site_id],
                ['refund_status', '<>', 3],
            ];
            $list = $order_common_model->getOrderGoodsList(
                $condition,
                'order_goods_id,order_id,order_no,site_id,site_name,goods_id,sku_id,sku_name,sku_image,price,num'
            );
            $list           = $list['data'];
            $detail['list'] = $list;
            $this->assign('detail', $detail);
            return $this->fetch("goodsevaluate/evaluate_again", [], $this->replace);
        }
    }

    /**
     * 基础信息
     */
    public function firstinfo()
    {
        $goods_evaluate_model = new GoodsEvaluateModel();
        $goods_id             = input('goods_id', 0);
        if (empty($goods_id)) {
            return $goods_evaluate_model->error('', 'REQUEST_GOODS_ID');
        }
        $condition = [
            ['is_show', '=', 1],
            ['goods_id', '=', $goods_id]
        ];
        $info      = $goods_evaluate_model->getFirstEvaluateInfo($condition);
        return $info;
    }

    /**
     * 列表信息
     */
    public function page()
    {
        if (request()->isAjax()) {
            $goods_evaluate_model = new GoodsEvaluateModel();
            $page                 = input('page', 1);
            $page_size            = input('page_size', PAGE_LIST_ROWS);
            $goods_id             = input('goods_id', 0);
            if (empty($goods_id)) {
                return $goods_evaluate_model->error('', 'REQUEST_GOODS_ID');
            }
            $condition = [
                ['is_show', '=', 1],
                ['goods_id', '=', $goods_id]
            ];
            $list      = $goods_evaluate_model->getEvaluatePageList($condition, $page, $page_size);
            return $list;
        }
    }
}
