<?php
namespace app\saas\controller;

use app\Controller;
use app\model\system\User as UserModel;
use app\model\web\Config as ConfigModel;
use think\captcha\facade\Captcha as ThinkCaptcha;
use think\facade\Cache;
use app\model\agent\Agent;
use think\facade\Session;

class Login extends Controller
{
    protected $app_module = "saas";

    public function index(){
        return view();
    }

    public function login(){
        $config_model = new ConfigModel();
        $config_info = $config_model->getCaptchaConfig();
        $config = $config_info['data']['value'];

        $this->assign('admin_login', $config['admin_login']);
        if (request()->isAjax()) {
            $username = input('username', '');
            $password = input('password', '');

            if ($config["admin_login"] == 1) {
                $captcha_result = $this->checkCaptcha();
                //验证码
                if ($captcha_result["code"] != 0) {
                    return $captcha_result;
                }
            }

            $agent = new Agent();
            $res = $agent->login($username, $password, $this->app_module);
            return $res;
        } else {
            $this->assign("menu_info", [ 'title' => "登录" ]);
            $this->assign("config", $config);
            // 验证码
            $captcha = $this->captcha();
            $captcha = $captcha['data'];
            $this->assign("captcha", $captcha);
            //加载版权信息
            $copyright = $config_model->getCopyright();
            $this->assign('copyright', $copyright['data']['value']);

            return $this->fetch('login/index');
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
     * 退出登录
     */
    public function logOut(){
        Session::delete($this->app_module . "." . "user_info");
        Session::delete($this->app_module . "." . "uid");
        redirect('/saas/login/login.html')->send();
    }
}
