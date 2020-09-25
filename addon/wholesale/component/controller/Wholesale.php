<?php

namespace addon\wholesale\component\controller;

use app\component\controller\BaseDiyView;

/**
 * 批发模块·组件
 *
 */
class Wholesale extends BaseDiyView
{
	
	/**
	 * 设计界面
	 */
	public function design()
	{
		return $this->fetch("wholesale/design.html");
	}
}