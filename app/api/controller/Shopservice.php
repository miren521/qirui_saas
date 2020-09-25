<?php
/**
 * Shopservice.php
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

namespace app\api\controller;

use app\model\shop\ShopService as ShopServiceModel;

/**
 * 店铺服务
 * Class Shopcategory
 * @package app\api\controller
 */
class Shopservice extends BaseApi
{

	public function lists()
	{
		$shop_service_model = new ShopServiceModel();
		$list = $shop_service_model->getServiceNameList();
		foreach ($list as $k => $v) {
			unset($list[ $k ][ 'icon' ]);
		}
		return $this->response($this->success($list));
	}

}