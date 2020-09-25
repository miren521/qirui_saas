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


namespace app\shopapi\controller;

use app\model\system\User as UserModel;

class Login extends BaseApi
{
    protected $app_module = "shop";

	/**
	 * 登录方法
	 */
	public function login()
	{
		// 校验验证码
		$captcha = new Captcha();
		$check_res = $captcha->checkCaptcha();
		if ($check_res['code'] < 0) return $this->response($check_res);

		// 登录
		$login = new UserModel();
        if (empty($this->params["username"])) return $this->response($this->error([], "商家账号不能为空!"));
		if (empty($this->params["password"])) return $this->response($this->error([], "密码不可为空!"));

		$res = $login->appLogin($this->params['username'],$this->params["password"],$this->app_module);

		//生成access_token
		if ($res['code'] >= 0) {
			$token = $this->createToken($res['data']);
			return $this->response($this->success([ 'token' => $token ,'site_id' => $res['data']['site_id']]));
		}
		return $this->response($res);
	}



}