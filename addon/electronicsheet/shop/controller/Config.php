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


namespace addon\electronicsheet\shop\controller;

use app\shop\controller\BaseShop;
use addon\electronicsheet\model\ExpressElectronicsheet as ExpressElectronicsheetModel;


class Config extends BaseShop
{
	
	/*
	 *  电子面单设置
	 */
	public function config()
	{
        $config = new ExpressElectronicsheetModel();
        if (request()->isAjax()) {

            $data = [
                'site_id' => $this->site_id,
                'type' => input('type','kdniao'),

                'kdniao_user_id' => input('kdniao_user_id',''),
                'kdniao_api_key' => input('kdniao_api_key',''),
                'kdniao_port' => input('kdniao_port',''),

                'cainiao_token' => input('cainiao_token',''),
                'cainiao_ip' => input('cainiao_ip',''),
            ];

            return $config->setElectronicsheetConfig($data);
        } else {

            $res = $config->getElectronicsheetConfig($this->site_id);
            $this->assign('config_info', $res['data']['value']);
            return $this->fetch('config/config');
        }
	}

}