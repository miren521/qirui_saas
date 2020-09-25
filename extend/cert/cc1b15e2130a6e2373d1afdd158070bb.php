<?php
namespace app\event;
use app\model\system\Addon;
use think\facade\Lang;
use think\facade\Event;
use think\facade\Cache;
class InitAddon {
	public function handle() {
		if (defined('BIND_MODULE') && BIND_MODULE === 'install') return;
		$this->initEvent();
	}
	private function InitEvent() {
		$cache = Cache::get("addon_event_list");
		if(empty($cache)) {
			$addon_model = new Addon();
			$addon_data = $addon_model->getAddonList([], 'name');
			$listen_array = [];
			foreach ($addon_data['data'] as $k => $v) {
				if(file_exists('addon/'.$v['name'].'/config/event.php')) {
					$addon_event = require_once 'addon/'.$v['name'].'/config/event.php';
					$listen = isset($addon_event['listen']) ? $addon_event['listen'] : [];
					if(!empty($listen)) {
						$listen_array[] = $listen;
					}
				}
			}
			Cache::tag("addon")->set("addon_event_list", $listen_array);
		} else {
			$listen_array = $cache;
		}
		if(!empty($listen_array)) {
			foreach ($listen_array as $k => $listen) {
				if(!empty($listen)) {
					Event::listenEvents($listen);
				}
			}
		}
	}
}