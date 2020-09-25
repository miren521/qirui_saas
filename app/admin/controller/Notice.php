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


namespace app\admin\controller;

use app\model\web\Notice as NoticeModel;

/**
 * 网站公告
 */
class Notice extends BaseAdmin
{
	
	/**
	 * 公告管理
	 * @return \think\mixed
	 */
	public function index()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$limit = input('page_size', PAGE_LIST_ROWS);
			$condition = [];
			
			$receiving_type = input('receiving_type', '');
			if ($receiving_type) {
				$condition[] = [ 'receiving_type', 'like', '%' . $receiving_type . '%' ];
			}
			$notice = new NoticeModel();
			$list = $notice->getNoticePageList($condition, $page, $limit);
			return $list;
		}
		
		$is_addon_city = addon_is_exit('city');
		$this->assign('is_addon_city', $is_addon_city);
		return $this->fetch('notice/index');
	}
	
	/**
	 * 公告add
	 */
	public function addNotice()
	{
		
		if (request()->isAjax()) {
			$data = [
				'title' => input('title', ''),
				'content' => input('content', ''),
				'is_top' => input('is_top', 0),
				'create_time' => time(),
				'receiving_type' => input('receiving_type', ''),
				'receiving_name' => input('receiving_name', '')
			];
			
			if (!empty($data['receiving_type'])) {
				
				$data['receiving_name'] = in_array('shop', $data['receiving_type']) ? '店铺' : '';
				$data['receiving_name'] .= in_array('website', $data['receiving_type']) ? ' 城市分站' : '';
				$data['receiving_name'] .= in_array('mobile', $data['receiving_type']) ? ' 手机端' : '';
				$data['receiving_name'] .= in_array('web', $data['receiving_type']) ? ' 电脑端' : '';
				$data['receiving_type'] = implode(',', $data['receiving_type']);
			}
			
			$notice = new NoticeModel();
			$this->addLog("发布公告:" . $data['title']);
			$res = $notice->addNotice($data);
			return $res;
		} else {
			
			$is_addon_city = addon_is_exit('city');
			$this->assign('is_addon_city', $is_addon_city);
			return $this->fetch('notice/add_notice');
		}
	}
	
	/**
	 * 公告编辑
	 */
	public function editNotice()
	{
		
		$notice = new NoticeModel();
		if (request()->isAjax()) {
			$id = input('id', 0);
			$data = [
				'title' => input('title', ''),
				'content' => input('content', ''),
				'is_top' => input('is_top', 0),
				'receiving_type' => input('receiving_type', ''),
				'receiving_name' => input('receiving_name', '')
			];
			
			if (!empty($data['receiving_type'])) {
				
				$data['receiving_name'] = in_array('shop', $data['receiving_type']) ? '店铺' : '';
				$data['receiving_name'] .= in_array('website', $data['receiving_type']) ? ' 城市分站' : '';
				$data['receiving_name'] .= in_array('mobile', $data['receiving_type']) ? ' 手机端' : '';
				$data['receiving_name'] .= in_array('web', $data['receiving_type']) ? ' 电脑端' : '';
				$data['receiving_type'] = implode(',', $data['receiving_type']);
			}
			
			$res = $notice->editNotice($data, [ [ 'id', '=', $id ] ]);
			return $res;
		} else {
			$id = input('id', 0);
			$info = $notice->getNoticeInfo([ [ 'id', '=', $id ] ]);
			$this->assign('info', $info['data']);
			
			$is_addon_city = addon_is_exit('city');
			$this->assign('is_addon_city', $is_addon_city);
			echo $this->fetch('notice/edit_notice');
		}
	}
	
	/**
	 * 公告删除
	 * @return string[]|mixed[]
	 */
	public function deleteNotice()
	{
		if (request()->isAjax()) {
			$id = input('id', '');
			$notice = new NoticeModel();
			$res = $notice->deleteNotice([ [ 'id', 'in', $id ] ]);
			return $res;
		}
	}
	
	/**
	 * 公告置顶
	 */
	public function modifyNoticeTop()
	{
		$id = input('id', '');
		$notice = new NoticeModel();
		$res = $notice->editNotice([ 'is_top' => 1 ], [ [ 'id', '=', $id ] ]);
		return $res;
	}
	
}