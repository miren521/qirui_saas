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
namespace addon\wechat\admin\controller;

use app\model\message\Message as MessageModel;

/**
 * 微信公众号模板消息
 */
class Message extends BaseWechat
{
    /**
     * 编辑模板消息
     * @return array|mixed|string
     */
    public function edit()
    {
        $message_model = new MessageModel();
        $keywords = input("keywords", "");
        $info_result = $message_model->getMessageInfo([["keywords", "=",$keywords ]]);
        $info = $info_result["data"];

        $wechat_json_array = $info["wechat_json_array"];
        if (request()->isAjax()) {
            if(empty($info))
                return error("", "不存在的模板信息!");

            $bottomtext = input("bottomtext", "");
            $headtext = input("headtext", "");
            $bottomtextcolor = input("bottomtextcolor", "");
            $headtextcolor = input("headtextcolor", "");
            $wechat_is_open = input("wechat_is_open", 0);
            $wechat_json_array['headtext'] = $headtext;//头部标题
            $wechat_json_array['headtextcolor'] = $headtextcolor;//头部标题颜色
            $wechat_json_array['bottomtext'] = $bottomtext;//尾部描述
            $wechat_json_array['bottomtextcolor'] = $bottomtextcolor;//尾部描述颜色

            $data = array(
                'wechat_json' => json_encode($wechat_json_array),
                "wechat_is_open" => $wechat_is_open
            );
            $condition = array(
                ["keywords", "=", $keywords]
            );
            $res = $message_model->editMessage($data, $condition);
            return $res;
        } else {
            if(empty($info))
                $this->error("不存在的模板信息!");

            $this->assign("keywords", $keywords);
            $this->assign("wechat_is_open", $info["wechat_is_open"]);
            $this->assign("info", $wechat_json_array);
            return $this->fetch('message/edit');
        }
    }

    /**
     * 模板消息设置
     * @return array|mixed|\multitype
     */
    public function config() {
        $message_model = new MessageModel();

        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = array(
                ["message_type", "=", 1],
                ["support_type", "like", '%wechat%']
            );
            $list = $message_model->getMessagePageList($condition, $page, $page_size);
            foreach ($list['data']['list'] as $k => $v) {
                if($v['wechat_json']) {
                    $template_info = json_decode($v['wechat_json'], true);
                    $list['data']['list'][$k]['template_no'] = $template_info['template_id'] ?? '';
                }else {
                    $list['data']['list'][$k]['template_no'] = '';
                }
            }
            return $list;
        } else {
            return $this->fetch('message/config');
        }

    }

    /**
     * 微信模板消息状态设置
     */
    public function setWechatStatus() {
        $message_model = new MessageModel();
        if (request()->isAjax()) {
            $keywords = input("keywords", "");
            $status = input('status', 0);
            $res = $message_model->modifyMessageWechatIsOpen($status, [['keywords', 'in', $keywords]]);
            return $res;
        }
    }

    /**
     * 获取模板编号
     */
    public function getWechatTemplateNo() {
        if (request()->isAjax()) {
            $keywords = input("keywords", "");
            $message_model = new MessageModel();
            $res = $message_model->getWechatTemplateNo($keywords);
            return $res;
        }
    }
    
}