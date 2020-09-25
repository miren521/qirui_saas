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


namespace app\shop\controller;

use app\model\shop\Config as ConfigModel;

/**
 * 商家入驻
 */
class Shopjoin extends BaseShop
{

	/**
	 * 入驻指南
	 */
	public function guide()
	{
		if (request()->isAjax()) {
			$config_model = new ConfigModel();
			return $config_model->getShopJoinGuide();
		} else {
            $this->assign('menu_info', ['title' => '入驻指南']);
			return $this->fetch('shopjoin/guide');
		}
	}
	
	/**
	 * 入驻指南详情
	 */
	public function guideDetail()
	{
        //指南详情
        $guide_index = input('guide_index', 1);
        $config_model = new ConfigModel();
        $guide_info = $config_model->getShopJoinGuideDocument($guide_index);

        $this->assign("guide_info", $guide_info['data']);
        $this->assign('menu_info', ['title' => $guide_info['data']['title']]);
        return $this->fetch("shopjoin/guide_detail");
	}

}