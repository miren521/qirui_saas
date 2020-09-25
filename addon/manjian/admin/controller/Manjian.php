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


namespace addon\manjian\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\manjian\model\Manjian as ManjianModel;

/**
 * 满减控制器
 */
class Manjian extends BaseAdmin
{

    /**
     * 满减列表
     */
    public function lists()
    {
        if(request()->isAjax()){
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $manjian_name = input('manjian_name', '');
			$site_name = input('site_name', '');
            $site_id = input('site_id', 0);
			$status = input('status','');

            $condition = [];
            $condition[] = ['manjian_name', 'like', '%'. $manjian_name .'%'];
			$condition[] = ['site_name', 'like', '%'. $site_name .'%'];
            if($site_id != 0){
                $condition[] = ['site_id', '=', $site_id];
            }
			if($status != null){
				$condition[] = ['status', '=', $status];
			}
            $order = 'create_time desc';
            $field = 'manjian_id,manjian_name,site_name,start_time,end_time,create_time,status,site_id,site_name,rule_json';

            $manjian_model = new ManjianModel();
            $res = $manjian_model->getManjianPageList($condition, $page, $page_size, $order, $field);

            //获取状态名称
            $manjian_status_arr = $manjian_model->getManjianStatus();
            foreach($res['data']['list'] as $key=>$val){
                $res['data']['list'][$key]['status_name'] = $manjian_status_arr[$val['status']];
            }
            return $res;

        }else{
            //满减状态
            $manjian_model = new ManjianModel();
            $manjian_status_arr = $manjian_model->getManjianStatus();
            $this->assign('manjian_status_arr', $manjian_status_arr);
            $this->assign('is_install_present', addon_is_exit('present', $this->site_id));
            return $this->fetch("manjian/lists");
        }
    }

    /**
     * 满减详情
     */
    public function detail()
    {
        $manjian_id = input('manjian_id', 0);
        $site_id = input('site_id', 0);

        $manjian_model = new ManjianModel();
        $manjian_info = $manjian_model->getManjianDetail($manjian_id, $site_id);

        $this->assign('manjian_info',$manjian_info['data']);
        $this->assign('is_install_present', addon_is_exit('present', $this->site_id));
        return $this->fetch('manjian/detail');
    }

    /**
     * 满减关闭
     */
    public function close()
    {
        if(request()->isAjax()){
            $manjian_id = input('manjian_id', 0);
            $site_id = input('site_id', 0);
            $this->addLog("强制关闭满减id:".$manjian_id);
            $manjian_model = new ManjianModel();
            return $manjian_model->closeManjian($manjian_id, $site_id);
        }
    }

    /**
     * 满减删除
     */
    public function delete()
    {
        if(request()->isAjax()){
            $manjian_id = input('manjian_id', 0);
            $site_id = input('site_id', 0);
            $manjian_model = new ManjianModel();
            return $manjian_model->deleteManjian($manjian_id, $site_id);
        }
    }
}