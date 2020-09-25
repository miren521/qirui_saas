<?php
/**
 * KirySaaS--------||bai T o o Y ||
 * =========================================================
 * ----------------------------------------------
 * User Mack Qin
 * Copy right 2019-2029 kiry 保留所有权利。
 * ----------------------------------------------
 * =========================================================
 */


namespace addon\egg\event;
use app\model\games\Games;

/**
 * 关闭活动
 */
class CloseEgg
{

	public function handle($params)
	{
	    $games = new Games();
	    $res = $games->cronCloseGames($params['relate_id']);
        return $res;
	}
}