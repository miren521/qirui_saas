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

namespace addon\fenxiao\model;

use app\model\BaseModel;


/**
 * 分销
 */
class FenxiaoAccount extends BaseModel
{
    public $type = [
        'withdraw' => '提现',
        'order' => '订单结算',
    ];

    /**
     * 添加账户流水
     * @param $fenxiao_id
     * @param $fenxiao_name
     * @param string $type
     * @param $money
     * @param $relate_id
     * @return array
     */
    public function addAccount($fenxiao_id,$fenxiao_name, $type = 'order', $money, $relate_id)
    {
        $account_no = date('YmdHi').rand(1000,9999);
        $data = array(
            'fenxiao_id' => $fenxiao_id,
            'fenxiao_name' => $fenxiao_name,
            'account_no' => $account_no,
            'money' => $money,
            'type' => $type,
            'type_name' => $this->type[$type],
            'relate_id' => $relate_id,
            'create_time' => time(),
        );

        $res = model('fenxiao_account')->add($data);
        model('fenxiao')->setInc([['fenxiao_id','=',$fenxiao_id]],'account',$money);

        return $this->success($res);
    }


    /**
     * 获取分销商账户流水分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getFenxiaoAccountPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = '*')
    {
        $list = model('fenxiao_account')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 添加账户流水-不变更金额
     * @param $fenxiao_id
     * @param $fenxiao_name
     * @param string $type
     * @param $money
     * @param $relate_id
     * @return array
     */
    public function addAccountLog($fenxiao_id, $fenxiao_name, $type, $money, $relate_id)
    {
        $account_no = date('YmdHi') . rand(1000, 9999);
        $data = array(
            'fenxiao_id' => $fenxiao_id,
            'fenxiao_name' => $fenxiao_name,
            'account_no' => $account_no,
            'money' => $money,
            'type' => $type,
            'type_name' => $this->type[$type],
            'relate_id' => $relate_id,
            'create_time' => time(),
        );
        $res = model('fenxiao_account')->add($data);
        return $this->success($res);
    }
}