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


namespace app\pay\controller;

use app\Controller;
use app\model\web\WebSite;
use app\model\system\Pay as PayModel;

/**
 * 支付控制器
 */
class Pay extends Controller
{
    /**
     * 支付异步回调
     */
    public function notify()
    {
        event('PayNotify', []);
    }

    /**
     * 支付返回
     */
    public function payReturn()
    {
        $app_type     = input('app_type', '');
        $out_trade_no = input('out_trade_no', '');

        $pay_model = new PayModel();
        $pay_info_result = $pay_model->getPayInfo($out_trade_no);
        $pay_info = $pay_info_result['data'] ?? [];

        if(!empty($pay_info['return_url'])){
            $this->redirect(addon_url($pay_info['return_url']));
        }else{
            $website_model  = new WebSite();
            $website_result = $website_model->getWebSite([['site_id', '=', 0]], 'wap_domain,web_domain');
            $website        = $website_result['data'] ?? [];
            switch ($app_type) {
                case 'pc':
                    $return_url = $website['web_domain'] . '/result?code=' . $out_trade_no;
                    $this->redirect($return_url);
                    break;
                case 'h5':
                    $return_url = $website['wap_domain'] . '/pages/pay/result/result?code=' . $out_trade_no;
                    $this->redirect($return_url);
                    break;
            }
        }

    }
}
