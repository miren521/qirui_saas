<?php

namespace addon\platformcoupon\component\controller;

use app\component\controller\BaseDiyView;

/**
 * 平台优惠券·组件
 */
class AdminPlatformcoupon extends BaseDiyView
{
	/**
	 * 后台编辑界面
	 */
	public function design()
	{
		return $this->fetch("admin_coupon/design.html");
	}
}