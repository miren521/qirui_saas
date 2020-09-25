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


namespace addon\city\city\controller;

use app\model\system\User as UserModel;
use app\Controller;
use app\model\web\WebSite as WebsiteModel;
use think\captcha\facade\Captcha as ThinkCaptcha;
use think\facade\Cache;
use app\model\shop\Config;
use app\model\web\Config as ConfigModel;
/**
 * 登录
 * Class Login
 * @package app\shop\controller
 */
class Login extends Controller
{

    protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
    protected $app_module = "city";
    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->replace = [
            'CITY_CSS' => __ROOT__. '/addon/city/city/view/public/css',
            'CITY_JS' => __ROOT__. '/addon/city/city/view/public/js',
            'CITY_IMG' => __ROOT__. '/addon/city/city/view/public/img',
        ];
    }
    /**
     * 登录首页
     * @return mixed
     */
	public function login()
	{
        if(request()->isAjax()){
            $username = input("username", '');
            $password = input("password", '');
            $user_model = new UserModel();
            $captcha_result = $this->checkCaptcha();
                //验证码
                if ($captcha_result["code"] != 0) {
                    return $captcha_result;
                }
            $result = $user_model->login($username, $password, $this->app_module);
            if($result['code'] == 0){
                //查询站点状态
                $site_id = $user_model->userInfo($this->app_module);
                $website_model = new WebsiteModel();
                $website_info = $website_model->getWebSite([ ['site_id','=',$site_id['site_id']] ],'status');
                if($website_info['data']['status'] == -1){
                    //清除登录信息session
                    $user_model->clearLogin($this->app_module);
                    return error('-1','分站已关闭，请联系平台管理员');
                }
            }
            return $result;
        }else{
            $this->assign("menu_info",['title' => "登录"]);
            $this->assign("city_info",['site_name' => "城市分站端"]);

            //平台配置信息
            $website_model = new WebsiteModel();
            $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'web_phone,web_email,web_qrcode,web_qq,web_weixin,logo');
            $this->assign('website_info',$website_info['data']);
            $this->assign('domain', $_SERVER['SERVER_NAME']);

            $captcha = $this->captcha();
            $captcha = $captcha['data'];
            $this->assign("captcha", $captcha);
            //加载版权信息
            $config_model = new ConfigModel();
            $copyright = $config_model->getCopyright();
            $this->assign('copyright', $copyright['data']['value']);
            return $this->fetch("login/login", [], $this->replace);
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
            $this->redirect(addon_url("city://city/login/login"));
        }else{
            $this->redirect(addon_url("city://city/login/login"));
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

        if (empty($captcha)) return error(-1, '请输入验证码');
        if (!captcha_check($captcha)) return error(-1, '验证码错误');

        return success();
    }
    
    /**
     * 修改密码
     * */
    public function modifyPassword()
    {
        if(request()->isAjax()){
            $user_model = new UserModel();
            $uid = $user_model->uid($this->app_module);

            $old_pass = input('old_pass', '');
            $new_pass = input('new_pass', '123456');

            $res = $user_model->modifyAdminUserPassword($uid, $old_pass, $new_pass);

            return $res;
        }
    }

    /**
     * 清理缓存
     */
    public function clearCache()
    {
        Cache::clear();
        return success('', '缓存更新成功', '');
    }

}