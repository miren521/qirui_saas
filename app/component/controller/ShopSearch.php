<?php

namespace app\component\controller;

/**
 * 店铺搜索·组件
 */
class ShopSearch extends BaseDiyView
{
	/**
	 * 后台编辑界面
	 */
	public function design()
	{
		return $this->fetch("shop_search/design.html");
	}
}