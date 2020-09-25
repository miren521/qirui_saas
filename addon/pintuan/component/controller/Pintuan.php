<?php

namespace addon\pintuan\component\controller;

use app\component\controller\BaseDiyView;

/**
 * 拼团模块·组件
 *
 */
class Pintuan extends BaseDiyView
{
	
	/**
	 * 设计界面
	 */
	public function design()
	{
		return $this->fetch("pintuan/design.html");
	}
}