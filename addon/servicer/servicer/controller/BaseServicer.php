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


namespace addon\servicer\servicer\controller;

use app\Controller;
use app\model\shop\Shop;
use app\model\store\Store as StoreModel;
use app\model\system\Addon;
use app\model\system\Group as GroupModel;
use app\model\system\Menu;
use app\model\system\User as UserModel;
use app\model\web\Config as ConfigModel;
use app\model\web\WebSite;


/**
 * 客服
 */
class BaseServicer extends Controller
{
    protected $crumbs = [];

    protected $uid;
    protected $user_info;
    protected $url;
    protected $group_info;
    protected $site_id;
    protected $store_info;
    protected $shop_info;
    protected $app_module = "servicer";
    protected $addon = '';
    protected $replace;

    public function __construct()
    {
        $this->replace = [
            'SERVICER_CSS' => __ROOT__ . '/addon/servicer/servicer/view/public/css',
            'SERVICER_JS'  => __ROOT__ . '/addon/servicer/servicer/view/public/js',
            'SERVICER_IMG' => __ROOT__ . '/addon/servicer/servicer/view/public/img',
        ];
        //执行父类构造函数
        parent::__construct();
        //检测基础登录
        $user_model      = new UserModel();
        $this->uid       = $user_model->uid($this->app_module);
        $this->url       = request()->parseUrl();
        $this->addon     = request()->addon() ? request()->addon() : '';
        $this->user_info = $user_model->userInfo($this->app_module);
        $this->assign("user_info", $this->user_info);
        $this->site_id = $this->user_info["site_id"];
        if (empty($this->uid)) {
            $this->redirect(addon_url("servicer://servicer/login/login"));
            exit();
        }

        if (!request()->isAjax()) {
            $this->initBaseInfo();
        }
    }

    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        return ['code' => $code, 'data' => $data, 'msg' => $msg];
    }

    /**
     * 加载基础信息
     */
    private function initBaseInfo()
    {

        $shop_model      = new Shop();
        $shop_info       = $shop_model->getShopInfo(
            [['site_id', '=', $this->site_id]],
            'site_name,logo,is_own,level_id,category_id,group_id,seo_keywords,seo_description,expire_time'
        );
        $this->shop_info = $shop_info['data'];
        $this->assign("shop_info", $this->shop_info);
        $this->assign("url", $this->url);
        $this->assign("crumbs", $this->crumbs);

        //加载网站基础信息
        $website      = new WebSite();
        $website_info = $website->getWebSite(
            [['site_id', '=', 0]],
            'title,logo,desc,keywords,web_status,close_reason,web_qrcode,web_phone'
        );
        $this->assign("website_info", $website_info['data']);
        //加载版权信息
        $config_model = new ConfigModel();
        $copyright    = $config_model->getCopyright();
        $this->assign('copyright', $copyright['data']['value']);
    }
}
