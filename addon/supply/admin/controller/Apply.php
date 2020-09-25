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
use addon\supply\model\SupplyCategory as SupplierCategoryModel;
use addon\supply\model\Config as ConfigModel;
use addon\supply\model\SupplyApply as SupplierApplyModel;

/**
 * 供货商申请控制器
 */
class Apply extends BaseAdmin
{
    /******************************* 供货商申请列表及相关操作 ***************************/

    /**
     * 供货商申请
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page             = input('page', 1);
            $page_size        = input('page_size', PAGE_LIST_ROWS);
            $search_text      = input('search_text', '');
            $search_text_user = input('search_text_user', '');
            $category_id      = input('category_id', 0);
            $apply_state      = input('apply_state', 0);
            $start_time       = input("start_time", '');
            $end_time         = input("end_time", '');

            $condition   = [];
            $condition[] = ['supplier_name', 'like', '%' . $search_text . '%'];
            $condition[] = ['username', 'like', '%' . $search_text_user . '%'];
            if ($category_id != 0) {
                $condition[] = ['category_id', '=', $category_id];
            }
            // 申请状态
            if ($apply_state != 0) {
                if ($apply_state == 4) {
                    $condition[] = ['apply_state', '=', '2'];
                    $condition[] = ['paying_money_certificate', '=', ''];
                } else {
                    $condition[] = ['apply_state', '=', $apply_state];
                }
            }
            // 申请时间
            if (!empty($start_time) && empty($end_time)) {
                $condition[] = ['create_time', '>=', date_to_time($start_time)];
            } elseif (empty($start_time) && !empty($end_time)) {
                $condition[] = ["create_time", "<=", date_to_time($end_time)];
            } elseif (!empty($start_time) && !empty($end_time)) {
                $condition[] = ['create_time', 'between', [date_to_time($start_time), date_to_time($end_time)]];
            }

            $field = 'apply_id,member_id,member_name,cert_id,supplier_name,apply_state,apply_message,apply_year,'
                . 'category_name,paying_money_certificate,group_name,audit_time,finish_time,create_time,'
                . 'username,paying_apply,paying_deposit,paying_amount';

            $apply_model = new SupplierApplyModel();
            $res         = $apply_model->getApplyPageList($condition, $page, $page_size, 'create_time desc', $field);

            //处理审核状态
            $apply_state_arr = $apply_model->getApplyState();
            foreach ($res['data']['list'] as $key => $val) {
                if ($apply_state == 2) {
                    if (empty(trim($val['paying_money_certificate']))) {
                        $res['data']['count'] = $res['data']['count'] - 1;
                        unset($res['data']['list'][$key]);
                        continue;
                    }
                }
                $res['data']['list'][$key]['apply_state_name'] = $apply_state_arr[$val['apply_state']];
            }
            if ($apply_state == 2) {
                if (empty($res['data']['count'])) {
                    $res['data']['page_count'] = 0;
                } else {
                    $res['data']['page_count'] = ceil($res['data']['page_count'] / $page_size);
                }
            }

            return $res;
        } else {
            //供货商主营行业
            $category_model = new SupplierCategoryModel();
            $category_list  = $category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
            $this->assign('shop_category_list', $category_list['data']);

            //申请状态
            $apply_model     = new SupplierApplyModel();
            $apply_state_arr = $apply_model->getApplyState();
            $this->assign('apply_state_arr', $apply_state_arr);

            return $this->fetch('apply/lists');
        }
    }

    /**
     * 申请详情
     */
    public function detail()
    {
        $apply_id = input('apply_id', 0);

        $apply_model = new SupplierApplyModel();
        $detail      = $apply_model->getApplyDetail([['nsa.apply_id', '=', $apply_id]]);
        $this->assign('apply_detail', $detail['data']);

        return $this->fetch('apply/detail');
    }

    /**
     * 编辑供货商申请
     */
    public function edit()
    {
        $apply_model = new SupplierApplyModel();
        if (request()->isAjax()) {
            if (empty(input('apply_state'))) {
                return 0;
            }
            $apply_state = input('apply_state');
            if (!in_array($apply_state, [3, -2])) {
                return 0;
            }
            $data     = [
                'apply_state'   => $apply_state,
                'apply_message' => input('apply_message', ''),//审核意见
            ];
            $apply_id = input('apply_id', 0);
            if ($apply_state == -2) {
                $this->addLog("财务审核拒绝入驻申请ID:" . $apply_id);
            } else {
                $this->addLog("入驻申请通过ID:" . $apply_id);
            }

            if ($apply_state == 3) {
                $this->addLog("开店通过，申请id:" . $apply_id);
                $result = $apply_model->openSupply($apply_id, $data['apply_message']);
            } else {
                $result = $apply_model->editApply($data, [['apply_id', '=', $apply_id]]);
            }
            return $result;
        } else {
            $apply_id = input('apply_id', 0);

            //申请信息
            $apply_info = $apply_model->getApplyInfo([['apply_id', '=', $apply_id]]);

            //供货商主营行业
            $category_model              = new SupplierCategoryModel();
            $category                    = $category_model->getCategoryInfo(
                ['category_id' => $apply_info['data']['category_id']],
                'category_name'
            );
            $apply_info['category_name'] = $category['data']['category_name'];
            $this->assign('apply_info', $apply_info['data']);
            return $this->fetch('apply/edit');
        }
    }

    /**
     * 申请通过
     */
    public function pass()
    {
        $apply_id    = input('apply_id', 0);
        $apply_model = new SupplierApplyModel();
        $this->addLog("供货商申请通过id:" . $apply_id);
        return $apply_model->applyPass($apply_id);
    }

    /**
     * 申请失败
     */
    public function refuse()
    {
        $apply_id = input('apply_id', 0);
        $reason   = input('reason', '');
        $this->addLog("供货商申请拒绝id:" . $apply_id);
        $apply_model = new SupplierApplyModel();
        return $apply_model->applyReject($apply_id, $reason);
    }

    /**
     * 入驻通过
     */
    public function openSupply()
    {
        $apply_id = input('apply_id', 0);
        $this->addLog("入驻通过，申请id:" . $apply_id);
        $apply_model = new SupplierApplyModel();
        return $apply_model->openSupply($apply_id);
    }

    /**
     * 入驻协议
     * @return mixed
     */
    public function agreement()
    {
        $config_model = new ConfigModel();
        if (request()->isAjax()) {
            $title   = input('title', '');//标题
            $content = input('content', '');//内容
            return $config_model->setApplyAgreement($title, $content);
        }

        $data = $config_model->getApplyAgreement();
        $this->assign('data', $data);

        return $this->fetch('apply/agreement');
    }
}
