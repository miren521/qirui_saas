<?php
namespace app\event;
use app\model\system\Upgrade;
use think\app\Service;
use think\facade\Route;
use app\model\system\Addon;
use think\facade\Cache;
class InitRoute extends Service {
	public function handle() {
		if (defined('BIND_MODULE') && BIND_MODULE === 'install') return;
		$ip = request()->ip();
		if($ip != '127.0.0.1' && $ip != '0.0.0.0' && $ip != '::1') {
			$cert = file_get_contents('cert.key');
			if(empty($cert)) {
				die('当前系统未授权，请联系管理员!');
			}
			$cert_data = $this->decrypt($cert);
                        
			if(!empty($cert_data)) {
                           
				define("NIUSHOP_AUTH_CODE", $cert_data['devolution_code']);
				$time = time();
				$url = request()->domain();
				if($cert_data['devolution_url'] == 'niutest') {
					if($time > $cert_data['devolution_expire_date']) {
						die('当前系统未授权，请联系管理员!');
					}
					define("NIUSHOP_AUTH_VERSION",$cert_data['module_mark']);
				} else {
					if(strpos($url,$cert_data['devolution_url']) !== false) {
						if(($time > $cert_data['devolution_expire_date']) && $cert_data['devolution_expire_date'] != 0) {
							die('当前系统未授权，请联系管理员!');
						}
						define("NIUSHOP_AUTH_VERSION",$cert_data['module_mark']);
					} else {
						define("NIUSHOP_AUTH_VERSION",$cert_data['module_mark']);
						//die('当前系统未授权，请联系管理员!');
					}
				}
			} else {
				//die('当前系统未授权，请联系管理员!');
			}
		} else {
			define("NIUSHOP_AUTH_VERSION", SYS_VERSION);
			if (file_exists('cert.key')) {
				$cert = file_get_contents('cert.key');
				if(!empty($cert)) {
					$cert_data = $this->decrypt($cert);
					if (!empty($cert_data)) {
						define("NIUSHOP_AUTH_CODE", $cert_data['devolution_code']);
					}
				}
			}
		}
		$system_array = [ 'admin', 'shop', 'install', 'cron', 'api', 'pay'];
		$pathinfo = request()->pathinfo();
		$pathinfo_array = explode('/', $pathinfo);
		$check_model = $pathinfo_array[0];
		$addon = in_array($check_model, $system_array) ? '' : $check_model;
		if(!empty($addon)) {
			$addons_auth = $this->addonsAuth();
			if(!empty($addons_auth) && !in_array($addon, $addons_auth)) {
				//die('当前系统未授权，请联系管理员!');
			}
			$module = isset($pathinfo_array[1]) ? $pathinfo_array[1] : 'admin';
			$controller = isset($pathinfo_array[2]) ? $pathinfo_array[2] : 'index';
			$method = isset($pathinfo_array[3]) ? $pathinfo_array[3] : 'index';
			request()->addon($addon);
			$this->app->setNamespace("addon\\".$addon.'\\'.$module);
			$this->app->setAppPath($this->app->getRootPath() . 'addon' . DIRECTORY_SEPARATOR. $addon.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR);
		} else {
			$module = isset($pathinfo_array[0]) ? $pathinfo_array[0] : 'admin';
			$controller = isset($pathinfo_array[1]) ? $pathinfo_array[1] : 'index';
			$method = isset($pathinfo_array[2]) ? $pathinfo_array[2] : 'index';
		}
		$pathinfo = str_replace(".html", '', $pathinfo);
		$controller = str_replace(".html", '', $controller);
		$method = str_replace(".html", '', $method);
		request()->module($module);
		Route::rule($pathinfo, $module.'/'.$controller. '/'. $method);
	}
	private function decrypt($data) {
		$format_data = substr($data, 32);
		$time = substr($data, -10);
		$decrypt_data = strstr($format_data, $time);
		$key = str_replace($decrypt_data, '', $format_data);
		$data = str_replace($time, '', $decrypt_data);
		$json_data = decrypt($data, $key);
		$array = json_decode($json_data, true);
		if($array['time'] == md5($time.'niushop'.$key)) {
			$cache = Cache::get("niushop_auth_tag");
			if(empty($cache)) {
				$domain = request()->domain();
				$redirect = 'http://my.lpstx.cn/api/Version/getNewVersion?key='.$key.'&url='.$domain.'&version='.$array['module_mark'];
                                Cache::set("niushop_auth_tag", 1, 3600*24*2);
				http($redirect);
			}
			return $array;
		} else {
			return [];
		}
	}
	private function addonsAuth() {
		$cache = Cache::get('auth_addon');
		if (!empty($cache)) return $cache;
		$upgrade = new Upgrade();
		$auth_addons = $upgrade->getAuthAddons();
		$addons = [];
		if ($auth_addons['code'] == 0) {
			$addons = array_column($auth_addons['data'], 'code');
		}
		Cache::set('auth_addon', $addons);
		return $addons;
	}
}