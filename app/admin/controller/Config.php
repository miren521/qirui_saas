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


namespace app\admin\controller;

use app\model\system\Api;
use app\model\system\Pay;
use app\model\system\Upgrade;
use app\model\web\Config as ConfigModel;
use app\model\web\WebSite as WebsiteModel;
use app\model\shop\Config as ShopConfigModel;
use app\model\goods\Config as GoodsConfigModel;
use extend\RSA;


/**
 * 设置 控制器
 */
class Config extends BaseAdmin
{
    /**
     * 网站设置
     */
    public function webConfig()
    {
        if (request()->isAjax()) {
            $data = [
                'title' => input('title', ''),//网站标题
                'logo' => input('logo', ''),//网站logo 图片
                'desc' => input('desc', ''),//网站描述
                'keywords' => input('keywords', ''),//网站关键字
                'web_address' => input('web_address', ''),//网站地址
                'web_qrcode' => input('web_qrcode', ''),//网站公众号二维码 图片
                'web_email' => input('web_email', ''),//网站邮箱
                'web_phone' => input('web_phone', ''),//网站联系方式
                'web_qq' => input('web_qq', ''),//网站qq
                'web_weixin' => input('web_weixin', ''),//网站微信
                'web_status' => input('web_status', 1),//pc端状态 1：开启 0：关闭
                'wap_status' => input('wap_status', 1),//手机端状态 1：开启 0：关闭
                'close_reason' => input('close_reason', ''),//网站关闭原因 textarea
                'site_id' => 0,
                'wap_domain' => input('wap_domain', ''), // 手机端域名
                'web_domain' => input('web_domain', '') // 电脑端域名
            ];
            $website_model = new WebsiteModel();
            return $website_model->setWebSite($data, [['site_id', '=', 0]]);
        } else {
            $website_model = new WebsiteModel();
            $website_info = $website_model->getWebSite([['site_id', '=', 0]], '*');
            $this->assign('website_info', $website_info);
            return $this->fetch('config/web_config');
        }
    }

    public function copyright()
    {
        $config_model = new ConfigModel();
        $copyright = $config_model->getCopyright();
        $upgrade_model = new Upgrade();
        $auth_info     = $upgrade_model->authInfo();

	    if (request()->isAjax()) {
            $logo = input('logo', '');
	        $data = [
                'icp' => input('icp', ''),
                'gov_record' => $gov_record = input('gov_record', ''),
                'gov_url' => input('gov_url', ''),
                'logo' => '',
                'company_name' => '',
                'copyright_link' => '',
                'copyright_desc' => '',
            ];
            if ($auth_info['code'] == 0) {
                $data['logo'] = input('logo', '');
                $data['company_name'] = input('company_name', '');
                $data['copyright_link'] = input('copyright_link', '');
                $data['copyright_desc'] = input('copyright_desc', '');
            }
            $this->addLog("修改版权配置");
            $res = $config_model->setCopyright($data);
            return $res;
        }
        $this->assign('is_auth', ($auth_info['code'] >= 0 ? 1 : 0));
        $this->assign('copyright', $copyright[ 'data' ][ 'value' ]);
        return $this->fetch('config/copyright');
    }

    /**
     * 收款设置
     */
    public function receivable()
    {
        if (request()->isAjax()) {
            $data = [
                'bank_account_name' => input('bank_account_name', ''),//银行开户名
                'bank_account_no' => input('bank_account_no', ''),//银行账户
                'bank_name' => input('bank_name', ''),//开户行名称
                'bank_address' => input('bank_address', ''),//开户地址
            ];
            $shop_config_model = new ShopConfigModel();
            return $shop_config_model->setSystemBankAccount($data);
        } else {
            $shop_config_model = new ShopConfigModel();
            $receivable_config = $shop_config_model->getSystemBankAccount();

            $this->assign('receivable_config', $receivable_config);
            return $this->fetch('config/receivable');
        }
    }

    /*
     * 售后保障
     */
    public function aftersale()
    {
        $goods_config_model = new GoodsConfigModel();
        if (request()->isAjax()) {
            $content = input('content', '');//售后保障协议
            return $goods_config_model->setAfterSaleConfig('售后保障协议', $content);
        } else {
            $content = $goods_config_model->getAfterSaleConfig();

            $this->assign('content', $content);
            return $this->fetch('config/aftersale');
        }
    }

    /**
     * 验证码设置
     */
    public function captcha()
    {
        if (request()->isAjax()) {
            $data = [
                'admin_login' => input('admin_login', 0),//平台登录是否启用 1：启用 0：不启用
                'shop_login' => input('shop_login', 0),//商家登陆是否启用 1：启用 0：不启用
            ];
            $config_model = new ConfigModel();
            return $config_model->setCaptchaConfig($data);
        } else {
            $config_model = new ConfigModel();
            $config_info = $config_model->getCaptchaConfig();
            $this->assign('config_info', $config_info[ 'data' ][ 'value' ]);
            return $this->fetch('config/captcha');
        }
    }

    /**
     * 支付管理
     */
    public function pay()
    {
        if (request()->isAjax()) {
            $pay_model = new Pay();
            $list = $pay_model->getPayType([]);
            return $list;
        } else {
            return $this->fetch('config/pay');
        }
    }

    /**
     * api安全
     */
    public function api()
    {
        $api_model = new Api();
        if (request()->isAjax()) {
            $is_use = input("is_use", 0);
            $public_key = input("public_key", "");
            $private_key = input("private_key", "");
            $data = array(
                "public_key" => $public_key,
                "private_key" => $private_key,
            );
            $result = $api_model->setApiConfig($data, $is_use);
            return $result;
        } else {
            $config_result = $api_model->getApiConfig();
            $config = $config_result[ "data" ];
            $this->assign("config", $config);
//			var_dump(RSA::getSecretKey());
            return $this->fetch('config/api');
        }
    }

    public function generateRSA()
    {
        if (request()->isAjax()) {
            return RSA::getSecretKey();
        }
    }

    /**
     * 默认图设置
     */
    public function defaultPicture()
    {
        $upload_config_model = new ConfigModel();
        if (request()->isAjax()) {
            $data = array(
                "default_goods_img" => input("default_goods_img", ""),
                "default_headimg" => input("default_headimg", ""),
                "default_shop_img" => input("default_shop_img", ""),
            );
            $this->addLog("修改默认图配置");
            $res = $upload_config_model->setDefaultImg($data);

            return $res;
        } else {
            $upload_config_result = $upload_config_model->getDefaultImg();
            $this->assign("default_img", $upload_config_result[ 'data' ][ 'value' ]);
            return $this->fetch('config/default_picture');
        }
    }

    /**
     * 商城配置
     */
    public function basic()
    {
        $config_model = new ConfigModel();
        if (request()->isAjax()) {
            $data = array(
                'search_keywords' => input('search_keywords', [])
            );
            $this->addLog("修改商城配置");
            $res = $config_model->setBasicConfig($data);

            return $res;
        } else {
            $result = $config_model->getBasicConfig();
            $this->assign("config", $result[ 'data' ][ 'value' ] ?? []);
            return $this->fetch('config/basic');
        }
    }

}