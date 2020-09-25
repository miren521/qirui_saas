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


namespace addon\freeshipping\shop\controller;

use app\shop\controller\BaseShop;
use addon\freeshipping\model\Freeshipping as FreeshippingModel;

class Freeshipping extends BaseShop
{

    protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'FREESHIPPING_CSS' => __ROOT__ . '/addon/freeshipping/shop/view/public/css',
            'FREESHIPPING_JS' => __ROOT__ . '/addon/freeshipping/shop/view/public/js',
            'FREESHIPPING_IMG' => __ROOT__ . '/addon/freeshipping/shop/view/public/img',
        ];
    }
	/*
	 *  团购活动列表
	 */
	public function lists()
	{
		$model = new FreeshippingModel();
		
		$condition[] = [ 'site_id', '=', $this->site_id ];
		//获取续签信息
		if (request()->isAjax()) {
			
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getFreeshippingPageList($condition, $page, $page_size, 'price asc');
			return $list;
		} else {
			return $this->fetch("freeshipping/lists", [], $this->replace);
		}
		
	}
	
	/**
	 * 添加活动
	 */
	public function add()
	{
		if (request()->isAjax()) {
			
			$price = input("price", '');
			$json = input("json", "");
			$surplus_area_ids = input('surplus_area_ids', '');
			
			$json_data = json_decode($json, true);
			$data = $json_data['1'];
			$data['price'] = $price;
			$data['site_id'] = $this->site_id;
			$data['surplus_area_ids'] = $surplus_area_ids;
			$model = new FreeshippingModel();
			$result = $model->addFreeshipping($data);
			return $result;
			
		} else {
			
			// 地区等级设置 将来从配置中查询数据
			$area_level = 2;
			$this->assign('area_level', $area_level);//地址级别
			return $this->fetch("freeshipping/add", [], $this->replace);
		}
	}
	
	/**
	 * 编辑活动
	 */
	public function edit()
	{
		$model = new FreeshippingModel();
		$freeshipping_id = input('freeshipping_id', 0);
		if (request()->isAjax()) {
			
			$price = input("price", '');
			$json = input("json", "");
			$surplus_area_ids = input('surplus_area_ids', '');
			
			$json_data = json_decode($json, true);
			$data = $json_data['1'];
			$data['price'] = $price;
			$data['site_id'] = $this->site_id;
			$data['surplus_area_ids'] = $surplus_area_ids;
			$data['freeshipping_id'] = $freeshipping_id;
			
			$result = $model->editFreeshipping($data);
			return $result;
			
		} else {
			$this->assign('freeshipping_id', $freeshipping_id);
			// 地区等级设置 将来从配置中查询数据
			$area_level = 2;
			$this->assign('area_level', $area_level);//地址级别
			
			$info = $model->getFreeshippingInfo([ [ 'freeshipping_id', '=', $freeshipping_id ], [ 'site_id', '=', $this->site_id ] ]);
			$this->assign('info', $info['data']);
			return $this->fetch("freeshipping/edit", [], $this->replace);
		}
	}
	
	/*
	 *  删除
	 */
	public function delete()
	{
		$freeshipping_id = input('freeshipping_id', '');
		$site_id = $this->site_id;
		
		$model = new FreeshippingModel();
		return $model->deleteFreeshipping($freeshipping_id, $site_id);
	}
	
}