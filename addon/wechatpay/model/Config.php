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

namespace addon\wechatpay\model;


use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
/**
 * 微信支付配置
 */
class Config extends BaseModel
{
    /**
     * 支持端口
     * @var unknown
     */
    public $app_type = ['h5', 'wechat','pc', 'weapp'];
    
    /**
     * 设置支付配置
     * array $data
     */
    public function setPayConfig($data)
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, '微信支付配置', 1, [['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'WECHAT_PAY_CONFIG']]);
        return $res;
    }
    
    /**
     * 获取支付配置
     */
    public function getPayConfig()
    {
        $config = new ConfigModel();
        $res = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'WECHAT_PAY_CONFIG']]);
        return $res;
    }
}