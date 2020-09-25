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
 * 启动活动
 */
class OpenTopic
{

	public function handle($params)
	{
	    $topic = new Topic();
	    $res= $topic->cronOpenTopic($params['relate_id']);
        return $res;
	}
}