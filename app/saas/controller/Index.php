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
use think\captcha\facade\Captcha as ThinkCaptcha;
use think\facade\Cache;
use think\facade\Session;
use think\facade\View;

/**
 * 首页 控制器
 */
class Index extends BaseSaas
{

    protected $app_module = "saas";

    public function index(){
        $admin_id = Session::get($this->app_module . "." . "uid");

        return view();
    }

    public function menus(){
        $admin_id = Session::get($this->app_module . "." . "uid");
        $menus = json_decode(cookie('adminMenus_'.$admin_id));
        if(!$menus){
            $cate = AuthRule::where('menu_status',1)->order('sort asc')->select()->toArray();
            $menus = Menu::authMenu($cate);
//            cookie('adminMenus_'.$admin_id,json_encode($menus),['expire'=>3600]);
        }
        $href = (string)url('saas/index/main');
        $home = ["href"=>$href,"icon"=>"fa fa-home","title"=>"首页"];
        $logoInfo = ["title"=> "LEMOCMS", "image"=> "/static/admin/images/logo.png", "href"=>"https://www.lemocms.com"];
        $menusinit =['menuInfo'=>$menus,'homeInfo'=>$home,'logoInfo'=>$logoInfo];
        return  json($menusinit);
    }



    /**
     * @return string
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\PDOException
     * 主页面
     */
    public function main(){
//        $version = Db::query('SELECT VERSION() AS ver');
//        $version = [0=>1];
        $config = Cache::get('main_config');
        if(!$config){
            $config  = [
                'url'             => $_SERVER['HTTP_HOST'],
                'document_root'   => $_SERVER['DOCUMENT_ROOT'],
                'document_protocol'   => $_SERVER['SERVER_PROTOCOL'],
                'server_os'       => PHP_OS,
                'server_port'     => $_SERVER['SERVER_PORT'],
                'server_ip'       => $_SERVER['REMOTE_ADDR'],
                'server_soft'     => $_SERVER['SERVER_SOFTWARE'],
                'server_file'     => $_SERVER['SCRIPT_FILENAME'],
                'php_version'     => PHP_VERSION,
//                'mysql_version'   => $version[0]['ver'],
                'max_upload_size' => ini_get('upload_max_filesize'),
            ];
            Cache::set('main_config',$config,3600);
        }

        View::assign('config', $config);

        return view();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
//        halt(1);
//        session('admin',null);
//        Session::clear();
        $this->success(lang('logout success'), 'saas/login/login');
    }

}
