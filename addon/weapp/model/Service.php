<?php

namespace addon\weapp\model;

use app\model\BaseModel;

class Service extends BaseModel
{
	
	private $url = "https://www.niushop.com.cn";
	private $cert;
	
	public function __construct()
	{
		if (file_exists('cert.key')) {
			$this->cert = file_get_contents('cert.key');
		}
	}
	
	/**
	 * 获取已经购买的小程序
	 */
	public function getPurchasedApplet()
	{
		$url = $this->url . "/auth/Applet/getMemberAppletList";
		$data = [
			'cert' => $this->cert
		];
		$res = $this->doPost($url, $data);
		return json_decode($res, true);
	}
	
	/**
	 * 获取小程序版本列表
	 * @param unknown $mark
	 */
	public function getAppletVersionList($mark)
	{
		$url = $this->url . "/auth/Applet/getAppletVersionList";
		$data = [
			'cert' => $this->cert,
			'applet_module_mark' => $mark
		];
		$res = $this->doPost($url, $data);
		return json_decode($res, true);
	}
	
	/**
	 * 获取小程序包数据
	 * @param unknown $params
	 */
	public function getAppletVersionUpgradeInfo($params)
	{
		$url = $this->url . "/auth/Applet/getAppletVersionUpgradeInfo";
		$data = [
			'cert' => $this->cert,
			'applet_module_mark' => $params['mark'],
			'last_version_release' => $params['release'],
			'type' => $params['type']
		];
		$res = $this->doPost($url, $data);
		return json_decode($res, true);
	}
	
	/**
	 * 小程序下载
	 * @param unknown $token
	 */
	public function download($token)
	{
		$url = $this->url . "/auth/Applet/download?token=" . $token;
		return $url;
	}
	
	/**
	 * post 服务器请求
	 */
	private function doPost($post_url, $post_data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		if ($post_data != '' && !empty($post_data)) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}