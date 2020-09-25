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

namespace addon\alioss\event;

/**
 * 云上传方式
 */
class OssType
{
	/**
	 * 短信发送方式方式及配置
	 */
	public function handle()
	{
	    $info = array(
	        "sms_type" => "alioss",
            "sms_type_name" => "阿里云上传",
            "edit_url" => "alioss://admin/config/config",
            "desc" => "阿里云上传"
        );
        return $info;
	}
}