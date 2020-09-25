<?php
/**
 * Shopjoin.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

namespace app\api\controller;

use app\model\shop\Config as ConfigModel;

/**
 * 入住指南
 * Class Shopjoin
 * @package app\api\controller
 */
class Shopjoin extends BaseApi
{
	
	/**
	 * 基础信息
	 */
	public function info()
	{
		//指南索引 1 2 3 4
		$guide_index = isset($this->params['guide_index']) ? $this->params['guide_index'] : 1;
		$config_model = new ConfigModel();
		$info = $config_model->getShopJoinGuideDocument($guide_index);
		return $this->response($info);
	}
	
	/**
	 * 列表信息
	 */
	public function lists()
	{
		$config_model = new ConfigModel();
		$list = $config_model->getShopJoinGuide();
		return $this->response($list);
	}
	
}