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


namespace addon\servicer\api\controller;

use addon\servicer\model\Dialogue;
use addon\servicer\model\Member;
use addon\servicer\model\Servicer;
use app\api\controller\BaseApi;
use app\model\goods\Goods;
use app\model\order\Order;
use app\model\shop\Shop;
use Exception;
use GatewayClient\Gateway;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Request;
use think\facade\Config;
use app\model\web\WebSite as WebsiteModel;

/**
 * 客户端客服相关API
 */
class Chat extends BaseApi
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
     * 查找当前是否有客服在线
     * @return false|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function hasServicers()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $site_id = input('site_id', 0);
        if (empty($site_id)) {
            return $this->response($this->error('没有指定店铺'));
        }

        $result = (new Servicer)->getOnlineServicers($site_id);
        if ($result['code'] != 0) {
            return $this->response($result);
        }

        $list = $result['data'];

        $onlineCount = 0;
        foreach ($list as $item) {
            $online = @Gateway::isUidOnline('ns_servicer_' . $item['user_id']) ?? 0;
            if ($online) {
                $onlineCount += 1;
            }
        }

//        if (!$onlineCount) {
//            return $this->response($this->error('没有客服在线'));
//        }

        return $this->response($this->success(['online_count' => $onlineCount]));
    }

    /**
     * 绑定WebSocket client_id 和 member_id / user_id
     * @return false|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function bind()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $site_id = Request::param('site_id', '');
        if (empty($site_id) && $site_id != 0) {
            return $this->response($this->error('', '没有指定商家'));
        }

        $client_id = Request::param('client_id', '');
        $member_id = $this->member_id;

        if (empty($client_id)) {
            return $this->response($this->error('', '缺少必要的参数'));
        }

        // 检测当前用户是否仍然在线
        $isOnline = Gateway::isOnline($client_id);

        // 用户发起请求后，通信链路意外断开，用户已不在线
//        if (!$isOnline) {
//            return $this->response($this->error('', '当前用户已不在线'));
//        }

        Gateway::bindUid($client_id, $member_id);

        // 获取匹配的客服
        $servicerModel = new Servicer();
        $servicerList = @$servicerModel->getUsefulServicer($site_id, $member_id);
        if (empty($servicerList)) {
                return $this->response($this->error('', '客服不在线'));
        }
        foreach ($servicerList as $item) {
            // ws是否在线
            $online = @Gateway::isUidOnline('ns_servicer_' . $item['user_id']) ?? 0;
            if (!$online) {
                continue;
            }
            $servicer = $item;
            if (!empty($servicer)) {
                break;
            }
        }
        if (empty($servicer)) {
            return $this->response($this->error('', '客服不在线'));
        }
        // 绑定客服
        $memberModel = new Member();
        $id = $memberModel->createMember($member_id, $servicer['user_id'], $isOnline, $client_id);
        if (!$id) {
            return $this->response($this->error('', '客服连接异常'));
        }

        // 向客服通知，有会员咨询
        $member = (new Member)->getMember($member_id, $servicer['user_id']);
        Gateway::sendToUid(
            'ns_servicer_' . $servicer['user_id'],
            json_encode(['type' => 'connect', 'data' => $member])
        );

        return $this->response($this->success(['servicer_id' => $servicer['user_id']]));
    }

    /**
     * 获取店铺信息 todo
     * @return false|string
     */
    public function siteInfo()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $site_id = input('site_id', '');
        if (empty($site_id) && $site_id != 0) {
            return $this->response($this->error('没有指定站点'));
        }

        if ($site_id == 0) {
            $website_model = new WebsiteModel();
            $website_info = $website_model->getWebSite([['site_id', '=', 0]], '*');
            $result['data']['logo'] = $website_info['data']['logo'];
            $result['data']['site_name'] = $website_info['data']['title'];
        } else {
            $result = (new Shop)->getShopInfo(['site_id' => $site_id, ['site_name', 'logo']]);
        }

        return $this->response($this->success($result));
    }

    /**
     * 订单详情
     * @return array|false|string
     */
    public function orderDetail()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $orderId = input('order_id', 0);

        $condition = array(
            ['order_id', '=', $orderId]
        );
        $orderDetail = (new Order)->getOrderDetail($condition);
        return $orderDetail;
    }

    /**
     * 商品详情
     * @return array|false|string
     */
    public function goodSkuDetial()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $skuId = input('sku_id', 0);

        $goodsSkuDetail = (new Goods)->getGoodsSkuInfo(['sku_id' => $skuId]);

        return $goodsSkuDetail;
    }

    /**
     * 发送聊天内容
     * @return false|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function say()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $servicer_id = Request::param('servicer_id', 0);
        $contentType = Request::param('content_type', '');
        $message = Request::param('message', '');
        // $consumerSay =  Request::param('consumer_say', '');
        $goodsId = Request::param('goods_id', 0);
        $orderId = Request::param('order_id', 0);
        $siteId = Request::param('site_id', '');

        if (empty($siteId) && $siteId != 0) {
            return $this->response($this->error('没有指定商家'));
        }

        if (empty($message) && empty($goodsId) && empty($orderId)) {
            return $this->response($this->error('不能发送空消息哦！'));
        }

        try {
            $isServicerOnline = @Gateway::isUidOnline('ns_servicer_' . $servicer_id);
        } catch (Exception $e) {
            $isServicerOnline = false;
        }

        $read = $isServicerOnline ? 1 : 0;

        // 消息持久化逻辑
        $dialogueModel = new Dialogue();
        $dialogueId = $dialogueModel->createDialogue(
            0,
            $this->member_id,
            $servicer_id,
            $contentType,
            $read,
            $siteId,
            $servicer_id,
            $message,
            '',
            $goodsId,
            $orderId
        );

        // 客服不在线时，不推送
        if (!$isServicerOnline) {
            // 重新匹配的客服
            $servicerModel = new Servicer();
            $servicerList = @$servicerModel->getUsefulServicer($siteId, $this->member_id);
            if (empty($servicerList)) {
                return $this->response($this->success(['read' => $read]));
            }
            foreach ($servicerList as $item) {
                // ws是否在线
                $online = @Gateway::isUidOnline('ns_servicer_' . $item['user_id']) ?? 0;
                if (!$online) {
                    continue;
                }
                $servicer = $item;
                if (!empty($servicer)) {
                    break;
                }
            }
            if (empty($servicer)) {
                return $this->response($this->success(['read' => $read]));
            }
            // 绑定客服
            $client_id = Gateway::getClientIdByUid($this->member_id);
            $memberModel = new Member();
            $id = $memberModel->createMember($this->member_id, $servicer['user_id'], 1, $client_id);
            if (!$id) {
                return $this->response($this->success(['read' => $read]));
            }
            // 向客服通知，有会员咨询
            $member = (new Member)->getMember($this->member_id, $servicer['user_id']);
            Gateway::sendToUid(
                'ns_servicer_' . $servicer['user_id'],
                json_encode(['type' => 'connect', 'data' => $member])
            );
            Gateway::sendToUid(
                $this->member_id,
                json_encode(['type' => 'connect', 'data' => ['servicer_id' => $servicer['user_id']]])
            );
        }

        $dialogue = $dialogueModel->getDialogue($dialogueId);

        $type = 'string';
        switch ($contentType) {
            case Dialogue::CONTENTTYPE_STRING:
                $type = 'string';
                break;
            case Dialogue::CONTENTTYPE_ORDER:
                $type = 'order';
                break;
            case Dialogue::CONTENTTYPE_GOODSKU:
                $type = 'goodssku';
                break;
            case Dialogue::CONTENTTYPE_IMAGE:
                $type = 'image';
                break;
        }
        $message = ['type' => $type, 'data' => $dialogue];

        // 发送给所有连接的客服
        $memberModel = new Member();
        $memberList = $memberModel->getList([['member_id', '=', $this->member_id], ['online', '=', 1]]);
        if (!empty($memberList)) {
            foreach ($memberList as $item) {
                $isServicerOnline = @Gateway::isUidOnline('ns_servicer_' . $item['servicer_id']);
                $isServicerOnline = $isServicerOnline ? 1 : 0;
                if ($isServicerOnline) {
                    // 转发消息至客服·
                    Gateway::sendToUid('ns_servicer_' . $item['servicer_id'], json_encode($message));
                }
            }
        }
        return $this->response($this->success(['read' => $read]));
    }

    /**
     * 获取聊天记录表
     * @return false|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function dialogs()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $page = input('page', 1);
        $limit = input('limit', 5);
        $siteId = input('site_id', '');

//        $servicer_id = input('servicer_id', 0);
        if (empty($siteId) && $siteId != 0) {
            return $this->response($this->error('没有指定商家'));
        }

        $pagelist = (new Dialogue())->getDialogueList($this->member_id, $page, $limit, $siteId);
        if (!empty($pagelist) && !empty($pagelist['list']) && count($pagelist['list']) > 0) {
            $pagelist['list'] = array_reverse($pagelist['list']);
        }

        return $this->response($this->success($pagelist));
    }

    /**
     * 客户端主动结束会话
     * @return false|string
     * @throws DbException
     */
    public function bye()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) {
            return $this->response($token);
        }

        $servicer_id = input('servicer_id', 0);
        if (empty($servicer_id)) {
            return $this->response($this->error('没有指定客服'));
        }

        $member_id = $this->member_id;
        $client_id = Gateway::getClientIdByUid($member_id);

        if (empty($client_id) || count($client_id) == 0) {
            return $this->response($this->success('', '会话已结束！'));
        }

        Gateway::closeClient($client_id[0], json_encode(['type' => 'close', 'data' => ['status' => true]]));
        Gateway::sendToUid(
            'ns_servicer_' . $servicer_id,
            json_encode(['type' => 'disconnect', 'data' => ['member_id' => $member_id]])
        );

        $memberModel = new Member();
        $memberModel->setMemberOnline($member_id, false);

        return $this->response($this->success('', '会话已结束！'));
    }

    /**
     * 在线信息
     * @return false|string
     */
    public function checkOnline()
    {
        $uidList = Gateway::getAllUidList();
        $clientList = Gateway::getAllClientIdList();
        return json_encode(['code'=>1, 'msg'=>'success', 'data'=>compact('uidList', 'clientList')]);
    }

    /**
     * 在线信息
     * @return false|string
     */
    public function checkClient()
    {
        $client_id = request()->param('client_id', '');
        $uid = request()->param('uid', 0);

        $online = 0;
        if (!empty($client_id)) {
            $session = Gateway::getSession($client_id);
            $uid = Gateway::getUidByClientId($client_id);
            $online = @Gateway::isOnline($client_id) ?? 0;
        }
        if (!empty($uid)) {
            $clients = Gateway::getClientIdByUid($uid);
            $client_id = $clients[0];
            $session = Gateway::getSession($client_id);
            $uid = Gateway::getUidByClientId($client_id);
            $online = @Gateway::isUidOnline($uid) ?? 0;
        }
        if (!$online) {
            return json_encode(['code'=>0, 'msg'=>'客服不在线', 'data'=>compact('session', 'uid', 'client_id')]);
        }
        return json_encode(['code'=>1, 'msg'=>'客服在线', 'data'=>compact('session', 'uid', 'client_id')]);
    }
    /**
     * 在线信息
     * @return false|string
     */
    public function sendMsg()
    {
        $client_id = request()->param('client_id', '');
        $uid = Gateway::getUidByClientId($client_id);
        Gateway::sendToUid(
            $uid,
            json_encode(['type' => 'connect', 'data' => ['servicer_id' => 111]])
        );
        return json_encode(['code'=>1, 'msg'=>'发送成功']);
    }
}
