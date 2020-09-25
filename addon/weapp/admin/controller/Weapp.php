<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+
namespace addon\weapp\admin\controller;

use addon\weapp\model\Config as ConfigModel;
use app\admin\controller\BaseAdmin;
use addon\weapp\model\Service;
/**
 * 微信小程序功能设置
 */
class Weapp extends BaseAdmin
{
    protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'WEAPP_CSS' => __ROOT__ . '/addon/weapp/admin/view/public/css',
            'WEAPP_JS' => __ROOT__ . '/addon/weapp/admin/view/public/js',
            'WEAPP_IMG' => __ROOT__ . '/addon/weapp/admin/view/public/img',
            'WEAPP_SVG' => __ROOT__ . '/addon/weapp/admin/view/public/svg',
        ];
    }
    /**
     * 功能设置
     */
    public function setting()
    {
        $weapp_menu =  event('WeappMenu', []);
        $this->assign('weapp_menu', $weapp_menu);
        return $this->fetch('weapp/setting', [], $this->replace);
    }

    /**
     * 公众号配置
     */
    public function config()
    {

        $weapp_model = new ConfigModel();
        if (request()->isAjax()) {
            $weapp_name = input('weapp_name', '');
            $weapp_original = input('weapp_original', '');
            $appid = input('appid', '');
            $appsecret = input('appsecret', '');
            $token = input('token', 'TOKEN');
            $encodingaeskey = input('encodingaeskey', '');
            $is_use = input('is_use', 0);
            $qrcode = input('qrcode','');
            $data = array(
                "appid" => $appid,
                "appsecret" => $appsecret,
                "token" => $token,
                "weapp_name" => $weapp_name,
                "weapp_original" => $weapp_original,
                "encodingaeskey" => $encodingaeskey,
                'qrcode' => $qrcode
            );
            $res = $weapp_model->setWeAppConfig($data, $is_use);
            return $res;
        } else {
            $weapp_config_result = $weapp_model->getWeAppConfig();
            $config_info = $weapp_config_result['data']["value"];
            $this->assign("config_info", $config_info);
            // 获取当前域名
            $url = __ROOT__;
            // 去除链接的http://头部
            $url_top = str_replace("https://", "", $url);
            $url_top = str_replace("http://", "", $url_top);
            // 去除链接的尾部/?s=
            $url_top = str_replace('/?s=', '', $url_top);
            $call_back_url = $url . '/wechat/wap/config/relateWeixin';
            $this->assign("url", $url_top);
            $this->assign("call_back_url", $call_back_url);
            return $this->fetch('weapp/config', [], $this->replace);
        }

    }
    
    /**
     * 源码发布
     */
    public function release(){
        $service = new Service();
        $res = $service->getPurchasedApplet();
        $this->assign('list', $res['data']);
        return $this->fetch('weapp/release', [], $this->replace);
    }
    
    /**
     * 小程序包管理
     */
    public function package(){
        if (request()->isAjax()) {
            $mark = input('mark', '');
            $service = new Service();
            $list = $service->getAppletVersionList($mark);
            return $list;
        }
        $mark = input('mark', '');
        $this->assign('mark', $mark);
        return $this->fetch('weapp/package', [], $this->replace);
    }
    
    /**
     * 小程序包下载
     */
    public function download(){
        if (request()->isAjax()) {
            $param = [
                'mark' => input('mark', ''),
                'release' => input('release', ''),
                'type' => input('type', '')
            ];
            $service = new Service();
            $res = $service->getAppletVersionUpgradeInfo($param);
            return $res;
        }
    }
    
    /**
     * 下载
     */
    public function toDownload(){
        $token = input('token', '');
        $service = new Service();
        $redirect_url = $service->download($token);
        $this->redirect($redirect_url);
    }
}