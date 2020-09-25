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


namespace addon\alioss\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\alioss\model\Config as ConfigModel;

/**
 * 七牛云上传管理
 */
class Config extends BaseAdmin
{
	
	/**
	 * 云上传配置
	 * @return mixed
	 */
	public function config()
	{
		$config_model = new ConfigModel();
		if (request()->isAjax()) {
			$bucket = input("bucket", "");
			$access_key_id = input("access_key_id", "");
			$access_key_secret = input("access_key_secret", "");
			$endpoint = input("endpoint", "");
                $domain = input("domain", "");
                $is_domain = input("is_domain", 0);


			$status = input("status", 0);
			$data = array(
				"bucket" => $bucket,
				"access_key_id" => $access_key_id,
				"access_key_secret" => $access_key_secret,
				"endpoint" => $endpoint,
                        "domain" => $domain,
                      "is_domain" => $is_domain
			);
			
			$result = $config_model->setAliossConfig($data, $status);
			return $result;
		} else {
			$info_result = $config_model->getAliossConfig();
			$info = $info_result["data"];
			$this->assign("info", $info);
			return $this->fetch("config/config");
		}
	}
}