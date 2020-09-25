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

use app\model\system\Config;
use app\model\web\WebSite as WebsiteModel;
use app\model\web\Notice as NoticeModel;

class Index extends BaseCity
{
    public function index()
    {
        $config_model = new Config();

        //站点信息
        $website_model = new WebsiteModel();
        $website_info = $website_model->getWebSite([['site_id','=',$this->site_id]],'account,account_withdraw,account_shop,account_order');
        $total_account = $website_info['data']['account'] + $website_info['data']['account_withdraw'];
        $this->assign('total_account',number_format($total_account,2,'.' , ''));
        $this->assign('website_info',$website_info['data']);

        $system_config = $config_model->getSystemConfig();

        $this->assign('system_config', $system_config['data']);

        //网站公告
        $notice_model = new NoticeModel();
        $notice_list = $notice_model->getNoticePageList([['receiving_type','like','%website%']], 1, 5,'is_top desc,create_time desc','id,title');
        $this->assign('notice_list', $notice_list['data']['list']);

        //平台配置信息
        $website_model = new WebsiteModel();
        $website = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
        $this->assign('website',$website['data']);

        return $this->fetch('index/index',[],$this->replace);

    }

}