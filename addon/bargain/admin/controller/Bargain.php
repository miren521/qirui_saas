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


namespace addon\bargain\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\bargain\model\Bargain as BargainModel;


class Bargain extends BaseAdmin
{
	
	/*
	 *  砍价活动列表
	 */
	public function lists()
	{
		$model = new BargainModel();

		$condition = [ ];
		//获取续签信息
		if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

			$status = input('status', '');//砍价状态

			if ($status !== '') {
				$condition[] = [ 'status', '=', $status ];
			}
			//活动名称
			$bargain_name = input('bargain_name','');
			if($bargain_name){
                $condition[] = [ 'bargain_name', 'like', '%' . $bargain_name . '%' ];
            }

            $start_time = input('start_time','');
            $end_time = input('end_time','');
            if($start_time && !$end_time){
                $condition[] = ['start_time','>=',date_to_time($start_time)];
            }elseif(!$start_time && $end_time){
                $condition[] = ['end_time','<=',date_to_time($end_time)];
            }elseif($start_time && $end_time){
                $condition[] = ['start_time','>=',date_to_time($start_time)];
                $condition[] = ['end_time','<=',date_to_time($end_time)];
            }

			$list = $model->getBargainPageList($condition, $page, $page_size, 'bargain_id desc');
			return $list;
		} else {
			
			$bargain_status = $model->getBargainStatus();
			$this->assign('bargain_status',$bargain_status['data']);

            return $this->fetch("bargain/lists");
		}
	}
	


	
	/*
	 *  砍价详情
	 */
	public function detail()
	{
		$bargain_model = new BargainModel();
		
		$bargain_id = input('bargain_id', '');
		//获取砍价信息
        $condition = [
            [ 'bargain_id', '=', $bargain_id ],
        ];
		$bargain_info = $bargain_model->getBargainInfo($condition);
		$this->assign('bargain_info', $bargain_info['data']);
		return $this->fetch("bargain/detail");
	}
	
	/*
	 *  删除砍价活动
	 */
	public function delete()
	{
		$bargain_id = input('bargain_id', '');
        $site_id = input('site_id', 0);
		
		$bargain_model = new BargainModel();
		return $bargain_model->deleteBargain($bargain_id, $site_id);
	}
	
	/*
	 *  结束砍价活动
	 */
	public function finish()
	{
		$bargain_id = input('bargain_id', '');
        $site_id = input('site_id', 0);
		$bargain_model = new BargainModel();
		return $bargain_model->finishBargain($bargain_id, $site_id);
	}
	
}