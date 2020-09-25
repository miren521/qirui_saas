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


namespace addon\supply\model;

use app\model\BaseModel;
use Exception;

/**
 * 供货商服务申请
 */
class SupplyService extends BaseModel
{
    //服务申请状态
    private $status = [
        1  => '审核通过',
        0  => '待审核',
        -1 => '审核失败',
    ];
    //服务项目
    private $service_list = [
        [
            'name'    => '7天退换',
            'desc'    => '7天无理由退货',
            'key'     => 'service_qtian',
            'icon'    => 'public/static/img/shop/replacement.png',
            'pc_icon' => 'icon7tiantuihuan'
        ],
        [
            'name'    => '正品保障',
            'desc'    => '正品保障',
            'key'     => 'service_zhping',
            'icon'    => 'public/static/img/shop/quality_goods.png',
            'pc_icon' => 'iconzhengpinbaozheng'
        ],
        [
            'name'    => '两小时发货',
            'desc'    => '两小时发货',
            'key'     => 'service_erxiaoshi',
            'icon'    => 'public/static/img/shop/delivery.png',
            'pc_icon' => 'iconliangxiaoshifahuo'
        ],
        [
            'name'    => '退货承诺',
            'desc'    => '退货承诺',
            'key'     => 'service_tuihuo',
            'icon'    => 'public/static/img/shop/return_goods.png',
            'pc_icon' => 'iconchengnuotuihuo1'
        ],
        [
            'name'    => '试用中心',
            'desc'    => '试用中心',
            'key'     => 'service_shiyong',
            'icon'    => 'public/static/img/shop/trial.png',
            'pc_icon' => 'iconshiyanzhongxin'
        ],
        [
            'name'    => '实体验证',
            'desc'    => '实体验证',
            'key'     => 'service_shiti',
            'icon'    => 'public/static/img/shop/entity.png',
            'pc_icon' => 'iconshitiyanzheng1'
        ],
        [
            'name'    => '消协保证',
            'desc'    => '消协保证',
            'key'     => 'service_xiaoxie',
            'icon'    => 'public/static/img/shop/ensure;.png',
            'pc_icon' => 'iconxiaoxiebaozheng1'
        ]
    ];

    /**
     * 获取服务申请信息
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getServiceInfo($condition, $field = '*')
    {
        $info = model('supply_service')->getInfo($condition, $field);
        return $this->success($info);
    }

    /**
     * 获取服务申请列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getServiceList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('supply_service')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取服务申请分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getServicePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('supply_service')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 商家服务列表
     * @param $site_id
     * @return array
     */
    public function serviceApplyList($site_id)
    {
        $service_list_arr = $this->service_list;

        $condition = [];
        //获取该服务商的服务状态
        $supply_service_info = model('supplier')->getInfo(
            [['supplier_site_id', '=', $site_id]],
            'supplier_site_id,title,service_qtian,service_zhping,service_erxiaoshi,
            service_tuihuo,service_shiyong,service_shiti,service_xiaoxie'
        );
        //通过全局配置查询对应的服务信息
        foreach ($service_list_arr as $key => $value) {
            $service_key                      = $value['key'];
            $service_list_arr[$key]['status'] = $supply_service_info[$service_key];

            $condition[$key][] = ['site_id', '=', $site_id];
            $condition[$key][] = ['service_type', '=', $service_key];
            // 获取最近一条记录审核的状态
            $supply_service = model('supply_service')->getFirstData($condition[$key], '', 'apply_id desc');

            $service_list_arr[$key]['apply_status'] = 2;
            $service_list_arr[$key]['remark']       = '';

            if ($supply_service['service_type'] == $value['key']) {
                // 查询到对应的信息,进行审核状态的获取
                $service_list_arr[$key]['apply_status'] = $supply_service['status'];
                $service_list_arr[$key]['remark']       = $supply_service['remark'];
            }
        }
        return $service_list_arr;
    }

    /**
     * 修改服务申请
     * @param $data
     * @param $condition
     * @return array
     */
    public function editService($data, $condition)
    {
        $res = model('supply_service')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 审核通过
     * @param $apply_id
     * @return array
     */
    public function servicePass($apply_id)
    {
        // 开启事务
        model('supply_service')->startTrans();
        try {
            //获取服务申请信息
            $service_info = model('supply_service')->getInfo([['apply_id', '=', $apply_id]]);
            //获取站点ID
            $site_id = $service_info['site_id'];

            $key = array_search($service_info['service_type'], array_column($this->service_list, 'key'));

            $service_type_key = $this->service_list[$key]['key'];

            // 商城供货商信息修改
            model('supplier')->update([$service_type_key => 1], [['supplier_site_id', '=', $site_id]]);
            // 服务记录修改
            $res = model('supply_service')->update(
                ['status' => 1, 'audit_time' => time()],
                [['apply_id', '=', $apply_id]]
            );
            // 事务提交
            model('supply_service')->commit();
            return $this->success($res);
        } catch (Exception $e) {
            // 事务回滚
            model('supply_service')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 审核拒绝
     * @param $apply_id
     * @param $reason
     * @return array
     */
    public function serviceReject($apply_id, $reason)
    {
        $res = model('supply_service')->update(['status' => -1, 'remark' => $reason], [['apply_id', '=', $apply_id]]);
        return $this->success($res);
    }

    /**
     * 获取服务申请状态
     */
    public function getServiceStatus()
    {
        return $this->status;
    }

    /**
     * 获取服务列表
     */
    public function getServiceNameList()
    {
        return $this->service_list;
    }

    /**
     * 商家服务申请
     * @param $reopen_data
     * @return array
     */
    public function serviceApply($reopen_data)
    {
        $res = model('supply_service')->add($reopen_data);
        return $this->success($res);
    }

    /**
     * 商家服务退出
     * @param $data
     * @param $site_id
     * @return array
     */
    public function serviceQuit($data, $site_id)
    {
        // 商城供货商信息修改
        $res = model('supplier')->update($data, [['supplier_site_id', '=', $site_id]]);
        return $this->success($res);
    }
}
