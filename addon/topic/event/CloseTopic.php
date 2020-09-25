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



namespace addon\topic\event;
use addon\topic\model\Topic;

/**
 * 关闭活动
 */
class CloseTopic
{

	public function handle($params)
	{
	    $topic = new Topic();
	    $res = $topic->cronCloseTopic($params['relate_id']);
        return $res;
	}
}