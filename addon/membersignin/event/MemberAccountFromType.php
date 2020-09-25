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
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    
	public function handle($data)
	{
    	$from_type = [
    		'point' => [
    			'signin' => [
    				'type_name' => '签到',
    				'type_url' => '',
    			],
    		],
    		'growth' => [
    			'signin' => [
    				'type_name' => '签到',
    				'type_url' => '',
    			],
    		],
    	];
        if($data == ''){
            return $from_type;
        }else{
            return $from_type[$data];
        }
	    
	}
}