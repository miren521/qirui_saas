<?php

/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

declare(strict_types=1);

namespace addon\servicer\servicer\controller;

use app\model\web\WebSite as WebsiteModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Config;
use think\facade\Request;
use GatewayClient\Gateway;
use addon\servicer\model\Member;
use addon\servicer\model\Dialogue;
use addon\servicer\model\Servicer;
use app\model\goods\Goods;
use app\model\order\Order;
use app\model\shop\Shop;

/**
 * 客服聊天
 */
class Chat extends BaseServicer
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        Config::load(__DIR__ . "/../../config/gateway_client.php");

        // 注册GateWayClient 到 GatewayWorker
        Gateway::$registerAddress = @config()['registeraddress'] ?? '127.0.0.1:1238';

        parent::__construct();
    }

    /**
     * 绑定WebSocket client_id 和 member_id / user_id
     * @return array|void
     * @throws DbException
     */
    public function bind()
    {
        $servicer_id = $this->uid;
        $client_id = Request::param('client_id', '');

        if (empty($client_id)) {
            return $this->result('', 0, '缺少参数');
        }

        // 重构 servicer_id, 防止与 客户 id 冲突
        $servicer_id_tmp = 'ns_servicer_' . $servicer_id;

        Gateway::bindUid($client_id, $servicer_id_tmp);
        Gateway::setSession($client_id, ['servicer_id'=>$servicer_id]);

        // 修改客服在线状态及绑定客户端ID
        $servicerModel = new Servicer();
        $servicerModel->setServicerOnlineStatus($servicer_id, $client_id);

        return $this->result(['servicer_id' => $servicer_id]);
    }

    /**
     * 获取店铺信息
     *
     * @return void
     */
    public function siteInfo()
    {
        $site_id = $this->site_id;
        if (empty($site_id) && $site_id != 0) {
            return $this->result('没有指定站点', -1);
        }

        if (empty($site_id)) {
            $website_model = new WebsiteModel();
            $website_info = $website_model->getWebSite([['site_id', '=', 0]], '*');
            $result['data']['logo'] = $website_info['data']['logo'];
            $result['data']['site_name'] = $website_info['data']['title'];
        } else {
            $result = (new Shop)->getShopInfo(['site_id' => $site_id], ['site_name', 'logo']);
        }

        return $this->result($result);
    }

    /**
     * 订单详情
     *
     * @return void
     */
    public function orderDetail()
    {
        $orderId = input('order_id', 0);
        $condition = array(
            ['order_id', '=', $orderId]
        );
        $orderDetail = (new Order)->getOrderDetail($condition);
        return $orderDetail;
    }

    /**
     * 商品详情
     * @return array
     */
    public function goodSkuDetial()
    {
        $skuId = input('sku_id', 0);

        $goodsSkuDetail = (new Goods)->getGoodsSkuInfo(['sku_id' => $skuId]);

        return $goodsSkuDetail;
    }

    /**
     * 客服回答咨询问题
     * @return array|void
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function answer()
    {
        $member_id = Request::param('member_id', '');
        $contentType = Request::param('content_type', 0);
        $servicerSay =  Request::param('servicer_say', '');
        $goodsId = Request::param('goods_id', 0);
        $orderId = Request::param('order_id', 0);

        if (empty($member_id)) {
            return $this->result('', -1, '没有指定会话人员');
        }

        if (empty($servicerSay) && empty($goodsId) && empty($orderId)) {
            return $this->result('', -2, '不能发送空消息哦！');
        }

        $isClientOnline = Gateway::isUidOnline($member_id);

        $read = $isClientOnline ? 1 : 0;

        // 消息持久化逻辑
        $dialogueModel = new Dialogue();
        $dialogueId = $dialogueModel->createDialogue(1, $member_id, $this->uid, $contentType, $read, $this->site_id, $this->uid, '', $servicerSay, $goodsId, $orderId);

        // 客服不在线时，不推送
        if (!$isClientOnline) {
            return $this->result('', 0, '会员不在线');
        }

        $dialogue = $dialogueModel->getDialogue($dialogueId);

        $messageType = '';
        switch ($contentType) {
            case Dialogue::CONTENTTYPE_GOODSKU:
                $messageType = 'goodssku';
                break;
            case Dialogue::CONTENTTYPE_ORDER:
                $messageType = 'order';
                break;
            case Dialogue::CONTENTTYPE_STRING:
                $messageType = 'string';
                break;
            case Dialogue::CONTENTTYPE_IMAGE:
                $messageType = 'image';
                break;
        }

        $message = ['type' => $messageType, 'data' => $dialogue];

        // 转发消息至客服
        Gateway::sendToUid($member_id, json_encode($message));

        // 绑定会员
        $memberModel = new Member();
        $client_id = @Gateway::getClientIdByUid($member_id);
        $memberModel->createMember($member_id, $this->uid, 1, $client_id);

        return $this->result('', 0, '发送成功');
    }

    /**
     * 客服下线
     * @return array|void
     * @throws DbException
     */
    public function bye()
    {
        $servicer_id = $this->uid;

        $servicerModel = new Servicer();
        $servicerModel->setServicerOnlineStatus($servicer_id, '', false);

        $client_id = Gateway::getClientIdByUid('ns_servicer_' . $servicer_id);
        Gateway::closeClient($client_id);

        return $this->result('', 0, '下线成功');
    }

    /**
     * 客服结束与客户会话
     * @return array|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function closeMember()
    {
        $member_id = Request::param('member_id', '');

        $memberModel = new Member();
        $member = $memberModel->getMember($member_id, $this->uid);

        $memberModel->setMemberOnline($member_id, false);

        if (!empty($member)) {
            Gateway::closeClient($member['client_id'], json_encode(['msg' => '会话已结束！感谢您的咨询']));
        }

        return $this->result('', 0, '已结束会员会话');
    }

    /**
     * 绑定会员WebSocket client_id 和 member_id / user_id
     * @return array|void
     * @throws DbException
     */
    public function bindMember()
    {
        $member_id = input('member_id', 0);
        if (empty($member_id)) {
            return $this->result('', -1, '没有指定会员');
        }

        // 绑定商城端客户
        $isOnline = @Gateway::isUidOnline($member_id);
        $isOnline = $isOnline ? 1 : 0;
        if ($isOnline) {
            // 判断是否有绑定
            $condition     = [
                ['servicer_id', '=', $this->uid],
                ['member_id', '=', $member_id],
                ['online', '=', 1]
            ];
            $memberModel = new Member();
            $data = $memberModel->getList($condition);
            if (empty($data)) {
                $client_ids = @Gateway::getClientIdByUid($member_id);
                $memberModel->createMember($member_id, $this->uid, $isOnline, $client_ids[0]);
                // 向会员通知，有客服接入咨询
                Gateway::sendToUid(
                    $member_id,
                    json_encode(['type' => 'connect', 'data' => ['servicer_id' => $this->uid]])
                );
                return $this->result(['servicer_id' => $this->uid], 0, '会员连接成功');
            }
            return $this->result(['servicer_id' => $this->uid], 0, '无需重复连接');
        }
        return $this->result('', 0, '会员不在线');
    }
}
