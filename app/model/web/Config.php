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


namespace app\model\web;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
use app\model\system\Upgrade;

/**
 * 网站系统性设置
 */
class Config extends BaseModel
{
	//缓存类型
	private $cache_list = [
		[
			'name' => '数据缓存',
			'desc' => '数据缓存',
			'key' => 'content',
			'icon' => 'public/static/img/cache/data.png'
		],
		[
			'name' => '数据表缓存',
			'desc' => '数据表缓存',
			'key' => 'data_table_cache',
			'icon' => 'public/static/img/cache/data_table.png'
		],
		[
			'name' => '模板缓存',
			'desc' => '模板缓存',
			'key' => 'template_cache',
			'icon' => 'public/static/img/cache/template.png'
		],
	];
	
	/**
	 * 验证码设置
	 * array $data
	 */
	public function setCaptchaConfig($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '验证码设置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'CAPTCHA_CONFIG' ] ]);
		return $res;
	}
	
	/**
	 * 查询验证码设置
	 */
	public function getCaptchaConfig()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'CAPTCHA_CONFIG' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'shop_login' => 1,
				'admin_login' => 1
			];
		}
		return $res;
	}
	
	/**
	 * 默认图上传配置
	 * array $data
	 */
	public function setDefaultImg($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '默认图设置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'DEFAULT_IMAGE' ] ]);
		return $res;
	}
	
	public function setCopyright($data)
	{
		$config = new ConfigModel();
		$res = $config->setConfig($data, '版权设置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'COPYRIGHT' ] ]);
		return $res;
	}
	
	/**
	 * 默认图查询上传配置
	 */
	public function getDefaultImg()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'DEFAULT_IMAGE' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				"default_goods_img" => "upload/default/default_img/goods.png",
				"default_headimg" => "upload/default/default_img/head.png",
				"default_shop_img" => "upload/default/default_img/shop.png"
			];
		}
		return $res;
	}
	
	/**
	 * 获取缓存类型
	 */
	public function getCacheList()
	{
		return $this->cache_list;
	}
	
	/**
	 * 获取版权信息
	 * @return array
	 */
	public function getCopyright()
	{
		$config = new ConfigModel();
		$res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'COPYRIGHT' ] ]);
		if (empty($res['data']['value'])) {
			$res['data']['value'] = [
				'logo' => '',
				'company_name' => '',
				'copyright_link' => '',
				'copyright_desc' => '',
				'icp' => '',
				'gov_record' => '',
				'gov_url' => '',
			];
		} else {
			$upgrade_model = new Upgrade();
            $auth_info = $upgrade_model->authInfo();
            if (is_null($auth_info) || $auth_info['code'] != 0) {
                $res['data']['value']['logo'] = '';
                $res['data']['value']['company_name'] = '';
                $res['data']['value']['copyright_link'] = '';
                $res['data']['value']['copyright_desc'] = '';
            }
        }
		return $res;
	}



    /**
     * 商城配置
     * array $data
     */
    public function setBasicConfig($data)
    {
        $config = new ConfigModel();
        $res = $config->setConfig($data, '商城配置', 1, [ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'BASIC_CONFIG' ] ]);
        return $res;
    }

    /**
     * 商城配置
     */
    public function getBasicConfig()
    {
        $config = new ConfigModel();
        $res = $config->getConfig([ [ 'site_id', '=', 0 ], [ 'app_module', '=', 'admin' ], [ 'config_key', '=', 'BASIC_CONFIG' ] ]);
        return $res;
    }
}