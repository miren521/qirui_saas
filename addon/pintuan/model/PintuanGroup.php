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

namespace addon\pintuan\model;

use app\model\BaseModel;
use app\model\order\OrderCommon;
use app\model\order\OrderRefund;
use app\model\order\VirtualOrder;
use app\model\system\Cron;

/**
 * 拼团组
 */
class PintuanGroup extends BaseModel
{

    /**
     * 创建拼团组
     * @param $pintuan_order_info
     * @return array|\multitype
     */
    public function addPintuanGroup($pintuan_order_info)
    {
        model('promotion_pintuan_group')->startTrans();
        //获取拼团信息
        $pintuan_model = new Pintuan();
        $pintuan_id = $pintuan_order_info[ 'pintuan_id' ];
        $pintuan = $pintuan_model->getPintuanInfo([['pintuan_id', '=', $pintuan_id]]);
        $pintuan_info = $pintuan[ 'data' ];

        try {
            $data = [
                'site_id' => $pintuan_info[ 'site_id' ],
                'goods_id' => $pintuan_info[ 'goods_id' ],
                'is_virtual_goods' => $pintuan_info[ 'is_virtual_goods' ],
                'pintuan_id' => $pintuan_order_info[ 'pintuan_id' ],
                'head_id' => $pintuan_order_info[ 'head_id' ],
                'pintuan_num' => $pintuan_info[ 'pintuan_num' ],
                'pintuan_count' => 1,
                'create_time' => time(),
                'end_time' => time() + ($pintuan_info[ 'pintuan_time' ] * 60),
                'status' => 2,
                'is_virtual_buy' => $pintuan_info[ 'is_virtual_buy' ],
                'is_single_buy' => $pintuan_info[ 'is_single_buy' ],
                'is_promotion' => $pintuan_info[ 'is_promotion' ],
                'buy_num' => $pintuan_info[ 'buy_num' ],
            ];
            $res = model('promotion_pintuan_group')->add($data);

            //添加拼团组关闭事件
            $cron = new Cron();
            $cron->addCron(1, 0, "拼团组关闭", "ClosePintuanGroup", $data[ 'end_time' ], $res);

            //更新拼团开组人数及购买人数
            $pintua_data = [
                'group_num' => $pintuan_info[ 'group_num' ] + 1,
                'order_num' => $pintuan_info[ 'order_num' ] + 1,
            ];

            $pintuan_model->editPintuanNum($pintua_data, [['pintuan_id', '=', $pintuan_order_info[ 'pintuan_id' ]]]);

            model('promotion_pintuan_group')->commit();
            return $this->success($res);
        } catch ( \Exception $e ) {

            model('promotion_pintuan_group')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 编辑组信息
     * @param array $condition
     * @param array $data
     * @return array
     */
    public function editPintuanGroup($condition = [], $data = [])
    {
        $res = model('promotion_pintuan_group')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 加入拼团组
     * @param $pintuan_order_info
     * @return array|\multitype
     */
    public function joinPintuanGroup($pintuan_order_info)
    {
        model('promotion_pintuan_group')->startTrans();
        //获取拼团信息
        $pintuan_model = new Pintuan();
        $pintuan_id = $pintuan_order_info[ 'pintuan_id' ];
        $pintuan = $pintuan_model->getPintuanInfo([['pintuan_id', '=', $pintuan_id]]);
        $pintuan_info = $pintuan[ 'data' ];

        try {

            //更新拼团购买人数
            $pintuan_model->editPintuanNum(['order_num' => $pintuan_info[ 'order_num' ] + 1], [['pintuan_id', '=', $pintuan_order_info[ 'pintuan_id' ]]]);

            //获取拼团组信息
            $group = $this->getPintuanGroupInfo([['group_id', '=', $pintuan_order_info[ 'group_id' ]]]);
            $group_info = $group[ 'data' ];
            //更新拼团组当前数量及状态
            $pintuan_count = $group_info[ 'pintuan_count' ] + 1;
            $res = $this->editPintuanGroup([['group_id', '=', $pintuan_order_info[ 'group_id' ]]], ['pintuan_count' => $pintuan_count]);

            if ($pintuan_count == $group_info[ 'pintuan_num' ]) {//已成团

                //修改拼团组状态
                model('promotion_pintuan_group')->update(['status' => 3], [['group_id', '=', $pintuan_order_info[ 'group_id' ]]]);

                //查询该组所有订单
                $pintuan_order_model = new PintuanOrder();
                $pintuan_order = $pintuan_order_model->getPintuanOrderList([['group_id', '=', $pintuan_order_info[ 'group_id' ]]], 'order_id,pintuan_status');
                $pintuan_order_list = $pintuan_order[ 'data' ];

                $order_model = new OrderCommon();
                if (!empty($pintuan_order_list)) {
                    foreach ($pintuan_order_list as $v) {
                        switch ($v[ 'pintuan_status' ]) {

                            case 0:
                                //将未支付的修改为失败
                                model('promotion_pintuan_order')->update(['pintuan_status' => 1], [['order_id', '=', $v[ 'order_id' ]]]);
                                //开放订单
                                $order_model->orderUnlock($v[ 'order_id' ]);
                                //关闭订单
                                $close_condition = array(
                                    ['order_id', '=', $v[ 'order_id' ]],
                                );
                                $result = $order_model->orderClose($close_condition);
                                if ($result[ "code" ] < 0) {
                                    model('promotion_pintuan_group')->rollback();
                                    return $result;
                                }
                                //更新订单营销状态名称
                                model('order')->update(['promotion_status_name' => '拼团失败'], [['order_id', '=', $v[ 'order_id' ]]]);
                                break;
                            case 2://已支付

                                //将已支付的修改为成功
                                model('promotion_pintuan_order')->update(['pintuan_status' => 3], [['order_id', '=', $v[ 'order_id' ]]]);
                                //开放订单
                                $order_model->orderUnlock($v[ 'order_id' ]);
                                //更新订单营销状态名称
                                model('order')->update(['promotion_status_name' => '拼团成功', 'is_enable_refund' => 1], [['order_id', '=', $v[ 'order_id' ]]]);
                                //针对虚拟订单执行收发货操作
                                if ($group_info[ 'is_virtual_goods' ] == 1) {
                                    $order_common_model = new OrderCommon();
                                    $take_condition = array(
                                        ['order_id', '=', $v[ 'order_id' ]],
                                    );
                                    $order_common_model->orderCommonTakeDelivery($take_condition);
                                }
                                break;
                        }

                    }
                }

            }

            model('promotion_pintuan_group')->commit();
            return $this->success($res);
        } catch ( \Exception $e ) {

            model('promotion_pintuan_group')->rollback();
            return $this->error('', $e->getMessage());
        }

    }

    /**
     * 查询拼团组信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getPintuanGroupInfo($condition = [], $field = '*')
    {
        $group_info = model('promotion_pintuan_group')->getInfo($condition, $field);
        return $this->success($group_info);
    }

    /**
     * 获取组列表
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getPintuanGroupList($condition = [], $field = '*')
    {
        $list = model('promotion_pintuan_group')->getList($condition, $field);
        return $this->success($list);
    }

    /**
     * 获取拼团组商品列表
     * @param array $condition
     * @return array
     */
    public function getPintuanGoodsGroupList($condition = [])
    {
        $field = 'ppg.group_id,ppg.goods_id,ppg.pintuan_id,ppg.head_id,ppg.pintuan_num,ppg.pintuan_count,ppg.create_time,ppg.end_time,ppg.status,ppg.is_single_buy,ppg.is_promotion,ppg.buy_num,m.member_id,m.nickname,m.headimg';
        $alias = 'ppg';
        $join = [
            [
                'member m',
                'ppg.head_id = m.member_id',
                'inner'
            ]
        ];
        $list = model('promotion_pintuan_group')->getList($condition, $field, 'ppg.create_time desc', $alias, $join);
        return $this->success($list);
    }

    /**
     * 获取拼团组分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     */
    public function getPintuanGroupPageList($pintuan_id, $condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
    {
        if ($pintuan_id) {
            $pintuan_field = 'p.*,g.goods_name,g.goods_image';
            $pintuan_alias = 'p';
            $pintuan_join = [
                [
                    'goods g',
                    'p.goods_id = g.goods_id',
                    'inner'
                ]
            ];
            $pintuan_condition[] = ['p.pintuan_id', '=', $pintuan_id];
            $pintuan_info = model('promotion_pintuan')->getInfo($pintuan_condition, $pintuan_field, $pintuan_alias, $pintuan_join);

            $condition[] = ['pg.pintuan_id', '=', $pintuan_id];
        } else {
            $pintuan_info = [];
        }
        $field = 'pg.*,g.goods_name,g.goods_image';
        $alias = 'pg';
        $join = [
            [
                'goods g',
                'pg.goods_id = g.goods_id',
                'inner'
            ]
        ];
        $list = model('promotion_pintuan_group')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);

        $data = [
            'pintuan_info' => $pintuan_info,
            'list' => $list
        ];
        return $this->success($data);
    }

    /**
     * 关闭拼团组
     * @param $group_id
     * @return array|\multitype
     */
    public function cronClosePintuanGroup($group_id)
    {
        model('promotion_pintuan_group')->startTrans();
        try {

            //获取拼团组信息
            $pintuan_group = model('promotion_pintuan_group')->getInfo([['group_id', '=', $group_id]], 'status,is_virtual_buy,is_virtual_goods');
            if (!empty($pintuan_group)) {

                if ($pintuan_group[ 'status' ] == 2) {
                    //关闭所有已支付的订单
                    $res = $this->closePaidGroupOrder($group_id, $pintuan_group[ 'is_virtual_buy' ], $pintuan_group[ 'is_virtual_goods' ]);
                    if ($res[ 'code' ] < 0) {
                        model('promotion_pintuan_group')->rollback();
                        return $res;
                    }

                }
            }

            model('promotion_pintuan_group')->commit();
            return $this->success();
        } catch ( \Exception $e ) {
            model('promotion_pintuan_group')->rollback();
            return $this->error('', $e->getMessage());
        }

    }

    /**
     * 关闭拼团组未支付订单
     * @param $group_id
     * @return array|\multitype
     */
    public function closeUnpaidGroupOrder($group_id)
    {
        //获取所有改组未支付的订单
        $pintuan_order_model = new PintuanOrder();
        $unpaid_order = $pintuan_order_model->getPintuanOrderList([['group_id', '=', $group_id], ['pintuan_status', '=', 0]], 'order_id');
        $unpaid_order_list = $unpaid_order[ 'data' ];

        model('promotion_pintuan_order')->startTrans();

        $order_model = new OrderCommon();
        try {
            if (!empty($unpaid_order_list)) {
                foreach ($unpaid_order_list as $v) {

                    //修改拼团订单状态
                    model('promotion_pintuan_order')->update(['pintuan_status' => 1], [['order_id', '=', $v[ 'order_id' ]]]);
                    //解除锁定
                    $order_model->orderUnlock($v[ 'order_id' ]);
                    //关闭订单
                    $close_condition = array(
                        ['order_id', '=', $v[ 'order_id' ]],
                    );
                    $result = $order_model->orderClose($close_condition);
                    if ($result[ "code" ] < 0) {
                        model('promotion_pintuan_group')->rollback();
                        return $result;
                    }
                    //更新订单营销状态名称
                    model('order')->update(['promotion_status_name' => '拼团失败'], [['order_id', '=', $v[ 'order_id' ]]]);
                }
            }

            model('promotion_pintuan_order')->commit();
            return $this->success();
        } catch ( \Exception $e ) {
            model('promotion_pintuan_order')->rollback();
            return $this->error('', $e->getMessage());
        }

    }

    /**
     * 关闭拼团组已支付订单
     * @param $group_id
     * @param $is_virtual_buy
     * @param $is_virtual_goods
     * @return array|\multitype
     */
    public function closePaidGroupOrder($group_id, $is_virtual_buy, $is_virtual_goods)
    {
        //获取所有该组的订单
        $pintuan_order_model = new PintuanOrder();
        $paid_order = $pintuan_order_model->getPintuanOrderList([['group_id', '=', $group_id]], 'order_id,pintuan_status');
        $paid_order_list = $paid_order[ 'data' ];

        $order_model = new OrderCommon();
        model('promotion_pintuan_group')->startTrans();

        try {
            if ($is_virtual_buy == 1) {//虚拟成团
                //修改拼团组状态（成功）
                $res = model('promotion_pintuan_group')->update(['status' => 3], [['group_id', '=', $group_id]]);

                if (!empty($paid_order_list)) {
                    foreach ($paid_order_list as $v) {
                        switch ($v[ 'pintuan_status' ]) {
                            case 0:
                                //将未支付的修改为失败
                                model('promotion_pintuan_order')->update(['pintuan_status' => 1], [['order_id', '=', $v[ 'order_id' ]]]);
                                //解除锁定
                                $order_model->orderUnlock($v[ 'order_id' ]);
                                //关闭订单
                                $close_condition = array(
                                    ['order_id', '=', $v[ 'order_id' ]],
                                );
                                $result = $order_model->orderClose($close_condition);
                                if ($result[ "code" ] < 0) {
                                    model('promotion_pintuan_group')->rollback();
                                    return $result;
                                }
                                //更新订单营销状态名称
                                model('order')->update(['promotion_status_name' => '拼团失败'], [['order_id', '=', $v[ 'order_id' ]]]);
                                break;
                            case 2://已支付

                                //解除锁定
                                $order_model->orderUnlock($v[ 'order_id' ]);
                                //将已支付的修改为成功
                                model('promotion_pintuan_order')->update(['pintuan_status' => 3], [['order_id', '=', $v[ 'order_id' ]]]);
                                //更新订单营销状态名称
                                model('order')->update(['promotion_status_name' => '拼团成功', 'is_enable_refund' => 1], [['order_id', '=', $v[ 'order_id' ]]]);
                                //针对虚拟订单执行收发货操作
                                if ($is_virtual_goods == 1) {
                                    $Virtual_model = new VirtualOrder();
                                    $Virtual_model->orderTakeDelivery($v[ 'order_id' ]);
                                }
                                break;
                        }
                    }
                }

            } else {//未开启虚拟成团

                //修改拼团组状态为失败
                $res = model('promotion_pintuan_group')->update(['status' => 1], [['group_id', '=', $group_id]]);
                if (!empty($paid_order_list)) {
                    foreach ($paid_order_list as $v) {
                        switch ($v[ 'pintuan_status' ]) {
                            case 0:

                                //将未支付的修改为失败
                                model('promotion_pintuan_order')->update(['pintuan_status' => 1], [['order_id', '=', $v[ 'order_id' ]]]);
                                //解除锁定
                                $order_model->orderUnlock($v[ 'order_id' ]);
                                //关闭订单
                                $close_condition = array(
                                    ['order_id', '=', $v[ 'order_id' ]],
                                );
                                $result = $order_model->orderClose($close_condition);
                                if ($result[ "code" ] < 0) {
                                    model('promotion_pintuan_group')->rollback();
                                    return $result;
                                }
                                //更新订单营销状态名称
                                model('order')->update(['promotion_status_name' => '拼团失败'], [['order_id', '=', $v[ 'order_id' ]]]);
                                break;

                            case 2:

                                //关闭拼团订单
                                model('promotion_pintuan_order')->update(['pintuan_status' => 1], [['order_id', '=', $v[ 'order_id' ]]]);
                                //解除锁定
                                $order_model->orderUnlock($v[ 'order_id' ]);

                                //主动退款
                                $order_refund_model = new OrderRefund();
                                $refund_result = $order_refund_model->activeRefund($v[ 'order_id' ], "拼团订单关闭", '拼团订单关闭');
                                if ($refund_result[ "code" ] < 0) {
                                    model('promotion_pintuan_group')->rollback();
                                    return $refund_result;
                                }

                                //关闭订单
                                $close_condition = array(
                                    ['order_id', '=', $v[ 'order_id' ]],
                                );
                                $result = $order_model->orderClose($close_condition);
                                if ($result[ "code" ] < 0) {
                                    model('promotion_pintuan_group')->rollback();
                                    return $result;
                                }
                                //更新订单营销状态名称
                                model('order')->update(['promotion_status_name' => '拼团失败'], [['order_id', '=', $v[ 'order_id' ]]]);
                                break;

                        }

                    }
                }
            }

            model('promotion_pintuan_group')->commit();
            return $this->success($res);
        } catch ( \Exception $e ) {
            model('promotion_pintuan_group')->rollback();
            return $this->error('', $e->getMessage());
        }

    }
}