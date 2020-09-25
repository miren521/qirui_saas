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


namespace addon\alisms\model;

use app\model\BaseModel;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\InvalidArgumentException;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use Overtrue\EasySms\Strategies\OrderStrategy;

/**
 * 阿里云短信
 */
class Sms extends BaseModel
{
	/**
	 * 短信发送
	 * @param array $param
	 * @return array|mixed
	 * @throws InvalidArgumentException
	 */
	public function send($param = [])
	{
		$config_model = new Config();
		$config_result = $config_model->getSmsConfig();
		if ($config_result["data"]["is_use"]) {
			$config = $config_result["data"]["value"];
			$sms_info = $param["message_info"]["sms_json_array"];//消息类型模板 短信模板信息
			if (empty($sms_info["alisms"])) return $this->error([], "消息模板尚未配置");
			
			$sms_info = $sms_info["alisms"];
			$var_parse = $param["var_parse"];//变量解析
			$account = $param["sms_account"];//发送手机号
			//加入阿里云短信配置
			
			$sms_config = [
				// HTTP 请求的超时时间（秒）
				'timeout' => 5.0,
				// 默认发送配置
				'default' => [
					// 网关调用策略，默认：顺序调用
					'strategy' => OrderStrategy::class,
					// 默认可用的发送网关
					'gateways' => [ 'aliyun' ],
				],
				// 可用的网关配置
				'gateways' => [
					"aliyun" => [
						'access_key_id' => $config["access_key_id"],
						'access_key_secret' => $config["access_key_secret"],
						'sign_name' => $config["smssign"],
					]
				],
			];
			try {
				unset($var_parse['site_name']);
				$easySms = new EasySms($sms_config);
				$easySms->send($account, [
					'template' => $sms_info["template_id"],
					'data' => $var_parse,
				]);
				return $this->success([ "addon" => "alisms", "addon_name" => "阿里云短信", "content" => $sms_info["content"] ]);
			} catch (NoGatewayAvailableException $exception) {
				$message = $exception->getException('aliyun')->getMessage();
				return $this->error([ "content" => $sms_info["content"] ], $message ? : '短信发送异常');
			}
		}
	}
}
