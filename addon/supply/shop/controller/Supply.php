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
use app\shop\controller\BaseShop;

/**
 * 供应商信息
 * Class Order
 * @package app\shop\controller
 */
class Supply extends BaseSupplyshop
{


    /**
     * 供应商专页
     */
    public function index()
    {
        $supply_id = input('supply_id', 0);
        //供应商信息 todo
        $supply_model = new Supplier();
        $supply_info  = $supply_model->getSupplierInfo([['supplier_site_id', '=', $supply_id]], '*');
        $this->assign('detail', $supply_info['data'] ?? []);
        return $this->fetch("supply/index", [], $this->replace);
    }


}