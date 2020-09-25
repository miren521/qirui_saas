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

namespace addon\alisms\event;

use addon\alisms\model\Sms;
/**
 * 短信发送
 */
class SendSms
{
    /**
     * 短信发送方式方式及配置
     * @param $param
     * @return array|mixed
     * @throws \Overtrue\EasySms\Exceptions\InvalidArgumentException
     */
	public function handle($param)
	{
	    $sms = new Sms();
	    $res = $sms->send($param);
        return $res;
	}
}