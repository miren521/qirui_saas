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


namespace app\shop\controller;

use app\Controller;
use app\model\shop\Config;
use app\model\system\User as UserModel;
use app\model\web\Config as ConfigModel;
use app\model\web\WebSite as WebsiteModel;
use think\captcha\facade\Captcha as ThinkCaptcha;
use think\facade\Cache;

/**
 * 登录
 * Class Login
 * @package app\shop\controller
 */
class Login extends Controller
{
	
	protected $app_module = "shop";
	
	/**
	 * 登录首页
	 * @return mixed
	 */
	public function login()
	{
		$config_model = new ConfigModel();
		$config_info = $config_model->getCaptchaConfig();
		$config = $config_info['data']['value'];
		$this->assign('shop_login', $config['shop_login']);
		if (request()->isAjax()) {
			$username = input("username", '');
			$password = input("password", '');
			$user_model = new UserModel();
			if ($config["shop_login"] == 1) {
				$captcha_result = $this->checkCaptcha();
				//验证码
				if ($captcha_result["code"] != 0) {
					return $captcha_result;
				}
			}
			$result = $user_model->login($username, $password, $this->app_module);
			return $result;
		} else {
			$this->assign("menu_info", [ 'title' => "登录" ]);
			$this->assign("shop_info", [ 'site_name' => "店铺端" ]);
			$config = new Config();
			//广告详情 广告最多三张
			$advertisement = $config->getShopJoinAdvConfig();
			$this->assign('advertisement', $advertisement['data']['value']);
			
			//入驻指南
			$guide = $config->getShopJoinGuide();
			$this->assign('guide', $guide);
			
			//平台配置信息
			$website_model = new WebsiteModel();
			$website_info = $website_model->getWebSite([ [ 'site_id', '=', 0 ] ], 'web_phone,web_email,web_qrcode,web_qq,web_weixin,logo');
			$this->assign('website_info', $website_info['data']);
			$this->assign('domain', $_SERVER['SERVER_NAME']);
			// 验证码
			$captcha = $this->captcha();
			$captcha = $captcha['data'];
			$this->assign("captcha", $captcha);
            //加载版权信息
            $copyright = $config_model->getCopyright();
            $this->assign('copyright', $copyright['data']['value']);
			return $this->fetch("login/login");
		}
		
	}
	
	/**
	 * 退出操作
	 */
	public function logout()
	{
		$user_model = new UserModel();
		$uid = $user_model->uid($this->app_module);
		if ($uid > 0) {
			//清除登录信息session
			$user_model->clearLogin($this->app_module);
			$this->redirect(url("shop/login/login"));
		} else {
			$this->redirect(url("shop/login/login"));
		}
	}
	
	/**
	 * 验证码
	 */
	public function captcha()
	{
		$captcha_data = ThinkCaptcha::create(null, true);
		$captcha_id = md5(uniqid(null, true));
		// 验证码10分钟有效
		Cache::set($captcha_id, $captcha_data['code'], 600);
		return success(0, '', [ 'id' => $captcha_id, 'img' => $captcha_data['img'] ]);
	}
	
	/**
	 * 验证码验证
	 */
	public function checkCaptcha()
	{
		$captcha = input('captcha', '');
		$captcha_id = input('captcha_id', '');
		
		if (empty($captcha)) return error(-1, '请输入验证码');
		
		$captcha_data = Cache::pull($captcha_id);
		if (empty($captcha_data)) return error('', '验证码已失效');
		
		if ($captcha != $captcha_data) return error(-1, '验证码错误');
		
		return success();
	}
	
	/**
	 * 用户注册
	 */
	public function register()
	{
		$config_model = new ConfigModel();
		$config_info = $config_model->getCaptchaConfig();
		$config = $config_info['data']['value'];
		$this->assign('shop_login', $config['shop_login']);
		if (request()->isAjax()) {
			$data['username'] = trim(input("username", ''));//账户
			$data['password'] = trim(input("password", ''));//密码
			
			if ($config["shop_login"] == 1) {
				$captcha_result = $this->checkCaptcha();
				//验证码
				if ($captcha_result["code"] != 0) {
					return $captcha_result;
				}
			}
			
			$user_model = new UserModel();
			$data['app_module'] = $this->app_module;
			$data['site_id'] = 0;
			$result = $user_model->addUser($data);
			if ($result['code'] == 0) {
				
				$user_model->login($data['username'], $data['password'], $this->app_module);
				if ($result['data'] == 2) {
					return success('0', '帐户已注册，登录成功');
				} else {
					return success('0', '帐户注册成功');
				}
				
			} else {
				return $result;
			}
			
		}
		$this->assign("menu_info", [ 'title' => "注册" ]);
		$this->assign("shop_info", [ 'site_name' => "店铺端" ]);
		
		//平台配置信息
		$website_model = new WebsiteModel();
		$website_info = $website_model->getWebSite([ [ 'site_id', '=', 0 ] ], 'web_phone,web_email,web_qrcode,web_qq,web_weixin,logo');
		$this->assign('website_info', $website_info['data']);
		$this->assign("menu_info", [ 'title' => "账号申请" ]);
		$this->assign("shop_info", [ 'site_name' => "店铺端" ]);
		
		// 验证码
		$captcha = $this->captcha();
		$captcha = $captcha['data'];
		$this->assign("captcha", $captcha);
		return $this->fetch("login/register");
	}
	
	/**
	 * 修改密码
	 * */
	public function modifyPassword()
	{
		if (request()->isAjax()) {
			$user_model = new UserModel();
			$uid = $user_model->uid($this->app_module);
			
			$old_pass = input('old_pass', '');
			$new_pass = input('new_pass', '123456');
			
			$res = $user_model->modifyAdminUserPassword($uid, $old_pass, $new_pass);
			
			return $res;
		}
	}
	
}