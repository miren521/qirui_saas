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

use addon\alisms\model\Config;

/**
 * 短信方式  (后台调用)
 */
class SmsType
{
	/**
	 * 短信发送方式方式及配置
	 */
	public function handle()
	{
	    $info = array(
	        "sms_type" => "alisms",
            "sms_type_name" => "阿里云短信",
            "edit_url" => "alisms://admin/sms/config",
            "desc" => "阿里云短信服务（Short Message Service）支持国内和国际快速发送验证码、短信通知和推广短信，服务范围覆盖全球200多个国家和地区。国内短信支持三网合一专属通道，与工信部携号转网平台实时互联。电信级运维保障，实时监控自动切换，到达率高达99%。"
        );

	    $config_model = new Config();
	    $config = $config_model->getSmsConfig();
        $info['status'] = $config['data']['value']['status'] ?? 0;

        return $info;
	}
}