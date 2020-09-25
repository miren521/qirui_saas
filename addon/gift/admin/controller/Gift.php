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


namespace addon\gift\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\gift\model\Gift as GiftModel;

/**
 * 礼品管理
 */
class Gift extends BaseAdmin
{

    /**
     * 礼品列表
     * @return mixed
     */
    public function lists()
    {
        if(request()->isAjax())
        {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
			$gift_state = input('gift_state', '');
            $condition = [];
            $condition[] = ['is_delete', '=', 0];
            if($search_text) {
                $condition[] = ['gift_name|gift_keywords', 'like', '%'. $search_text .'%'];
            }
			if($gift_state !== ""){
				$condition[] = [ 'gift_state', '=', $gift_state];
			}
            $order = 'create_time desc';
            $field = '*';

            $gift_model = new GiftModel();
            //礼品名称 礼品图片 礼品库存  礼品价格
            return $gift_model->getGiftPageList($condition, $page, $page_size, $order, $field);
        }else {
            $this->forthMenu();
            return $this->fetch("gift/lists");
        }
    }

    /**
     * 礼品添加
     * @return mixed
     */
    public function add()
    {
        $gift_model = new GiftModel();
        if(request()->isAjax()){
            $data = [
                'gift_name' => input('gift_name', ''),
                'gift_keywords' => input('gift_keywords', ''),
                'gift_desc' => input('gift_desc', ''),
                'gift_body' => input('gift_body', ''),
                'gift_price' => input('gift_price', 0),
                'gift_image' => input('gift_image', ''),
                'gift_stock' => input('gift_stock', ''),
                'gift_state' => input('gift_state', 1),
                'gift_no' => input('gift_no', ''),
            ];
            $this->addLog("添加礼品:".$data['gift_name']);
            return $gift_model->addGift($data);
        }else {
            $gift_no = $gift_model->getGiftNo();
            $this->assign('gift_no', $gift_no);
            return $this->fetch("gift/add");
        }
    }

    /**
     * 礼品编辑
     * @return mixed
     */
    public function edit()
    {
        $gift_id = input('gift_id', 0);
        $gift_model = new GiftModel();
        if(request()->isAjax()){
            $data = [
                'gift_name' => input('gift_name', ''),
                'gift_keywords' => input('gift_keywords', ''),
                'gift_desc' => input('gift_desc', ''),
                'gift_body' => input('gift_body', ''),
                'gift_price' => input('gift_price', 0),
                'gift_image' => input('gift_image', ''),
                'gift_stock' => input('gift_stock', ''),
                'gift_state' => input('gift_state', 0),
                'gift_id' => $gift_id,
            ];
            return $gift_model->editGift($data);
        }else {
            $gift_info = $gift_model->getGiftInfo(['gift_id' => $gift_id]);
            $this->assign('info', $gift_info['data']);
            return $this->fetch("gift/edit");
        }
    }

    /**
     * 礼品删除
     */
    public function delete()
    {
        if(request()->isAjax()){
            $gift_id = input('gift_id', 0);
            $this->addLog("删除礼品id:".$gift_id);
            $gift_model = new GiftModel();
            return $gift_model->deleteGift([['gift_id', '=', $gift_id]]);
        }
    }
}