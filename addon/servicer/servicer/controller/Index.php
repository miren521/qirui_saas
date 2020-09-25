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


namespace addon\servicer\servicer\controller;

use addon\servicer\model\Dialogue;
use addon\servicer\model\Member;
use GatewayClient\Gateway;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 门店首页
 * @author Administrator
 *
 */
class Index extends BaseServicer
{
    /**
     * 首页
     * @return array|mixed|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function index()
    {
        $page = input('page', 1);

        $memberModel   = new Member();
        $condition     = [
            ['servicer_id', '=', $this->uid],
            ['online', '=', 1]
        ];
        $onlineMembers = $memberModel->getList($condition);

        $condition      = [
            ['servicer_id', '=', $this->uid],
//            ['online', '=', 0]
        ];
        $offlineMembers = $memberModel->getPageList($condition, true, 'last_online_time desc', $page);

        if (request()->isAjax()) {
            return $this->result(['onlineMembers' => $onlineMembers, 'offlineMembers' => $offlineMembers]);
        }

        $this->assign('servicer', $this->user_info);
        $this->assign('online_members', $onlineMembers);
        $this->assign('online_members_count', @count($onlineMembers) ?? 0);
        $this->assign('offline_members', @$offlineMembers['list'] ?? []);
        $this->assign('offline_members_count', @$offlineMembers['count'] ?? 0);

        $this->assign("menu_info", ['title' => "聊天室"]);
        return $this->fetch("index/index", [], $this->replace);
    }

    /**
     * 获取聊天记录表
     * @return array|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function dialogs()
    {
        $member_id = input('member_id', 0);
        if (empty($member_id)) {
            return $this->result('', -1, '没有指定会员');
        }

        $page  = input('page', 1);
        $limit = input('limit', 15);

        $pagelist = (new Dialogue())->getDialogueList($member_id, $page, $limit, $this->site_id);

        return $this->result($pagelist);
    }

    /**
     * 获取会员详情
     * @return array|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getMember()
    {
        $member_id = input('member_id', 0);
        if (empty($member_id)) {
            return $this->result('', -1, '没有指定会员');
        }

        $member = (new Member)->getMember($member_id, $this->uid);
        return $this->result($member);
    }

    /**
     * 历史聊天会员
     * @return array|void
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function historyMembers()
    {
        $page = input('page', 1);
        $size = input('size', 10);

        $memberModel = new Member();

        $condition      = [
            ['servicer_id', '=', $this->uid],
//            ['online', '=', 0]
        ];
        $offlineMembers = $memberModel->getPageList($condition, true, 'last_online_time desc', $page, $size);

        return $this->result(['offlineMembers' => $offlineMembers]);
    }

    public function history()
    {
        $this->assign("menu_info", ['title' => "聊天记录"]);
        return $this->fetch("index/history", [], $this->replace);
    }
}
