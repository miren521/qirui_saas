<?php

namespace app\component\controller;

/**
 * 店铺信息·组件
 */
class ShopInfo extends BaseDiyView
{
	/**
	 * 后台编辑界面
	 */
	public function design()
	{
		return $this->fetch("shop_info/design.html");
	}
}