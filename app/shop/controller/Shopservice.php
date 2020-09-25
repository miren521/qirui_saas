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

use app\model\shop\Shop;
use app\model\shop\ShopService as ShopServiceModel;

/**
 * 店铺服务
 * Class Shop
 * @package app\shop\controller
 */
class Shopservice extends BaseShop
{
    /*
     * 服务首页
     */
	public function lists()
    {
    
       	$shop_service_model = new ShopServiceModel();

       	$service_name_arr = $shop_service_model->serviceApplyList($this->site_id);
  
       	$this->assign('service_name_arr', $service_name_arr);

        return $this->fetch("shopservice/lists");
    }
    /*
     * 服务申请 
     */
    public function apply()
    {

        if(request()->isAjax()){

            $shop_service_model = new ShopServiceModel();
            //查询对应的服务类型列表
            $service_name_arr = $shop_service_model->getServiceNameList();

            $service_key = input("service_key", "");
            
            $key = array_search($service_key, array_column($service_name_arr, 'key'));
            
            $service_type_name = $service_name_arr[$key]['name'];
            $service_type_key = $service_name_arr[$key]['key'];
            //获取店铺信息
            $shop = new Shop();
            $shop_info = $shop->getShopInfo([ [ 'site_id', '=', $this->site_id ] ], 'site_name');
            $reopen_data = [
                'site_id' => $this->site_id,//店铺ID
                'site_name' => $shop_info['data']['site_name'],
                'service_type' => $service_type_key,
                'service_type_name' => $service_type_name,
                'status' => 0,
                'create_time' => time()
            ];
            $result = $shop_service_model->ServiceApply($reopen_data);
            return $result;
        }
    }
    /**
     * 服务退出
     */
    public function quit()
    {
        if(request()->isAjax()){
            $shop_service_model = new ShopServiceModel();
            $service_key = input("service_key", "");
            $data = [
                $service_key => 0
            ];
            $result = $shop_service_model->ServiceQuit($data,$this->site_id);
            return $result;
        }
    }
}
