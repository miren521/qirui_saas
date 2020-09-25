<?php

namespace app\component\controller;

/**
 * 店铺门店·组件
 */
class ShopStore extends BaseDiyView
{
	/**
	 * 后台编辑界面
	 */
	public function design()
	{
		return $this->fetch("shop_store/design.html");
	}
}