<?php

namespace addon\groupbuy\component\controller;

use app\component\controller\BaseDiyView;

/**
 * 团购模块·组件
 *
 */
class Groupbuy extends BaseDiyView
{
	
	/**
	 * 设计界面
	 */
	public function design()
	{
		return $this->fetch("groupbuy/design.html");
	}
}