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


namespace app\shop\controller;

use app\model\web\Notice as NoticeModel;

/**
 * 网站公告
 */
class Notice extends BaseShop
{

    /**
     * 公告列表
     */
    public function lists()
    {
        if(request()->isAjax()){
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition[] = ['receiving_type', 'like', '%shop%'];

            $notice_model = new NoticeModel();
            $list = $notice_model->getNoticePageList($condition, $page, $page_size,'is_top desc,create_time desc');
            return $list;
        }else{

            $this->assign("menu_info", [ 'title' => "网站公告" ]);
            return $this->fetch('notice/lists');
        }
    }


    /**
     * 公告详情
     */
    public function detail()
    {
        $id = input('id', 1);
        $notice_model = new NoticeModel();
        $info = $notice_model->getNoticeInfo([['id','=',$id]]);

        $this->assign("info", $info['data']);
        $this->assign("menu_info", [ 'title' => "网站公告" ]);
        return $this->fetch('notice/detail');
    }

}