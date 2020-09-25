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


namespace addon\fenxiao\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\fenxiao\model\Config as ConfigModel;
use addon\fenxiao\model\FenxiaoLevel as FenxiaoLevelModel;

/**
 *  分销等级管理
 */
class Level extends BaseAdmin
{
	
	/**
	 * 分销等级列表
	 */
	public function lists()
	{
		$model = new FenxiaoLevelModel();
		$field = 'level_id,level_num,level_name,one_rate,two_rate,three_rate,status,create_time';
		if (request()->isAjax()) {
			
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getLevelPageList([], $page, $page_size, 'one_rate asc', $field);
			return $list;
			
		} else {
			//获取系统配置
			$config_model = new ConfigModel();
			$basics = $config_model->getFenxiaoBasicsConfig();
			$this->assign("basics_info", $basics['data']['value']);
			return $this->fetch('level/lists');
		}
		
	}
	
	/**
	 * 添加分销等级
	 */
	public function add()
	{
		$model = new FenxiaoLevelModel();
		
		if (request()->isAjax()) {
			
			$data = [
				
				'level_name' => input('level_name', ''),
				'one_rate' => input('one_rate', ''),
				'two_rate' => input('two_rate', ''),
				'three_rate' => input('three_rate', ''),
				'upgrade_type' => input('upgrade_type', ''),
				
				'fenxiao_order_num' => input('fenxiao_order_num', ''),
				'fenxiao_order_meney' => input('fenxiao_order_meney', ''),
				'one_fenxiao_order_num' => input('one_fenxiao_order_num', ''),
				'one_fenxiao_order_money' => input('one_fenxiao_order_money', ''),
				'order_num' => input('order_num', ''),
				'order_money' => input('order_money', ''),
				'child_num' => input('child_num', ''),
				'child_fenxiao_num' => input('child_fenxiao_num', ''),
				'one_child_num' => input('one_child_num', ''),
				'one_child_fenxiao_num' => input('one_child_fenxiao_num', ''),
			];
			$res = $model->addLevel($data);
			return $res;
		} else {
			//获取系统配置
			$config_model = new ConfigModel();
			$basics = $config_model->getFenxiaoBasicsConfig();
			$this->assign("basics_info", $basics['data']['value']);
			
			return $this->fetch('level/add');
		}
		
	}
	
	/**
	 * 编辑分销等级
	 */
	public function edit()
	{
		$model = new FenxiaoLevelModel();
		
		if (request()->isAjax()) {
			
			$data = [
				'level_name' => input('level_name', ''),
				'one_rate' => input('one_rate', ''),
				'two_rate' => input('two_rate', ''),
				'three_rate' => input('three_rate', ''),
				'upgrade_type' => input('upgrade_type', ''),
				
				'fenxiao_order_num' => input('fenxiao_order_num', ''),
				'fenxiao_order_meney' => input('fenxiao_order_meney', ''),
				'one_fenxiao_order_num' => input('one_fenxiao_order_num', ''),
				'one_fenxiao_order_money' => input('one_fenxiao_order_money', ''),
				'order_num' => input('order_num', ''),
				'order_money' => input('order_money', ''),
				'child_num' => input('child_num', ''),
				'child_fenxiao_num' => input('child_fenxiao_num', ''),
				'one_child_num' => input('one_child_num', ''),
				'one_child_fenxiao_num' => input('one_child_fenxiao_num', ''),
			];
			$level_id = input('level_id', '');
			
			$res = $model->editLevel($data, [ [ 'level_id', '=', $level_id ] ]);
			return $res;
		} else {
			$level_id = input('level_id', '');
			$info = $model->getLevelInfo([ [ 'level_id', '=', $level_id ] ]);
			$this->assign('info', $info['data']);
			//获取系统配置
			$config_model = new ConfigModel();
			$basics = $config_model->getFenxiaoBasicsConfig();
			$this->assign("basics_info", $basics['data']['value']);
		}
		
		return $this->fetch('level/edit');
	}
	
	
	/**
	 * 删除分销等级
	 */
	public function delete()
	{
		$model = new FenxiaoLevelModel();
		
		$level_id = input('level_id', '');
		$res = $model->deleteLevel($level_id);
		return $res;
	}
	
	public function test()
	{
		event('FenxiaoUpgrade', 3);
	}
}