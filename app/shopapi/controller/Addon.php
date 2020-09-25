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


namespace app\shopapi\controller;

use app\model\system\Addon as AddonModel;

/**
 * 插件管理
 * @author Administrator
 *
 */
class Addon extends BaseApi
{
	
	/**
	 * 列表信息
	 */
	public function lists()
	{
		$addon = new AddonModel();
		$list = $addon->getAddonList();
		return $this->response($list);
	}
	
	public function addonisexit()
	{
		$res = [];
		$res['fenxiao'] = addon_is_exit('fenxiao');// 分销
		$res['pintuan'] = addon_is_exit('pintuan');// 拼团
		$res['membersignin'] = addon_is_exit('membersignin');// 会员签到
		$res['memberrecharge'] = addon_is_exit('memberrecharge');// 会员充值
		$res['memberwithdraw'] = addon_is_exit('memberwithdraw');// 会员提现
		$res['gift'] = addon_is_exit('gift');// 礼品
		$res['pointexchange'] = addon_is_exit('pointexchange');// 积分兑换
		
		return $this->response($this->success($res));
	}
	
	/**
	 * 插件是否存在
	 */
	public function isexit(){
		$name = $this->params['name'] ?? '';
		$res = 0;
		if (!empty($name)) $res = addon_is_exit($name);
		return $this->response($this->success($res));
	}
	
}