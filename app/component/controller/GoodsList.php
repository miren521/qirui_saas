<?php

namespace app\component\controller;

/**
 * 商品列表·组件
 */
class GoodsList extends BaseDiyView
{
	/**
	 * 后台编辑界面
	 */
	public function design()
	{
		return $this->fetch("goods_list/design.html");
	}
}