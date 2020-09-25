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


namespace app\model\shop;


use app\model\BaseModel;

/**
 * 店铺服务申请
 */
class ShopService extends BaseModel
{
	
	//服务申请状态
	private $status = [
		1 => '审核通过',
		0 => '待审核',
		-1 => '审核失败',
	];
	//服务项目
	private $service_list = [
		[
			'name' => '7天退换',
			'desc' => '7天无理由退货',
			'key'  => 'shop_qtian',
			'icon'  => 'public/static/img/shop/replacement.png',
			'pc_icon' => 'icon7tiantuihuan'
		],
		[
			'name' => '正品保障',
			'desc' => '正品保障',
			'key'  => 'shop_zhping',
			'icon'  => 'public/static/img/shop/quality_goods.png',
			'pc_icon' => 'iconzhengpinbaozheng'
		],
		[
			'name' => '两小时发货',
			'desc' => '两小时发货',
			'key'  => 'shop_erxiaoshi',
			'icon'  => 'public/static/img/shop/delivery.png',
			'pc_icon' => 'iconliangxiaoshifahuo'
		],
		[
			'name' => '退货承诺',
			'desc' => '退货承诺',
			'key'  => 'shop_tuihuo',
			'icon'  => 'public/static/img/shop/return_goods.png',
			'pc_icon' => 'iconchengnuotuihuo1'
		],
		[
			'name' => '试用中心',
			'desc' => '试用中心',
			'key'  => 'shop_shiyong',
			'icon'  => 'public/static/img/shop/trial.png',
			'pc_icon' => 'iconshiyanzhongxin'
		],
		[
			'name' => '实体验证',
			'desc' => '实体验证',
			'key'  => 'shop_shiti',
			'icon'  => 'public/static/img/shop/entity.png',
			'pc_icon' => 'iconshitiyanzheng1'
		],
		[
			'name' => '消协保证',
			'desc' => '消协保证',
			'key'  => 'shop_xiaoxie',
			'icon'  => 'public/static/img/shop/ensure;.png',
			'pc_icon' => 'iconxiaoxiebaozheng1'
		]
	];
	
	/**
	 * 获取服务申请信息
	 * @param unknown $condition
	 * @param unknown $field
	 */
	public function getServiceInfo($condition, $field = '*')
	{
		$info = model('shop_service')->getInfo($condition, $field);
		return $this->success($info);
	}
	/**
	 * 获取服务申请列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getServiceList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('shop_service')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取服务申请分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getServicePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('shop_service')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	/**
	 * 商家服务列表
	 */
	public function serviceApplyList($site_id)
	{
		$service_list_arr = $this->service_list;

		$condition = [];
		//获取该服务商的服务状态
		$shop_service_info = model('shop')->getInfo([ [ 'site_id', '=', $site_id ] ], 'site_id,site_name,shop_qtian,shop_zhping,shop_erxiaoshi,shop_tuihuo,shop_shiyong,shop_shiti,shop_xiaoxie');
		//通过全局配置查询对应的服务信息
		foreach ($service_list_arr as $key => $value) {
		    $service_key = $value['key'];
		    $service_list_arr[$key]['status'] = $shop_service_info[$service_key];

		    $condition[$key][] = [ 'site_id', '=', $site_id ];
		    $condition[$key][] = [ 'service_type', '=', $service_key];
			// 获取最近一条记录审核的状态
		    $shop_service = model('shop_service')->getFirstData($condition[$key],'','apply_id desc');

		    $service_list_arr[$key]['apply_status'] = 2;
		    $service_list_arr[$key]['remark'] = '';
		   
	    	if($shop_service['service_type'] == $value['key']){
	    		// 查询到对应的信息,进行审核状态的获取
	    		$service_list_arr[$key]['apply_status']  =  $shop_service['status'];
	    		$service_list_arr[$key]['remark']  =  $shop_service['remark'];
	    	}
		}
		return $service_list_arr;
	}
	/**
	 * 修改服务申请
	 * @param array $data
	 */
	public function editService($data, $condition)
	{
		$res = model('shop_service')->update($data, $condition);
		return $this->success($res);
	}
	
	/**
	 * 审核通过
	 * @param unknown $apply_id
	 */
	public function servicePass($apply_id)
	{
		// 开启事务
		model('shop_service')->startTrans();
		try {
		
            //获取服务申请信息
            $service_info = model('shop_service')->getInfo([ [ 'apply_id', '=', $apply_id ] ]);
            //获取站点ID
            $site_id = $service_info['site_id'];

            $key = array_search($service_info['service_type'], array_column($this->service_list, 'key'));
           
            $service_type_key = $this->service_list[$key]['key'];
            
     		// 商城店铺信息修改
            model('shop')->update([ $service_type_key => 1 ], [ [ 'site_id', '=', $site_id ] ]);
            // 服务记录修改
            $res = model('shop_service')->update([ 'status' => 1, 'audit_time' => time() ], [ [ 'apply_id', '=', $apply_id ] ]);
			// 事务提交
			model('shop_service')->commit();
			return $this->success($res);
		} catch (\Exception $e) {
			// 事务回滚
			model('shop_service')->rollback();
			return $this->error('', $e->getMessage());
		}
	}
	
	/**
	 * 审核拒绝
	 * @param unknown $apply_id
	 * @param unknown $reason
	 */
	public function serviceReject($apply_id, $reason)
	{
		$res = model('shop_service')->update([ 'status' => -1, 'remark' => $reason ], [ [ 'apply_id', '=', $apply_id ] ]);
		return $this->success($res);
	}
	/**
	 * 获取服务申请状态
	 */
	public function getServiceStatus()
	{
		return $this->status;
	}
	/**
	 * 获取服务列表
	 */
	public function getServiceNameList()
	{
		return $this->service_list;
	}
	/**
	 * 商家服务申请
	 */
	public function ServiceApply($reopen_data)
	{
		$res = model('shop_service')->add($reopen_data);
		return $this->success($res);
	}
	/**
	 * 商家服务退出
	 */
	public function ServiceQuit($data,$site_id)
	{
 		// 商城店铺信息修改
        $res = model('shop')->update($data, [ [ 'site_id', '=', $site_id ] ]);
        return $this->success($res);
	}
}