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


namespace addon\present\shop\controller;

use app\model\goods\Goods as GoodsModel;
use app\model\shop\Shop as ShopModel;
use app\shop\controller\BaseShop;
use addon\present\model\Present as PresentModel;

/**
 * 赠品控制器
 */
class Present extends BaseShop
{

    /*
     *  赠品活动列表
     */
    public function lists()
    {
        $model = new PresentModel();
        //获取续签信息
        if (request()->isAjax()) {
            $condition = [];
            $condition[] = ['site_id', '=', $this->site_id];
            $status = input('status', '');//赠品状态
            if ($status) {
                $condition[] = ['status', '=', $status];
            }
            $goods_name = input('goods_name', '');
            if(!empty($goods_name)){
                $condition[] = ['sku_name', 'like', '%' . $goods_name . '%'];
            }
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getPresentPageList($condition, $page, $page_size, 'present_id desc');
            return $list;
        } else {
            return $this->fetch("present/lists");
        }

    }

    /**
     * 添加活动
     */
    public function add()
    {
        if (request()->isAjax()) {
            $site_model = new ShopModel();
            $site_info = $site_model->getShopInfo([['site_id', '=', $this->site_id]], 'site_name');
            //获取商品信息
            $goods_id = input('goods_id', 0);
            $sku_id = input('sku_id', 0);
            $goods_model = new GoodsModel();
            $sku_info = $goods_model->getGoodsSkuInfo([['goods_id', '=', $goods_id], ['sku_id', '=', $sku_id]], 'sku_name,sku_image,price,is_virtual, stock')['data'] ?? [];
            $present_data = [
                'site_id' => $this->site_id,
                'site_name' => $site_info[ 'data' ][ 'site_name' ],
                'goods_id' => $goods_id,
                'sku_name' => $sku_info[ 'sku_name' ],
                'sku_image' => $sku_info[ 'sku_image' ],
                'sku_price' => $sku_info[ 'price' ],
                'is_virtual' => $sku_info['is_virtual'] ?? 0,
                'sku_id' => $sku_id,
                'limit_num' => input('limit_num', 0),//每人限制领用
                'start_time' => strtotime(input('start_time', '')),
                'end_time' => strtotime(input('end_time', '')),
                'stock' => $sku_info['stock']
            ];

            $present_model = new PresentModel();
            return $present_model->addPresent($present_data);
        } else {
            return $this->fetch("present/add");
        }
    }

    /**
     * 编辑活动
     */
    public function edit()
    {
        $present_model = new PresentModel();

        if (request()->isAjax()) {
            //获取商品信息
            $present_data = [
                'limit_num' => input('limit_num', 0),
                'start_time' => strtotime(input('start_time', '')),
                'end_time' => strtotime(input('end_time', '')),
            ];

            $present_id = input('present_id', '');
            $condition = array(
                ['present_id' , '=', $present_id],
                ['site_id', '=', $this->site_id]
            );
            return $present_model->editPresent($condition, $present_data);

        } else {
            $present_id = input('present_id', '');
            //获取赠品信息
            $present_info = $present_model->getPresentInfo([['present_id', '=', $present_id]]);
            $this->assign('present_info', $present_info);
            return $this->fetch("present/edit");
        }
    }

    /*
     *  赠品详情
     */
    public function detail()
    {
        $present_model = new PresentModel();

        $present_id = input('present_id', '');
        //获取赠品信息
        $present_info = $present_model->getPresentInfo([['present_id', '=', $present_id]]);
        $this->assign('present_info', $present_info);
        return $this->fetch("present/detail");
    }

    /*
     *  删除赠品活动
     */
    public function delete()
    {
        $present_id = input('present_id', '');
        $site_id = $this->site_id;

        $present_model = new PresentModel();
        $condition = array(
            ['site_id', '=', $site_id],
            ['present_id', '=', $present_id]
        );
        return $present_model->deletePresent($condition);
    }

    /*
     *  结束赠品活动
     */
    public function finish()
    {
        $present_id = input('present_id', '');
        $site_id = $this->site_id;

        $present_model = new PresentModel();
        $condition = array(
            ['site_id', '=', $site_id],
            ['present_id', '=', $present_id]
        );
        return $present_model->finishPresent($condition);
    }

}