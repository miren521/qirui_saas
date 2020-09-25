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

use addon\membersignin\model\Signin;

/**
 * 会员账户变化规则
 */
class MemberAccountRule
{
    
	public function handle($data)
	{
	    $config = new Signin();
	    $info = $config->getConfig();
	    $return  = '';
        if($data['account'] == 'point')
        {
            if($info['data']['is_use'] == 1)
            {
                foreach($info['data']['value'] as $v){

                    $return  .= "会员签到".$v['day']."天，赠送".$v['point']."积分；";
                }
            }
        }
        if($data['account'] == 'growth')
        {
            if($info['data']['is_use'] == 1)
            {
                foreach($info['data']['value'] as $v){

                    $return .= "会员签到".$v['day']."天，赠送".$v['growth']."成长值；";
                }
            }
        }
        return isset($return) ? $return : '';
	    
	}
}