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


namespace addon\supply\admin\controller;

use addon\supply\model\Config as ConfigModel;
use app\admin\controller\BaseAdmin;

/**
 * 供应商管理
 */
class Withdraw extends BaseAdmin
{

    /**
     * 供应商配置
     */
    public function config()
    {
        $config_model = new ConfigModel();
        if (request()->isAjax()) {
            $data = [
                'min_withdraw' => input('min_withdraw', 0),
                'max_withdraw' => input('max_withdraw', 0),
            ];
            $this->addLog("修改供应商提现配置");
            $res = $config_model->setSupplyWithdrawConfig($data);
            return $res;
        } else {
            $copyright = $config_model->getSupplyWithdrawConfig();
            $this->assign('config', $copyright['data']['value']);
            return $this->fetch('withdraw/config');
        }
    }
}
