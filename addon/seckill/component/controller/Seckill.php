<?php

namespace addon\seckill\component\controller;

use app\component\controller\BaseDiyView;

/**
 * 秒杀模块·组件
 *
 */
class Seckill extends BaseDiyView
{
	
	/**
	 * 设计界面
	 */
	public function design()
	{
		return $this->fetch("seckill/design.html");
	}
}