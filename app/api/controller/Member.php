<?php

/**
 * Member.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

namespace app\api\controller;

use addon\coupon\model\MemberCoupon;
use addon\platformcoupon\model\MemberPlatformcoupon;
use app\model\member\Member as MemberModel;
use app\model\member\Register as RegisterModel;
use app\model\message\Message;
use think\facade\Cache;

class Member extends BaseApi
{
	/**
	 * 基础信息
	 */
	public function detail()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$member_model = new MemberModel();
		$info = $member_model->getMemberInfo(
			[['member_id', '=', $token['data']['member_id']]],
			'member_id,source_member,username,nickname,mobile,email,status,headimg,member_level,member_level_name,member_label,member_label_name,sex,location,birthday,point,balance,balance_money,growth,sign_days_series'
		);

		if (!empty($info['data'])) {
			$info['data']['password'] = empty($info['data']['password']) ? 0 : 1;
		}

		return $this->response($info);
	}

	/**
	 * 基础信息
	 */
	public function info()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$member_model = new MemberModel();
		$info = $member_model->getMemberInfo([['member_id', '=', $token['data']['member_id']]], 'member_id,source_member,username,nickname,mobile,email,password,status,headimg,member_level,member_level_name,member_label,member_label_name,qq,qq_openid,wx_openid,wx_unionid,ali_openid,baidu_openid,toutiao_openid,douyin_openid,realname,sex,location,birthday,point,balance,balance_money,growth,sign_days_series');
		if (!empty($info['data'])) {
			$info['data']['password'] = empty($info['data']['password']) ? 0 : 1;
		}

		return $this->response($info);
	}

	/**
	 * 修改会员头像
	 * @return string
	 */
	public function modifyheadimg()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$headimg = isset($this->params['headimg']) ? $this->params['headimg'] : '';
		$member_model = new MemberModel();
		$res = $member_model->editMember(['headimg' => $headimg], [['member_id', '=', $token['data']['member_id']]]);
		return $this->response($res);
	}

	/**
	 * 修改昵称
	 * @return string
	 */
	public function modifynickname()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$nickname = isset($this->params['nickname']) ? $this->params['nickname'] : '';
		$member_model = new MemberModel();
		$res = $member_model->editMember(['nickname' => $nickname], [['member_id', '=', $token['data']['member_id']]]);
		return $this->response($res);
	}

	/**
	 * 修改手机号
	 * @return string
	 */
	public function modifymobile()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		// 校验验证码
		$captcha = new Captcha();
		$check_res = $captcha->checkCaptcha(false);
		if ($check_res['code'] < 0) return $this->response($check_res);

		$register = new RegisterModel();
		$exist = $register->mobileExist($this->params['mobile']);
		if ($exist) {
			return $this->response($this->error("", "手机号已存在"));
		} else {
			$key = $this->params['key'];
			$verify_data = Cache::get($key);
			if ($verify_data["mobile"] == $this->params["mobile"] && $verify_data["code"] == $this->params["code"]) {
				$mobile = isset($this->params['mobile']) ? $this->params['mobile'] : '';
				$member_model = new MemberModel();
				$res = $member_model->editMember(['mobile' => $mobile], [['member_id', '=', $token['data']['member_id']]]);
			} else {
				$res = $this->error("", "验证码不正确");
			}
			return $this->response($res);
		}
	}

	/**
	 * 修改邮箱
	 * @return string
	 */
	public function modifyemail()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		// 校验验证码
		$captcha = new Captcha();
		$check_res = $captcha->checkCaptcha(false);
		if ($check_res['code'] < 0) return $this->response($check_res);
		$register = new RegisterModel();
		$exist = $register->emailExist($this->params['email']);
		if ($exist) {
			return $this->response($this->error("", "邮箱已存在"));
		} else {
			$key = $this->params['key'];
			$verify_data = Cache::get($key);
			if ($verify_data["email"] == $this->params["email"] && $verify_data["code"] == $this->params["code"]) {
				$email = isset($this->params['email']) ? $this->params['email'] : '';
				$member_model = new MemberModel();
				$res = $member_model->editMember(['email' => $email], [['member_id', '=', $token['data']['member_id']]]);
			} else {
				$res = $this->error("", "验证码不正确");
			}
			return $this->response($res);
		}
	}

	/**
	 * 修改密码
	 * @return string
	 */
	public function modifypassword()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$old_password = isset($this->params['old_password']) ? $this->params['old_password'] : '';
		$new_password = isset($this->params['new_password']) ? $this->params['new_password'] : '';

		$member_model = new MemberModel();
		$info = $member_model->getMemberInfo([['member_id', '=', $token['data']['member_id']]], 'password');
		// 未设置密码时设置密码需验证身份
		if (empty($info['data']['password'])) {
			$key = $this->params['key'] ?? '';
			$code = $this->params['code'] ?? '';
			$verify_data = Cache::get($key);
			if (empty($verify_data) || $verify_data["code"] != $code) {
				return $this->response($this->error("", "手机验证码不正确"));
			}
		}
		$res = $member_model->modifyMemberPassword($token['data']['member_id'], $old_password, $new_password);

		return $this->response($res);
	}


	/**
	 * 绑定短信验证码
	 */
	public function bindmobliecode()
	{
		// 校验验证码
		$captcha = new Captcha();
		$check_res = $captcha->checkCaptcha(false);
		if ($check_res['code'] < 0) return $this->response($check_res);

		$mobile = $this->params['mobile']; //注册手机号
		$register = new RegisterModel();
		$exist = $register->mobileExist($mobile);
		if ($exist) {
			return $this->response($this->error("", "当前手机号已存在"));
		} else {
			$code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT); // 生成4位随机数，左侧补0
			$message_model = new Message();
			$res = $message_model->sendMessage(["mobile" => $mobile, "code" => $code, "support_type" => ["sms"], "keywords" => "MEMBER_BIND"]);
			if ($res["code"] >= 0) {
				//将验证码存入缓存
				$key = 'bind_mobile_code_' . md5(uniqid(null, true));
				Cache::tag("bind_mobile_code")->set($key, ['mobile' => $mobile, 'code' => $code], 600);
				return $this->response($this->success(["key" => $key]));
			} else {
				return $this->response($res);
			}
		}
	}

	/**
	 * 邮箱绑定验证码
	 */
	public function bingemailcode()
	{
		// 校验验证码
		$captcha = new Captcha();
		$check_res = $captcha->checkCaptcha(false);
		if ($check_res['code'] < 0) return $this->response($check_res);

		$email = $this->params['email']; //注册邮箱号
		$register = new RegisterModel();
		$exist = $register->emailExist($email);
		if ($exist) {
			return $this->response($this->error("", "当前邮箱已存在"));
		} else {
			$code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT); // 生成4位随机数，左侧补0
			$message_model = new Message();
			$res = $message_model->sendMessage(["email" => $email, "code" => $code, "support_type" => ["email"], "keywords" => "MEMBER_BIND"]);
			if ($res["code"] >= 0) {
				//将验证码存入缓存
				$key = 'bind_email_code_' . md5(uniqid(null, true));
				Cache::tag("bind_email_code")->set($key, ['email' => $email, 'code' => $code], 600);
				return $this->response($this->success(["key" => $key]));
			} else {
				return $this->response($res);
			}
		}
	}

	/**
	 * 设置密码时获取验证码
	 */
	public function pwdmobliecode()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		// 校验验证码
		$captcha = new Captcha();
		$check_res = $captcha->checkCaptcha(false);
		if ($check_res['code'] < 0) return $this->response($check_res);

		$member_model = new MemberModel();
		$info = $member_model->getMemberInfo([['member_id', '=', $token['data']['member_id']]], 'mobile');
		if (empty($info['data'])) return $this->response($this->error([], '未获取到会员信息！'));
		if (empty($info['data']['mobile'])) return $this->response($this->error([], '会员信息尚未绑定手机号！'));

		$mobile = $info['data']['mobile'];

		$code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT); // 生成4位随机数，左侧补0
		$message_model = new Message();
		$res = $message_model->sendMessage(["mobile" => $mobile, "code" => $code, "support_type" => ["sms"], "keywords" => "SET_PASSWORD"]);
		if (isset($res["code"]) && $res["code"] >= 0) {
			//将验证码存入缓存
			$key = 'password_mobile_code_' . md5(uniqid(null, true));
			Cache::tag("password_mobile_code_")->set($key, ['mobile' => $mobile, 'code' => $code], 600);
			return $this->response($this->success(["key" => $key, 'code' => $code]));
		} else {
			return $this->response($this->error('', '发送失败'));
		}
	}

	/**
	 * 验证邮箱
	 * @return string
	 */
	public function checkemail()
	{
		$email = isset($this->params['email']) ? $this->params['email'] : '';
		if (empty($email)) {
			return $this->response($this->error('', 'REQUEST_EMAIL'));
		}
		$member_model = new MemberModel();
		$condition = [
			['email', '=', $email]
		];
		$res = $member_model->getMemberCount($condition);
		if ($res['data'] > 0) {
			return $this->response($this->error('', '当前邮箱已存在'));
		}
		return $this->response($this->success());
	}

	/**
	 * 验证手机号
	 * @return string
	 */
	public function checkmobile()
	{
		$mobile = isset($this->params['mobile']) ? $this->params['mobile'] : '';
		if (empty($mobile)) {
			return $this->response($this->error('', 'REQUEST_MOBILE'));
		}
		$member_model = new MemberModel();
		$condition = [
			['mobile', '=', $mobile]
		];
		$res = $member_model->getMemberCount($condition);
		if ($res['data'] > 0) {
			return $this->response($this->error('', '当前手机号已存在'));
		}
		return $this->response($this->success());
	}

	/**
	 * 修改支付密码
	 * @return string
	 */
	public function modifypaypassword()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$key = $this->params['key'] ?? '';
		$code = $this->params['code'] ?? '';
		$password = isset($this->params['password']) ? trim($this->params['password']) : '';
		if (empty($password)) return $this->response($this->error('', '支付密码不可为空'));

		$verify_data = Cache::get($key);
		if ($verify_data["code"] == $this->params["code"]) {
			$member_model = new MemberModel();
			$res = $member_model->modifyMemberPayPassword($token['data']['member_id'], $password);
		} else {
			$res = $this->error("", "验证码不正确");
		}
		return $this->response($res);
	}

	/**
	 * 检测会员是否设置支付密码
	 */
	public function issetpayaassword()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$member_model = new MemberModel();
		$res = $member_model->memberIsSetPayPassword($this->member_id);
		return $this->response($res);
	}

	/**
	 * 检测支付密码是否正确
	 */
	public function checkpaypassword()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$password = isset($this->params['pay_password']) ? trim($this->params['pay_password']) : '';
		if (empty($password)) return $this->response($this->error('', '支付密码不可为空'));

		$member_model = new MemberModel();
		$res = $member_model->checkPayPassword($this->member_id, $password);
		return $this->response($res);
	}


	/**
	 * 修改支付密码发送手机验证码
	 */
	public function paypwdcode()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);

		$code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT); // 生成4位随机数，左侧补0
		$message_model = new Message();
		$res = $message_model->sendMessage(["member_id" => $this->member_id, "code" => $code, "support_type" => ["sms"], "keywords" => "MEMBER_PAY_PASSWORD"]);
		if ($res["code"] >= 0) {
			//将验证码存入缓存
			$key = 'pay_password_code_' . md5(uniqid(null, true));
			Cache::tag("pay_password_code")->set($key, ['member_id' => $this->member_id, 'code' => $code], 600);
			return $this->response($this->success(["key" => $key]));
		} else {
			return $this->response($res);
		}
	}

	/**
	 * 验证修改支付密码动态码
	 */
	public function verifypaypwdcode()
	{
		$key = isset($this->params['key']) ? trim($this->params['key']) : '';

		$verify_data = Cache::get($key);
		if ($verify_data["code"] == $this->params["code"]) {
			$res = $this->success([]);
		} else {
			$res = $this->error("", "验证码不正确");
		}
		return $this->response($res);
	}

	/**
	 * 通过token得到会员id
	 */
	public function id()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		return $this->response($this->success($this->member_id));
	}

	/**
	 * 账户奖励规则说明
	 * @return false|string
	 */
	public function accountrule()
	{
		//积分
		$point = event('MemberAccountRule', ['account' => 'point']);

		//余额
		$balance = event('MemberAccountRule', ['account' => 'balance']);

		//成长值
		$growth = event('MemberAccountRule', ['account' => 'growth']);

		$res = [
			'point' => $point,
			'balance' => $balance,
			'growth' => $growth
		];

		return $this->response($this->success($res));
	}

	/**
	 * 拉取会员头像
	 */
	public function pullhaedimg()
	{
		$member_id = input('member_id', '');
		$member = new MemberModel();
		$member->pullHeadimg($member_id);
	}

    /**
     * 统计会员优惠券
     */
	public function couponnum(){
	    //优惠券总和为  店铺优惠券和平台优惠券的总和
          $token = $this->checkToken();
          if ($token['code'] < 0) return $this->response($token);

          $state = $this->params['state'] ?? 1;
          $coupon_model = new MemberCoupon();
          $coupon_result = $coupon_model->getMemberCouponNum($token['data']['member_id'], $state);
          $coupon_num = $coupon_result['data'];

          $platformcoupon_model = new MemberPlatformcoupon();
          $plarform_result = $platformcoupon_model->getMemberPlatformcouponNum($token['data']['member_id'], $state);
          $platform_num = $plarform_result['data'];
          return $this->response($coupon_model->success($coupon_num+$platform_num));
      }
}
