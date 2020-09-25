<?php

namespace addon\servicer\model;

use app\model\BaseModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 客服
 */
class Servicer extends BaseModel
{
    /**
     * 新建客服
     * @param $isPlatform
     * @param $siteId
     * @param $userId
     * @param int $online
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function createServicer($isPlatform, $siteId, $userId, $online = 0)
    {
        // 客服与会员一对一绑定。也就是说，一个用户只能关联一个客服数据
        $servicerExists = model('servicer')->getInfo(['user_id' => $userId]);
        if ($servicerExists) {
            return $this->success($servicerExists);
        }

        $model = model('servicer')->add([
            'is_platform' => $isPlatform, // 此处一定为平台客服
            'shop_id' => $siteId,
            'user_id' => $userId,
            'create_time' => time(),
            'last_online_time' => time(),
            'delete_time' => 0,
            'client_id' => '',
            'online' => $online,
        ]);

        return $this->success($model);
    }

    /**
     * 获取在线客服列表
     * @param $site_id
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getOnlineServicers($site_id)
    {
        $list = model('servicer')->getList(['shop_id' => $site_id, 'online' => 1]);
//        if (empty($list) || count($list) == 0) {
//            return $this->error('没有在线客服');
//        }

        return $this->success($list);
    }

    /**
     * 获取会员信息
     * @param $where
     * @param bool $field
     * @return array|\think\Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getServicer($where, $field = true)
    {
        return model('servicer')->getInfo($where, $field);
    }

    /**
     * 设置客服在线状态
     * @param $servicerId
     * @param $clientId
     * @param bool $online
     * @return int
     * @throws DbException
     */
    public function setServicerOnlineStatus($servicerId, $clientId, $online = true)
    {
        if (!$online) {
            model('servicer_member')->update(
                ['client_id' => '', 'online' => $online],
                [['servicer_id', '=', $servicerId]]
            );
        }
        return model('servicer')->update(
            ['client_id' => $online?$clientId:'', 'online' => $online, 'last_online_time' => time()],
            [['user_id', '=', $servicerId]]
        );
    }

    /**
     * 获取客服列表
     * @param array $condition
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getServicerList($condition = [])
    {
        $alias = 'sm';
        $field = ['id'];

        return model('servicer')->getList($condition, $field, '', $alias);
    }

    /**
     * 获取当前客服服务的会员列表
     * @param $user_id
     * @param int $page
     * @param int $limit
     * @return mixed|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getCurrentUserChatMembers($user_id, $page = 1, $limit = 10)
    {
        $servicer_ids = model('servicer')->getColumn(['user_id' => $user_id], 'id');
        if (empty($servicer_ids)) {
            return null;
        }

        $alias = 'sm';
        $field = ['sm.id', 'sm.member_id', 'sm.servicer_id', 'sm.member_name', 'sm.online', 'sm.create_time', 'sm.headimg'];

        $condition = ['sm.servicer_id', 'in', $servicer_ids];
        $order = ['id' => 'desc'];

        return model('servicer')->pageList($condition, $field, $order, $page, 10, $alias, [], null, $limit);
    }

    /**
     * 删除客服
     * @param $servicerId
     * @return array
     * @throws DbException
     */
    public function delServicer($servicerId)
    {
        $del = model('servicer')->delete(['id' => $servicerId]);
        if ($del) {
            return $this->success();
        }

        return $this->error();
    }

    /**
     * 为当前会员匹配一个客服人员
     * @param $site_id
     * @param $member_id
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getUsefulServicer($site_id, $member_id)
    {
        // 如果当前会员有之前联系过的该商户下的客服且该客服在线，则默认绑定该客户至该客服
        // $alias = 's';
        // $join = [
        //     ['servicer_member sm', 'sm.servicer_id = s.user_id', 'left'],
        // ];
        // $condition = [['s.online', '=', 1], ['s.shop_id', '=', $site_id], ['sm.member_id', '=', $member_id]];
        // $field = ['s.id', 's.online', 's.user_id'];
        // $model = @model('servicer')->getInfo($condition, $field, $alias, $join);
        // if (!empty($model) && $model['id'] > 0) {
        //     return [$model];
        // }

        /**
         * 目前采用的是最闲优先匹配原则
         */
        $alias = 's';
        $condition = [['s.online', '=', 1], ['s.shop_id', '=', $site_id]];
        $join = [
            ['servicer_member sm', 'sm.servicer_id = s.user_id and sm.online=1', 'left'],
            ['user u', 'u.uid = s.user_id', 'inner']
        ];

        $field = ['s.id', 's.online', 'count(sm.`member_id`) chat_count', 's.user_id'];

        $group = 'sm.member_id';

        return model('servicer')->getList($condition, $field, 'chat_count asc', $alias, $join, $group, 1);
    }
}
