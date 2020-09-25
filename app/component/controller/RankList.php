<?php

namespace app\component\controller;

/**
 * 店铺排行榜·组件
 */
class RankList extends BaseDiyView
{
	/**
	 * 后台编辑界面
	 */
	public function design()
	{
		return $this->fetch("rank_list/design.html");
	}
}