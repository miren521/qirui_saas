<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

namespace app\api\controller;

use app\model\web\DiyView as DiyViewModel;
use app\model\web\WebSite;
use app\model\system\Config as ConfigModel;

/**
 * 自定义模板
 * @package app\api\controller
 */
class Diyview extends BaseApi
{
	
	/**
	 * 基础信息
	 */
	public function info()
	{
		$id = isset($this->params['id']) ? $this->params['id'] : 0;
		$name = isset($this->params['name']) ? $this->params['name'] : '';
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		$web_city = isset($this->params['web_city']) ? $this->params['web_city'] : "";
		if (empty($id) && empty($name)) {
			return $this->response($this->error('', 'REQUEST_DIY_ID_NAME'));
		}
		$diy_view = new DiyViewModel();
		$condition = [];
		if (!empty($id)) {
			$condition[] = [ 'sdv.id', '=', $id ];
		}
		if (!empty($name)) {
			$condition[] = [ 'sdv.name', '=', $name ];
		}
		if (!empty($site_id)) {
			$condition[] = [ 'sdv.site_id', '=', $site_id ];
		}
		// 查询是否存在城市分站
		if ($name == "DIYVIEW_INDEX" && addon_is_exit('city') && (!empty($site_id) || !empty($web_city))) {
			$website_model = new WebSite();
			$website_condition = [
				[ 'status', '=', 1 ]
			];
			if (!empty($site_id)) {
				$website_condition[] = [ 'site_id', '=', $site_id ];
			} else if (!empty($web_city)) {
				$website_condition[] = [ 'site_area_id', '=', $web_city ];
			}
			
			$is_empty = true;
			$website_info = $website_model->getWebSite($website_condition, 'site_id,site_area_id,site_area_name');
			$website_info = $website_info['data'];
			if (!empty($website_info)) {
				$condition[] = [ 'sdv.site_id', '=', $website_info['site_id'] ];
				$info = $diy_view->getSiteDiyViewDetail($condition);
				if (!empty($info['data'])) {
					$is_empty = false;
					$website_data = [
						'site_area_id' => $website_info['site_area_id'],
						'site_area_name' => $website_info['site_area_name']
					];
					$info['data'] = array_merge($info['data'], $website_data);
				}
			}
			if ($is_empty) {
				// 如果分站没有设置自定义首页数据，则显示全国的
				foreach ($condition as $k => $v) {
					if ($v[0] == 'sdv.site_id') {
						unset($condition[ $k ]);
						break;
					}
				}
				$condition = array_values($condition);
				$info = $diy_view->getSiteDiyViewDetail($condition);
			}
		} else {
			$info = $diy_view->getSiteDiyViewDetail($condition);
		}
		return $this->response($info);
	}
	
	/**
	 * 平台端底部导航
	 * @return string
	 */
	public function bottomNav()
	{
		$diy_view = new DiyViewModel();
		$info = $diy_view->getBottomNavConfig();
		return $this->response($info);
	}
	
	/**
	 * 店铺端底部导航
	 * @return string
	 */
	public function shopBottomNav()
	{
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		if (empty($site_id)) {
			return $this->response($this->error('', 'REQUEST_SITE_ID'));
		}
		$diy_view = new DiyViewModel();
		$info = $diy_view->getShopBottomNavConfig($site_id);
		return $this->response($info);
	}

    /**
     * 风格
     */
    public function style()
    {
        $config_model = new ConfigModel();
        $res = $config_model->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'SHOP_STYLE_CONFIG' ] ]);
        $style_theme = empty($res['data']['value']) ? ['style_theme'=>'default'] : $res['data']['value'];
        return $this->response($this->success($style_theme));
    }
	
}