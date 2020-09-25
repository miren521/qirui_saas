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

// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
declare (strict_types = 1);

namespace app\event;
use app\model\member\Member;


/**
 * 绑定发送验证码
 */
class MessagePayPasswordCode
{

	public function handle($param)
	{
	    //发送订单消息
        if($param["keywords"] == "MEMBER_PAY_PASSWORD"){
            $member_model = new Member();
            $result = $member_model->paypasswordCode($param);
            return $result;
        }

	}
	
}