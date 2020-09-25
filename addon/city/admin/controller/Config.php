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


namespace addon\city\admin\controller;

use addon\city\model\CitySettlement;
use app\admin\controller\BaseAdmin;


class Config extends BaseAdmin
{
    /**
     * 首页跳转
     */
    public function config()
    {
        $account_model = new CitySettlement();
        if(request()->isAjax()) {

            $config_json = input('config_json', '');
            $data = $config_json ? json_decode($config_json, true) : [];

            return $account_model->setCitySettlementConfig($data);
        }else {
            $config_info = $account_model->getCitySettlementConfig();
            $this->assign('config_info', $config_info['data']);

            return $this->fetch("config/config");
        }

    }
}