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


namespace addon\servicer\model;

use app\model\BaseModel;
use app\model\member\Member as MemberServices;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class Member extends BaseModel
{
    /**
     * 设置会员在线状态
     * @param integer $memberId 会员编号
     * @param boolean $online 是否在线
     * @return int
     * @throws DbException
     */
    public function setMemberOnline($memberId, $online = true)
    {
        return model('servicer_member')->update(['online'=> $online ? 1 : 0], [['member_id', '=', $memberId]]);
    }

    /**
     * 获取会员数据
     * @param $memberId
     * @param $servicerId
     * @return array|\think\Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getMember($memberId, $servicerId)
    {
        $alias = 'sm';
        $condition = [
            ['sm.member_id', '=', $memberId],
            ['sm.servicer_id', '=', $servicerId]
        ];
        $join = [
            ['member m', 'm.member_id = sm.member_id', 'left'],
        ];
        $fields = ['sm.id', 'sm.member_id', 'sm.servicer_id', 'sm.member_name', 'sm.online', 'sm.create_time', 'sm.last_online_time', 'sm.delete_time', 'sm.headimg', 'sm.client_id', 'm.nickname', 'm.username', 'm.headimg'];

        $model = model('servicer_member')->getInfo($condition, $fields, $alias, $join);

        if (empty($model)) {
            return null;
        }

        $dialogs = (new Dialogue)->getDialogueList($memberId, 1, 1);
        $dialog = [];
        if (!empty($dialogs) && !empty($dialogs['list']) && count($dialogs['list']) > 0) {
            $dialog = $dialogs['list'][0];
        }

        $model['last_dialog'] = $dialog;
        return $model;
    }

    /**
     * 新建聊天咨询用户
     * @param integer $memberId 会员ID
     * @param integer $servicerId 客服ID
     * @param boolean $online 在线状态
     * @param string $client_id 临时会话ID
     * @return int|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function createMember($memberId, $servicerId, $online = true, $client_id = '')
    {

        $memberService = new MemberServices();
        $memberInfo = $memberService->getMemberInfo(['member_id' => $memberId], ['member_id', 'headimg', 'username']);
        if (empty($memberInfo['data'])) {
            return $memberInfo['code'];
        }
        $memberInfo = $memberInfo['data'];

        $memberServicer = model('servicer_member')->getInfo(['servicer_id' => $servicerId, 'member_id' => $memberId]);
        if (!empty($memberServicer)) {
            // 更新在线状态、时间
            return model('servicer_member')->update(
                [
                    'online' => $online, 'last_online_time' => time(), 'client_id' => $client_id,
                    'member_name' => $memberInfo['username'], 'headimg' => $memberInfo['headimg']
                ],
                ['member_id' => $memberId, 'servicer_id' => $servicerId,]
            );
            return -1;
        }

        return model('servicer_member')->add([
            'member_id' => $memberId,
            'servicer_id' => $servicerId,
            'member_name' => $memberInfo['username'],
            'online' => $online,
            'create_time' => time(),
            'last_online_time' => time(),
            'delete_time' => 0,
            'headimg' => $memberInfo['headimg'],
            'client_id' => $client_id,
        ]);
    }

    /**
     * 获取客服关联会员列表
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param int $page
     * @param int $list_rows
     * @param string $alias
     * @param array $join
     * @param null $group
     * @param null $limit
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getPageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = [], $group = null, $limit = null)
    {
        return model('servicer_member')->pageList($condition, $field, $order, $page, $list_rows, $alias, $join, $group, $limit);
    }

    /**
     * 获取数据列表
     * @param $condition
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList($condition)
    {
        return model('servicer_member')->getList($condition);
    }
}
