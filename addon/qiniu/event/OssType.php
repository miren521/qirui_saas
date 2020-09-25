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

namespace addon\qiniu\event;

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
	        "sms_type" => "qiniu",
            "sms_type_name" => "七牛云上传",
            "edit_url" => "qiniu://admin/config/config",
            "desc" => "七牛云上传,七牛提供10GB免费存储空间"
        );
        return $info;
	}
}