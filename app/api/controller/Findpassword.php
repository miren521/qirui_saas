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

use app\model\member\Member as MemberModel;
use app\model\member\Register as RegisterModel;
use app\model\message\Message;
use think\facade\Cache;

class Findpassword extends BaseApi
{
	
	/**
	 * 邮箱找回密码
	 */
	public function email()
	{
		$register = new RegisterModel();
		$exist = $register->emailExist($this->params['email']);
		if (!$exist) {
			return $this->response($this->error("", "邮箱不存在"));
		} else {
			$key = $this->params['key'];
			$verify_data = Cache::get($key);
			if ($verify_data["email"] == $this->params["email"] && $verify_data["code"] == $this->params["code"]) {
				$member_model = new MemberModel();
				$res = $member_model->resetMemberPassword($this->params["password"], [ [ "email", "=", $this->params['email'] ] ]);
			} else {
				$res = $this->error("", "动态码不正确");
			}
			return $this->response($res);
		}
		
	}
	
	/**
	 * 发送邮箱验证码
	 * @return string
	 * @throws \Exception
	 */
	public function emailCode()
	{
		// 校验验证码
		$captcha = new Captcha();
		$check_res = $captcha->checkCaptcha();
		if ($check_res['code'] < 0) return $this->response($check_res);
		
		$email = $this->params['email'];//注册邮箱号
		$register = new RegisterModel();
		$exist = $register->emailExist($email);
		if (!$exist) {
			return $this->response($this->error("", "邮箱不存在"));
		} else {
			$code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);// 生成4位随机数，左侧补0
			$message_model = new Message();
			$res = $message_model->sendMessage([ "email" => $email, "code" => $code, "support_type" => [ "email" ], "keywords" => "FIND_PASSWORD" ]);
			if ($res["code"] >= 0) {
				//将验证码存入缓存
				$key = 'find_email_code_' . md5(uniqid(null, true));
				Cache::tag("find_email_code")->set($key, [ 'email' => $email, 'code' => $code ], 600);
				return $this->response($this->success([ "key" => $key ]));
			} else {
				return $this->response($res);
			}
		}
	}
	
	/**
	 * 手机号找回密码
	 */
	public function mobile()
	{
		$register = new RegisterModel();
		$exist = $register->mobileExist($this->params['mobile']);
		if (!$exist) {
			return $this->response($this->error("", "手机号不存在"));
		} else {
			$key = $this->params['key'];
			$verify_data = Cache::get($key);
			if ($verify_data["mobile"] == $this->params["mobile"] && $verify_data["code"] == $this->params["code"]) {
				$member_model = new MemberModel();
				$res = $member_model->resetMemberPassword($this->params["password"], [ [ "mobile", "=", $this->params['mobile'] ] ]);
			} else {
				$res = $this->error("", "手机动态码不正确");
			}
			return $this->response($res);
		}
		
	}
	
	/**
	 * 短信验证码
	 */
	public function mobileCode()
	{
		// 校验验证码
		$captcha = new Captcha();
		$check_res = $captcha->checkCaptcha();
		if ($check_res['code'] < 0) return $this->response($check_res);
		
		$mobile = $this->params['mobile'];//注册手机号
		$register = new RegisterModel();
		$exist = $register->mobileExist($mobile);
		if (!$exist) {
			return $this->response($this->error("", "手机号不存在"));
		} else {
			$code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);// 生成4位随机数，左侧补0
			$message_model = new Message();
			$res = $message_model->sendMessage([ "mobile" => $mobile, "code" => $code, "support_type" => [ "sms" ], "keywords" => "FIND_PASSWORD" ]);
			if ($res["code"] >= 0) {
				//将验证码存入缓存
				$key = 'find_mobile_code_' . md5(uniqid(null, true));
				Cache::tag("find_mobile_code")->set($key, [ 'mobile' => $mobile, 'code' => $code ], 600);
				return $this->response($this->success([ "key" => $key ]));
			} else {
				return $this->response($res);
			}
		}
	}
}