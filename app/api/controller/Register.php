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


namespace app\api\controller;

use app\model\member\Config;
use app\model\member\Register as RegisterModel;

class Register extends BaseApi
{
	/**
	 * 注册设置
	 */
	public function config()
	{
		$register = new Config();
		$info = $register->getRegisterConfig();
		return $this->response($info);
	}
	
	/**
	 * 注册协议
	 */
	public function aggrement()
	{
		$register = new Config();
		$info = $register->getRegisterDocument();
		return $this->response($info);
	}
	
	/**
	 * 用户名密码注册
	 */
	public function username()
	{
		$register = new RegisterModel();
		$exist = $register->usernameExist($this->params['username']);
		if ($exist) {
			return $this->response($this->error("", "用户名已存在"));
		} else {
			// 校验验证码
			$captcha = new Captcha();
			$check_res = $captcha->checkCaptcha();
			if ($check_res['code'] < 0) return $this->response($check_res);
			
			$res = $register->usernameRegister($this->params);
			//生成access_token
			if ($res['code'] >= 0) {
				$token = $this->createToken($res['data']);
				return $this->response($this->success([ 'token' => $token ]));
			}
			return $this->response($res);
		}
		
	}
	
	/**
	 * 检测存在性
	 */
	public function exist()
	{
		$type = $this->params['type'];
		$register = new RegisterModel();
		switch ($type) {
			case "username" :
				$res = $register->usernameExist($this->params['username']);
				break;
			case "email" :
				$res = $register->emailExist($this->params['email']);
				break;
			case "mobile" :
				$res = $register->mobileExist($this->params['mobile']);
				break;
			default:
				$res = 0;
				break;
		}
		if ($res) {
			return $this->response($this->error("", "账户已存在"));
		} else {
			return $this->response($this->success());
		}
	}
	
}