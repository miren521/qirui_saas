<?php

namespace addon\fenxiao\component\controller;

use app\component\controller\BaseDiyView;

/**
 * 分销商品·组件
 *
 */
class FenxiaoGoodsList extends BaseDiyView
{
	
	/**
	 * 设计界面
	 */
	public function design()
	{
		return $this->fetch("goods_list/design.html");
	}
}