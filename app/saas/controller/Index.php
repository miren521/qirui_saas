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

namespace app\saas\controller;

use app\common\lib\Menu;
use app\model\saas\AuthRule;
use app\model\system\User as UserModel;
use app\model\web\Config as ConfigModel;
use lemo\helper\SignHelper;
use think\captcha\facade\Captcha as ThinkCaptcha;
use think\facade\Cache;
use think\facade\Cookie;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use app\model\agent\Agent;


/**
 * 首页 控制器
 */
class Index extends BaseSaas
{

    protected $app_module = "saas";

    public function index()
    {
        $admin_id = Session::get($this->app_module . "." . "uid");

        return view();
    }

    public function menus()
    {
        $admin_id = Session::get($this->app_module . "." . "uid");
        $menus = json_decode(Cache::get('adminMenus_' . $admin_id));
        if (!$menus) {
            $cate = AuthRule::where('menu_status', 1)->order('sort asc')->select()->toArray();
            $menus = Menu::authMenu($cate);
            Cache::set('adminMenus_' . $admin_id, json_encode($menus), 3600);
        }
        $href = (string)url('saas/index/main');
        $home = ["href" => $href, "icon" => "fa fa-home", "title" => "首页"];
        $logoInfo = ["title" => "LEMOCMS", "image" => "/static/admin/images/logo.png", "href" => "https://www.lemocms.com"];
        $menusinit = ['menuInfo' => $menus, 'homeInfo' => $home, 'logoInfo' => $logoInfo];
        return json($menusinit);
    }


    /**
     * @return string
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\PDOException
     * 主页面
     */
    public function main()
    {
//        $version = Db::query('SELECT VERSION() AS ver');
//        $version = [0=>1];
        $config = Cache::get('main_config');
        if (!$config) {
            $config = [
                'url' => $_SERVER['HTTP_HOST'],
                'document_root' => $_SERVER['DOCUMENT_ROOT'],
                'document_protocol' => $_SERVER['SERVER_PROTOCOL'],
                'server_os' => PHP_OS,
                'server_port' => $_SERVER['SERVER_PORT'],
                'server_ip' => $_SERVER['REMOTE_ADDR'],
                'server_soft' => $_SERVER['SERVER_SOFTWARE'],
                'server_file' => $_SERVER['SCRIPT_FILENAME'],
                'php_version' => PHP_VERSION,
//                'mysql_version'   => $version[0]['ver'],
                'max_upload_size' => ini_get('upload_max_filesize'),
            ];
            Cache::set('main_config', $config, 3600);
        }

        View::assign('config', $config);

        return view();
    }

    /*
     * 清除缓存 出去session缓存
     */
    public function clearData()
    {
//        $dir = config('admin.clear_cache_dir') ? app()->getRootPath().'runtime/admin' : app()->getRootPath().'runtime';
        Cache::clear();
//        if(FileHelper::delDir($dir) ){
        return retMsg(200, '清理成功');
//        }
    }

    /*
     * 修改密码
     */
    public function password()
    {
        if (!Request::isPost()) {

            return View::fetch('login/password');

        } else {
            if (Request::isPost() and Session::get('admin.id') === 3) {
                $this->error(lang('test data cannot edit'));
            }

            $data = Request::post();
            $oldpassword = Request::post('oldpassword', '123456', 'lemo\helper\StringHelper::filterWords');
            $admin = \app\model\agent\Agent::find($data['id']);
            if (!password_verify($oldpassword, $admin['password'])) {
                $this->error(lang('origin password error'));
            }
            $password = Request::post('password', '123456', 'lemo\helper\StringHelper::filterWords');
            try {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT, SignHelper::passwordSalt());

                if (Session::get('admin.id') == 1) {
                    Agent::update($data);
                } elseif (Session::get('admin.id') == $data['id']) {
                    Agent::update($data);
                } else {
                    $this->error(lang('permission denied'));
                }

            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            return retMsg(200, '成功');

        }
    }


}
