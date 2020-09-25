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

declare (strict_types = 1);

namespace addon\membersignin\event;

/**
 * 会员操作
 */
class MemberAction
{
	/**
	 * 会员操作
	 */
	public function handle($data)
	{
	    if($data['member_action'] == 'memberregister')
	    {
	        return success();
	    }
	    return '';
	    
	}
}