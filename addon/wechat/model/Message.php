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

namespace addon\wechat\model;

use app\model\BaseModel;
use addon\weapp\model\Config as WeappConfig;
use app\model\web\WebSite as WebsiteModel;
/**
 * 微信消息模板
 */
class Message extends BaseModel
{
    /**
     * 发送模板消息
     * @param array $param
     */
	public function sendMessage(array $param) {
	    $support_type = $data["support_type"] ?? [];
	    //验证是否支持邮箱发送
	    if(!empty($support_type) && !in_array("wechat", $support_type)) return $this->success();
	    
	    if (empty($param['openid'])) return $this->success('缺少必需参数openid');
	    
	    $message_info = $param['message_info'];
	    if (!isset($message_info['wechat_is_open']) || $message_info['wechat_is_open'] == 0) return $this->error('未启用模板消息');
	    
	    $wechat_info = json_decode($message_info['wechat_json'], true);
	    if (!isset($wechat_info['template_id']) || empty($wechat_info['template_id'])) return $this->error('未配置模板消息');
        

	    $template_data = [
            'first' => [
                'value' => $wechat_info['headtext'],
                'color' => !empty($wechat_info['headtextcolor']) ? $wechat_info['headtextcolor'] : '#f00'
            ],
	        'remark' => [
	            'value' => $wechat_info['bottomtext'],
	            'color' => !empty($wechat_info['bottomtextcolor']) ? $wechat_info['bottomtextcolor']: '#333'
	        ]
	    ];
	    if (!empty($param['template_data'])) $template_data = array_merge($template_data, $param['template_data']);
	    
	    
	    $data = [
	        'openid' => $param['openid'],
	        'template_id' => $wechat_info['template_id'],
	        'data' => $template_data,
	        'miniprogram' => [],
	        'url' => ''
	    ];
	    
	    if (!empty($param['page'])) {
	        // 小程序配置
	        $weapp_config = new WeappConfig();
	        $weapp_config_result = $weapp_config->getWeAppConfig();
	        $weapp_config = $weapp_config_result['data']["value"];
	        
    	    if (!empty($weapp_config['appid'])) {
    	        $data['miniprogram'] = [
    	            'appid' => $weapp_config['appid'],
    	            'pagepath' => $param['page']
    	        ];
    	    }
            
            $website_model = new WebsiteModel();
            $website_info = $website_model->getWebSite([ [ 'site_id', '=', 0 ] ], 'wap_domain');
            if (!empty($website_info['data']['wap_domain'])) {
                $data['url'] = $website_info['data']['wap_domain'] .'/'. $param['page'];
            }
	    }
	    $wechat = new Wechat();
	    $res = $wechat->sendTemplateMessage($data);
	    return $res;
	}
}