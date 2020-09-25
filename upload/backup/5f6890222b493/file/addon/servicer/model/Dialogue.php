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

namespace addon\servicer\model;

use app\model\BaseModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

class Dialogue extends BaseModel
{
    /**
     * 消息类型，字符串
     */
    const CONTENTTYPE_STRING = 0;
    /**
     * 消息类型，商品SKU
     */
    const CONTENTTYPE_GOODSKU = 1;
    /**
     * 消息类型，订单
     */
    const CONTENTTYPE_ORDER = 2;
    /**
     * 消息类型，图片
     */
    const CONTENTTYPE_IMAGE = 3;

    /**
     * 插入对话数据
     * @param integer $type 会话方向，0 客户，1 客服
     * @param integer $memberId 会员ID
     * @param integer $serviceId 客服ID
     * @param integer $contentType 内容类型
     * @param integer $read 是否已读
     * @param integer $shopId 商户ID
     * @param integer $userId 用户ID
     * @param string $consumerSay 客户咨询内容
     * @param string $servicerSay 客服回答内容
     * @param integer $goodSkuId 商品ID
     * @param integer $orderId 订单ID
     * @return integer
     */
    public function createDialogue($type, $memberId, $serviceId, $contentType, $read, $shopId, $userId, $consumerSay = '', $servicerSay = '', $goodSkuId = 0, $orderId = 0)
    {
        return model('servicer_dialogue')->add([
            'member_id' => $memberId,
            'servicer_id' => $serviceId,
            'create_day' => date('Y-m-d'),
            'create_time' => date('H:i:s'),
            'content_type' => $contentType,
            'read' => $read,
            'shop_id' => $shopId,
            'goods_sku_id' => $goodSkuId,
            'order_id' => $orderId,
            'consumer_say' => $consumerSay,
            'servicer_say' => $servicerSay,
            'type' => $type
        ]);
    }

    /**
     * 设置消息已读状态
     * @param $id
     * @param bool $read
     * @return int
     * @throws DbException
     */
    public function setDialogueRead($id, $read = true)
    {
        return model('servicer_dialogue')->setFieldValue(['id' => $id], 'read', $read ? 1 : 0);
    }

    /**
     * 获取会话详情
     * @param $id
     * @return array|Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getDialogue($id)
    {
        return model('servicer_dialogue')->getInfo(['id' => $id]);
    }

    /**
     * 查询聊天记录
     * @param $memberId
     * @param int $page
     * @param int $limit
     * @param int $siteId
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getDialogueList($memberId, $page = 1, $limit = 20, $siteId = 0)
    {
        $alias = 'sd';
        $field = ['sd.id', 'sd.create_day', 'sd.type', 'sd.create_time', 'sd.content_type', 'sd.read', 'sd.goods_sku_id', 'sd.order_id', 'sd.consumer_say', 'sd.servicer_say'];
        $order = ['sd.id' => 'desc'];

        $join = [
            ['servicer s', 's.id = sd.servicer_id', 'LEFT'], // 关联客服
            ['goods_sku g', 'g.sku_id = sd.goods_sku_id', 'LEFT'], // 关联商品，聊天记录为商品时
            ['order o', 'o.order_id = sd.order_id', 'LEFT'],
            ['order_goods og', 'og.order_id = o.order_id', 'LEFT']
        ];

        $field = array_merge($field, ['s.is_platform']);
        $field = array_merge($field, ['g.goods_name', 'g.sku_image', 'g.price']);
        $field = array_merge($field, ['o.order_no', 'o.goods_money', 'o.order_money', 'o.order_status', 'o.order_status_name']);
        $field = array_merge($field, ['og.sku_name', 'og.sku_image']);

        $condition[] = ['sd.member_id', '=', $memberId];
        $condition[] = ['sd.shop_id', '=', $siteId];

        return model('servicer_dialogue')->pageList($condition, $field, $order, $page, $limit, $alias, $join, null, $limit);
    }
}
