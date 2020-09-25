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


namespace addon\supply\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\supply\model\SupplyService as SupplyServiceModel;

/**
 * 商家服务申请
 */
class Services extends BaseAdmin
{
    /******************************* 商家申请列表及相关操作 ***************************/

    /**
     * 商家申请列表
     */
    public function lists()
    {
        $shop_service_model = new SupplyServiceModel();
        if (request()->isAjax()) {
            $page         = input('page', 1);
            $page_size    = input('page_size', PAGE_LIST_ROWS);
            $search_text  = input('search_text', '');
            $status       = input('status', '');
            $service_type = input('service_type', '');
            $site_id      = input("site_id", "");
            $start_time   = input("start_time", '');
            $end_time     = input("end_time", '');
            $condition    = [];
            $condition[]  = ['site_name|service_type_name', 'like', '%' . $search_text . '%'];

            if ($status != '') {
                if ($status == 2) {
                    $condition[] = ['status', '=', 0];
                } else {
                    $condition[] = ['status', '=', $status];
                }
            }

            if ($service_type != '') {
                $condition[] = ['service_type', '=', $service_type];
            }
            if ($site_id != "") {
                $condition[] = ['site_id', '=', $site_id];
            }

            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }

            $order = 'create_time desc';

            $res = $shop_service_model->getServicePageList($condition, $page, $page_size, $order);

            //处理审核状态
            $service_status_arr = $shop_service_model->getServiceStatus();
            foreach ($res['data']['list'] as $key => $val) {
                $res['data']['list'][$key]['status_title'] = $service_status_arr[$val['status']];
            }
            return $res;
        }
        //申请状态
        $service_status_arr = $shop_service_model->getServiceStatus();
        $this->assign('service_status_arr', $service_status_arr);

        $service_name_arr = $shop_service_model->getServiceNameList();

        $this->assign('service_name_arr', $service_name_arr);

        return $this->fetch('services/lists');
    }

    /**
     * 申请详情
     */
    public function detail()
    {
        $apply_id = input('apply_id', 0);

        $shop_service_model = new SupplyServiceModel();
        $detail             = $shop_service_model->getServiceInfo([['apply_id', '=', $apply_id]]);
        $service_status_arr = $shop_service_model->getServiceStatus();

        $detail['data']['status_name'] = $service_status_arr[$detail['data']['status']];
        $this->assign('info', $detail['data']);

        return $this->fetch('services/detail');
    }

    /**
     * 申请编辑
     */
    public function edit()
    {
        $apply_id = input('apply_id', 0);

        $shop_service_model = new SupplyServiceModel();
        $info               = $shop_service_model->getServiceInfo([['apply_id', '=', $apply_id]]);
        $this->assign('info', $info['data']);

        $service_name_arr = $shop_service_model->getServiceNameList();

        $this->assign('service_name_arr', $service_name_arr);
        return $this->fetch('services/edit');
    }


    /**
     * 申请通过
     */
    public function pass()
    {
        $apply_id = input('apply_id', 0);
        $this->addLog("服务申请通过id:" . $apply_id);
        $shop_service_model = new SupplyServiceModel();
        return $shop_service_model->servicePass($apply_id);
    }

    /**
     * 申请失败
     */
    public function reject()
    {
        $apply_id = input('apply_id', 0);
        $reason   = input('reason', '');
        $this->addLog("商家申请拒绝id:" . $apply_id);
        $shop_service_model = new SupplyServiceModel();
        return $shop_service_model->serviceReject($apply_id, $reason);
    }
}