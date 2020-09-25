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
use addon\supply\model\SupplyCategory as SupplyCategoryModel;
use addon\supply\model\SupplyReopen as SupplyReopenModel;

/**
 * 供货商续签
 */
class Reopen extends BaseAdmin
{
    /******************************* 商家续签列表及相关操作 ***************************/

    /**
     * 商家申请续签列表
     */
    public function lists()
    {
        $model     = new SupplyReopenModel();
        $condition = [];
        if (request()->isAjax()) {
            $page        = input('page', 1);
            $page_size   = input('page_size', PAGE_LIST_ROWS);
            $site_name   = input('site_name', '');//供货商名称
            $category_id = input('category_id', '');//供货商类别id

            $site_id = input('site_id', '');
            if ($site_id) {
                $condition[] = ['r.site_id', '=', $site_id];
            }

            $apply_state = input('apply_state', '');
            if ($apply_state) {
                $condition[] = ['r.apply_state', '=', $apply_state];
            }

            if ($site_name) {
                $condition[] = ['s.site_name', 'like', '%' . $site_name . '%'];
            }
            if ($category_id) {
                $condition[] = ['s.category_id', '=', $category_id];
            }
            return $model->getApplyReopenPageList($condition, $page, $page_size);
        }
        // 主营行业
        $category_model = new SupplyCategoryModel();
        $category_list  = $category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
        $this->assign('shop_category_list', $category_list['data']);

        return $this->fetch('reopen/lists');
    }

    //查看续签详情
    public function detail()
    {
        $model = new SupplyReopenModel();
        $id    = input('id', '');

        $info = $model->getReopenInfo([['id', '=', $id]]);
        $this->assign('info', $info);
        return $this->fetch('reopen/detail');
    }

    /**
     * 申请续签通过
     * @return array|void
     */
    public function pass()
    {
        if (request()->isAjax()) {
            $model = new SupplyReopenModel();

            $id      = input('id', '');
            $site_id = input('site_id', '');
            $this->addLog("供货商续签审核通过站点id:" . $site_id);
            $result = $model->pass($id, $site_id);
            return $result;
        }
    }

    /**
     * 申请续签失败
     * @return array|void
     */
    public function fail()
    {
        if (request()->isAjax()) {
            $model = new SupplyReopenModel();

            $id     = input('id', '');
            $reason = input('reason', '');//拒绝原因
            $this->addLog("供货商续签审核拒绝id:" . $id);
            $result = $model->refuse($id, $reason);
            return $result;
        }
    }
}
