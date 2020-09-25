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

namespace addon\memberregister\event;

/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    
	public function handle($data)
	{
    	$from_type = [
    		'balance' => [
    			'register' => [
    				'type_name' => '注册',
    				'type_url' => '',
    			],
    		],
    		'point' => [
    			'register' => [
    				'type_name' => '注册',
    				'type_url' => '',
    			],
    		],
    		'growth' => [
    			'register' => [
    				'type_name' => '注册',
    				'type_url' => '',
    			],
    		],
    	
    	];
        if($data == ''){
            return $from_type;
        }else{
            if(isset($from_type[$data])){
                return $from_type[$data];
            }else{
                return [];
            }
        }
	    
	}
}