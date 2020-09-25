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


namespace addon\present\admin\controller;

use app\admin\controller\BaseAdmin;

use addon\present\model\Present as PresentModel;

/**
 * 赠品控制器
 */
class Present extends BaseAdmin
{
    /*
     *  赠品活动列表
     */
    public function lists()
    {

        $model = new PresentModel();


        //获取续签信息
        if (request()->isAjax()) {

            $condition = [];
            $status = input('status', '');//赠品状态
            if ($status) {
                $condition[] = ['status', '=', $status];
            }
            $goods_name = input('goods_name', '');
            $site_name = input('site_name', '');
            if(!empty($goods_name)){
                $condition[] = ['sku_name', 'like', '%' . $goods_name . '%'];
            }
            if(!empty($site_name)){
                $condition[] = ['site_name', 'like', '%' . $site_name . '%'];
            }

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $list = $model->getPresentPageList($condition, $page, $page_size, 'present_id desc');
            return $list;
        } else {
            return $this->fetch("present/lists");
        }

    }

    /*
     *  赠品详情
     */
    public function detail()
    {
        $present_model = new PresentModel();

        $present_id = input('present_id', '');
        //获取赠品信息
        $present_info = $present_model->getPresentInfo([['present_id', '=', $present_id]])['data'] ?? [];
        $this->assign('present_info', $present_info);
        return $this->fetch("present/detail");
    }

    /**
     * 删除赠品
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $present_id = input('present_id', '');
            $site_id = input('site_id', '');
            $this->addLog("删除赠品id:" . $present_id);
            $present_model = new PresentModel();
            $condition = array(
                ['present_id', '=', $present_id]
            );
            return $present_model->deletePresent($condition);
        }
    }

    /**
     * 结束赠品
     */
    public function close()
    {
        if (request()->isAjax()) {
            $present_id = input('present_id', 0);
            $site_id = input('site_id', '');
            $this->addLog("结束赠品id:" . $present_id);
            $present_model = new PresentModel();
            $condition = array(
                ['present_id', '=', $present_id]
            );
            return $present_model->finishPresent($condition);
        }
    }

}