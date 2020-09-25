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

/**
 * 会员账户变化来源类型
 */
class MemberAccountFromType
{
    public function handle($data)
    {
        $from_type = [
            'point' => [
                'egg' => ['type_name' => '砸金蛋', 'type_url' => ''],
            ],
            'balance' => [
                'egg' => ['type_name' => '砸金蛋', 'type_url' => ''],
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