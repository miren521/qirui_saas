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


namespace addon\manjian\shop\controller;

use app\shop\controller\BaseShop;
use addon\manjian\model\Manjian as ManjianModel;
use think\facade\Cache;

/**
 * 满减控制器
 */
class Manjian extends BaseShop
{

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();

        $this->replace = [
            'MANJIAN_CSS' => __ROOT__ . '/addon/manjian/shop/view/public/css',
            'MANJIAN_JS' => __ROOT__ . '/addon/manjian/shop/view/public/js',
            'MANJIAN_IMG' => __ROOT__ . '/addon/manjian/shop/view/public/img',
        ];

    }
    /**
     * 满减列表
     */
    public function lists()
    {
        if(request()->isAjax()){
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $manjian_name = input('manjian_name', '');
            $status = input('status','');
            $condition = [];
            $condition[] = ['site_id', '=', $this->site_id];
            $condition[] = ['manjian_name', 'like', '%'. $manjian_name .'%'];
            if($status != null){
                $condition[] = ['status', '=', $status];
            }
            $order = 'create_time desc';
            $field = 'manjian_id,manjian_name,start_time,end_time,create_time,status';

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

            return $this->fetch("manjian/lists");
        }
    }

    /**
     * 满减添加
     */
    public function add()
    {
        if(request()->isAjax()){
            $data = [
                'site_id' => $this->site_id,
                'site_name' => $this->shop_info['site_name'],
                'manjian_name' => input('manjian_name', ''),
                'manjian_type' => input('manjian_type', ''),
                'goods_ids' => input('goods_ids', ''),
                'start_time' => strtotime(input('start_time', '')),
                'end_time' => strtotime(input('end_time', '')),
                'rule_json' => input('rule_json', ''),
                'remark' => input('remark', ''),
                'create_time' => time(),
                'type' => input('type', 0),
            ];
            $manjian_model = new ManjianModel();
            return $manjian_model->addManjian($data);
        }else{
            $this->assign('is_install_present', addon_is_exit('present', $this->site_id));
            return $this->fetch("manjian/add", [], $this->replace);
        }
    }

    /**
     * 满减编辑
     */
    public function edit()
    {
        if(request()->isAjax()){
            $data = [
                'site_id' => $this->site_id,
                'site_name' => $this->shop_info['site_name'],
                'manjian_name' => input('manjian_name', ''),
                'manjian_type' => input('manjian_type', ''),
                'goods_ids' => input('goods_ids', ''),
                'start_time' => strtotime(input('start_time', '')),
                'end_time' => strtotime(input('end_time', '')),
                'rule_json' => input('rule_json', ''),
                'remark' => input('remark', ''),
                'manjian_id' => input('manjian_id', 0),
                'type' => input('type', 0),
            ];
            $manjian_model = new ManjianModel();
            return $manjian_model->editManjian($data);
        }else{
            $manjian_id = input('manjian_id', 0);
            $this->assign('manjian_id', $manjian_id);
            $manjian_model = new ManjianModel();
            $manjian_info = $manjian_model->getManjianDetail($manjian_id,$this->site_id);
            $this->assign('manjian_info', $manjian_info['data']);
            $this->assign('is_install_present', addon_is_exit('present', $this->site_id));
            return $this->fetch("manjian/edit", [], $this->replace);
        }
    }

    /**
     * 满减详情
     */
    public function detail()
    {
        $manjian_id = input('manjian_id', 0);

        $manjian_model = new ManjianModel();
        $manjian_info = $manjian_model->getManjianDetail($manjian_id, $this->site_id);
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
            $manjian_model = new ManjianModel();
            return $manjian_model->closeManjian($manjian_id, $this->site_id);
        }
    }

    /**
     * 满减删除
     */
    public function delete()
    {
        if(request()->isAjax()){
            $manjian_id = input('manjian_id', 0);
            $manjian_model = new ManjianModel();
            return $manjian_model->deleteManjian($manjian_id, $this->site_id);
        }
    }

    /**
     * 活动冲突商品
     */
    public function conflict(){
        $key = input('key', '');
        $conflict_data = Cache::get($key);
        $this->assign('conflict_data',$conflict_data);
        return $this->fetch('manjian/conflict');
    }
}