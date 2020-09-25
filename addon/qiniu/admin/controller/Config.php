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


namespace addon\qiniu\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\qiniu\model\Config as ConfigModel;

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
			$access_key = input("access_key", "");
			$secret_key = input("secret_key", "");
			$domain = input("domain", "");
			$status = input("status", 0);
			$data = array(
				"bucket" => $bucket,
				"access_key" => $access_key,
				"secret_key" => $secret_key,
				"domain" => $domain,
			);
			
			$result = $config_model->setQiniuConfig($data, $status);
			return $result;
		} else {
			$info_result = $config_model->getQiniuConfig();
			$info = $info_result["data"];
			$this->assign("info", $info);
			return $this->fetch("config/config");
		}
	}
}