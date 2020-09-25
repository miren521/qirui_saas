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



namespace addon\manjian\event;

/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    
	public function handle($data)
	{
	    $from_type = [
            'point' => [
                'manjian' => [
                    'type_name' => '满减送',
                    'type_url' => '',
                ],
            ]
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