<?php

namespace addon\bargain\component\controller;

use app\component\controller\BaseDiyView;

/**
 * 砍价模块·组件
 *
 */
class Bargain extends BaseDiyView
{
	
	/**
	 * 设计界面
	 */
	public function design()
	{
		return $this->fetch("bargain/design.html");
	}
}