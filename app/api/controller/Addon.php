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


namespace app\api\controller;

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
		$res['fenxiao'] = addon_is_exit('fenxiao');						// 分销
		$res['pintuan'] = addon_is_exit('pintuan');						// 拼团
		$res['membersignin'] = addon_is_exit('membersignin');			// 会员签到
		$res['memberrecharge'] = addon_is_exit('memberrecharge');		// 会员充值
		$res['memberwithdraw'] = addon_is_exit('memberwithdraw');		// 会员提现
		$res['gift'] = addon_is_exit('gift');							// 礼品
		$res['pointexchange'] = addon_is_exit('pointexchange');			// 积分兑换


		$res['city'] = addon_is_exit('city');							//城市分站
		$res['manjian'] = addon_is_exit('manjian');						//满减
		$res['memberconsume'] = addon_is_exit('memberconsume');			//会员消费
		$res['memberregister'] = addon_is_exit('memberregister');		//会员注册
		$res['coupon'] = addon_is_exit('coupon');						//优惠券
		$res['bundling'] = addon_is_exit('bundling');					//组合套餐
		$res['discount'] = addon_is_exit('discount');					//限时折扣
		$res['seckill'] = addon_is_exit('seckill');						//秒杀
		$res['topic'] = addon_is_exit('topic');							//专题活动
		$res['store'] = addon_is_exit('store');							//门店管理
		$res['groupbuy'] = addon_is_exit('groupbuy');					//团购

        $res['bargain'] = addon_is_exit('bargain');					//砍价
        $res['live'] = addon_is_exit('live');					//直播
        $res['wholesale'] = addon_is_exit('wholesale');					//批发
        $res['servicer'] = addon_is_exit('servicer');					//客服
        $res['platformcoupon'] = addon_is_exit('platformcoupon');					//平台优惠券

        return $this->response($this->success($res));
	}

	/**
	 * 插件是否存在
	 */
	public function isexit()
    {
		$name = $this->params['name'] ?? '';
		$res = 0;
		if (!empty($name)) $res = addon_is_exit($name);
		return $this->response($this->success($res));
	}

}