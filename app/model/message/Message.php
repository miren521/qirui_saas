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


namespace app\model\message;

use app\model\web\WebSite;
use think\facade\Cache;
use app\model\BaseModel;
use addon\wechat\model\Wechat;

/**
 * 消息管理类
 */
class Message extends BaseModel
{
	
	/********************************************************************* 平台消息类型 start *********************************************************************************/
	
	/**
	 * 编辑消息
	 * @param $data
	 * @param $condition
	 */
	function editMessage($data, $condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$keywords = isset($check_condition['keywords']) ? $check_condition['keywords'] : '';
		if ($keywords === '') {
			return $this->error('', 'REQUEST_KEYWORDS');
		}
		
		Cache::tag("message")->clear();
		$res = model('message')->update($data, $condition);
		if ($res === false) {
			return $this->error('', 'UNKNOW_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 编辑邮箱是否启动
	 * @param $is_open
	 * @param $condition
	 */
	public function modifyMessageEmailIsOpen($is_open, $condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$keywords = isset($check_condition['keywords']) ? $check_condition['keywords'] : '';
		if ($keywords === '') {
			return $this->error('', 'REQUEST_KEYWORDS');
		}
		Cache::tag("message")->clear();
		$data = array(
			"email_is_open" => $is_open
		);
		$res = model('message')->update($data, $condition);
		if ($res === false) {
			return $this->error('', 'UNKNOW_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 编辑短信消息是否启动
	 * @param $is_open
	 * @param $condition
	 */
	public function modifyMessageSmsIsOpen($is_open, $condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$keywords = isset($check_condition['keywords']) ? $check_condition['keywords'] : '';
		if ($keywords === '') {
			return $this->error('', 'REQUEST_KEYWORDS');
		}
		Cache::tag("message")->clear();
		$data = array(
			"sms_is_open" => $is_open
		);
		$res = model('message')->update($data, $condition);
		if ($res === false) {
			return $this->error('', 'UNKNOW_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 编辑微信模板消息是否启动
	 * @param $is_open
	 * @param $condition
	 */
	public function modifyMessageWechatIsOpen($is_open, $condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$keywords = isset($check_condition['keywords']) ? $check_condition['keywords'] : '';
		if ($keywords === '') {
			return $this->error('', 'REQUEST_KEYWORDS');
		}
		Cache::tag("message")->clear();
		$data = array(
			"wechat_is_open" => $is_open
		);
		$res = model('message')->update($data, $condition);
		if ($res === false) {
			return $this->error('', 'UNKNOW_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 编辑微信小程序消息是否启动
	 * @param $is_open
	 * @param $condition
	 */
	public function modifyMessageWeappIsOpen($is_open, $condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$keywords = isset($check_condition['keywords']) ? $check_condition['keywords'] : '';
		if ($keywords === '') {
			return $this->error('', 'REQUEST_KEYWORDS');
		}
		Cache::tag("message")->clear();
		$data = array(
			"weapp_is_open" => $is_open
		);
		$res = model('message')->update($data, $condition);
		if ($res === false) {
			return $this->error('', 'UNKNOW_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 编辑阿里小程序消息是否启动
	 * @param $is_open
	 * @param $condition
	 */
	public function modifyMessageAliappIsOpen($is_open, $condition)
	{
		$check_condition = array_column($condition, 2, 0);
		$keywords = isset($check_condition['keywords']) ? $check_condition['keywords'] : '';
		if ($keywords === '') {
			return $this->error('', 'REQUEST_KEYWORDS');
		}
		Cache::tag("message")->clear();
		$data = array(
			"aliapp_is_open" => $is_open
		);
		$res = model('message')->update($data, $condition);
		if ($res === false) {
			return $this->error('', 'UNKNOW_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 消息详情
	 * @param $condition
	 * @param string $field
	 * @return \multitype
	 */
	public function getMessageInfo($condition, $field = "*")
	{
		$check_condition = array_column($condition, 2, 0);
		$keywords = isset($check_condition['keywords']) ? $check_condition['keywords'] : '';
		if ($keywords === '') {
			return $this->error('', 'REQUEST_KEYWORDS');
		}
		
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::get("message_getMessageInfo_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$info = model("message")->getInfo($condition, $field);
		
		$info["message_json_array"] = empty($info["message_json"]) ? [] : json_decode($info["message_json"], true);//消息配置
		$info["sms_json_array"] = empty($info["sms_json"]) ? [] : json_decode($info["sms_json"], true);//短信配置
		$info["wechat_json_array"] = empty($info["wechat_json"]) ? [] : json_decode($info["wechat_json"], true);//微信公众号配置
		$info["weapp_json_array"] = empty($info["weapp_json"]) ? [] : json_decode($info["weapp_json"], true);//微信小程序配置
		$info["aliapp_json_array"] = empty($info["aliapp_json"]) ? [] : json_decode($info["aliapp_json"], true);//阿里配置
		
		Cache::tag("message")->set("message_getMessageInfo_" . $data, $info);
		return $this->success($info);
	}
	
	
	/**
	 * 消息列表
	 * @param array $condition
	 * @param bool $field
	 * @param string $order
	 * @param null $limit
	 */
	public function getMessageList($condition = [], $field = true, $order = '', $limit = null)
	{
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::get("message_getMessageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('message')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("message")->set("message_getMessageList_" . $data, $list);
		return $this->success($list);
	}
	
	/**
	 * 消息分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return \multitype
	 */
	public function getMessagePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		
		$data = json_encode([ $condition, $page, $page_size, $order, $field ]);
		$cache = Cache::get("message_getMessagePageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('message')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("message")->set("message_getMessagePageList_" . $data, $list);
		return $this->success($list);
	}
	
	/**
	 * 获取微信模板消息id
	 * @param string $keywords
	 */
	public function getWechatTemplateNo(string $keywords)
	{
		$keyword = explode(',', $keywords);
		$list = model('message')->getList([ [ 'keywords', 'in', $keyword ], [ 'wechat_json', '<>', '' ] ], 'keywords,wechat_json');
		if (!empty($list)) {
			$wechat = new Wechat();
			foreach ($list as $item) {
				$template_info = json_decode($item['wechat_json'], true);
				$res = $wechat->getTemplateId($template_info['template_id_short']);
				if (isset($res['errcode']) && $res['errcode'] == 0) {
					$template_info['template_id'] = $res['template_id'];
					model('message')->update([ 'wechat_json' => json_encode($template_info, JSON_UNESCAPED_UNICODE) ], [ 'keywords' => $item['keywords'] ]);
				} else {
					return $this->error($res, $res['errmsg']);
				}
			}
		}
		Cache::clear('message');
		return $this->success();
	}
	
	/********************************************************************* 平台消息类型 end *********************************************************************************/
	
	
	/**
	 * 消息发送
	 * @param $param
	 */
	public function sendMessage($param)
	{
        try {
            $keywords = $param["keywords"];
            $condition = [
                [ "keywords", "=", $keywords ],
            ];
            $message_info_result = $this->getMessageInfo($condition);
            $message_info = $message_info_result["data"];
            $param["message_info"] = $message_info;
            //查询网站信息
            $website_model = new WebSite();
            $website_info_result = $website_model->getWebSite([ [ "site_id", "=", 0 ] ], "title");
            $param["site_name"] = $website_info_result["data"]["title"];
            $result = event("SendMessageTemplate", $param, true);//匹配消息模板  并发送
            return $result;
        } catch (\Exception $e) {
            return $this->error('', "MESSAGE_FAIL");
        }
	}
}