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

use app\model\web\WebSite as WebsiteModel;

/**
 * 跳转页
 */
class Website extends BaseCity
{

    /**
     * 编辑城市分站
     */
    public function config()
    {
        $website_model = new WebsiteModel();
        if (request()->isAjax()) {

            $data = [
                'title' => input('title', ''),
                'logo' => input('logo', ''),
                'desc' => input('desc', ''),
                'keywords' => input('keywords', ''),
                'web_address' => input('web_address', ''),
                'web_qrcode' => input('web_qrcode', ''),
                'web_email' => input('web_email', ''),
                'web_phone' => input('web_phone', ''),
                'web_qq' => input('web_qq', ''),
                'web_weixin' => input('web_weixin', ''),
                'wap_domain' => input('wap_domain', ''),
            ];

            $condition[] = ['site_id','=',$this->site_id];
            $res = $website_model->setWebSite($data,$condition);
            return $res;

        } else {

            return $this->fetch('website/config',[],$this->replace);
        }
    }

}