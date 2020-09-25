<?php

namespace addon\live\component\controller;

use app\component\controller\BaseDiyView;

/**
 * 小程序直播·组件
 *
 */
class LiveInfo extends BaseDiyView
{
	
	/**
	 * 设计界面
	 */
	public function design()
	{
		return $this->fetch("live_info/design.html");
	}
}