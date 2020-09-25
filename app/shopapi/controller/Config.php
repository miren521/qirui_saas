<?php
/**
 * Config.php
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

namespace app\shopapi\controller;

use app\model\web\Config as ConfigModel;

class Config extends BaseApi
{
	/**
	 * 详情信息
	 */
	public function defaultimg()
	{
		$upload_config_model = new ConfigModel();
		$res = $upload_config_model->getDefaultImg();
		if (!empty($res['data']['value'])) {
			return $this->response($this->success($res['data']['value']));
		} else {
			return $this->response($this->error());
		}
	}
	
	/**
	 * 版权信息
	 */
	public function copyright()
	{
		$config_model = new ConfigModel();
		$res = $config_model->getCopyright();
		return $this->response($this->success($res['data']['value']));
	}
	
}