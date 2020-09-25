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


namespace app\admin\controller;

use app\model\system\Upgrade as UpgradeModel;
use app\model\system\Web;
use think\facade\Cache;
use app\model\system\Config;
use app\model\system\Stat as StatModel;
use Carbon\Carbon;
use app\model\system\User as UserModel;
use app\model\shop\ShopApply;
use app\model\shop\Shop as ShopModel;
use app\model\goods\Goods as GoodsModel;
use app\model\order\Order as OrderModel;

/**
 * 首页 控制器
 */
class Index extends BaseAdmin
{
	
	/**
	 * 首页
	 */
	public function index()
	{
		$config_model = new Config();
		
		$stat_model = new StatModel();
		
		$carbon = Carbon::now();
		
		$stat_info = $stat_model->getStatShop(0, $carbon->year, $carbon->month, $carbon->day);
		
		$this->assign('stat_info', $stat_info['data']);

		//订单总额
        $order_model = new OrderModel();
        $order_money = $order_model->getOrderMoneySum();
        $this->assign('order_money',$order_money['data']);

		//会员总数
        $user_model = new UserModel();
        $member_count = $user_model->getMemberTotalCount();
        $this->assign('member_count',$member_count['data']);
        //今日新增店铺数
        $shop_apply_model = new ShopApply();
        $condition = [
            ['create_time','between',[date_to_time(date('Y-m-d 00:00:00')),date_to_time(date('Y-m-d 23:59:59'))]]
        ];
        $shop_apply_count = $shop_apply_model->getShopApplyCount($condition);
        $this->assign('shop_apply_count',$shop_apply_count['data']);
        //今日申请店铺数
        $shop_model = new ShopModel();
        $shop_count = $shop_model->getShopCount($condition);
        $this->assign('shop_count',$shop_count['data']);

        //店铺总数
        $shop_model = new ShopModel();
        $shop_total_count = $shop_model->getShopCount();
        $this->assign('shop_total_count',$shop_total_count['data']);

        //商品总数
        $goods_model = new GoodsModel();
        $goods_count = $goods_model->getGoodsTotalCount();
        $this->assign('goods_count',$goods_count['data']);
//		$condition = [ 'nsu.uid' => UID ];
//
//		$user_model = new UserModel();
//		$addon_model = new AddonModel();
//
//		$addon_name = input('addon_name', '');
//		if ($addon_name) {
//			$condition['ns.addon_app'] = $addon_name;
//		}
//		$this->assign('addon_name', $addon_name);
//
//		$site_name = input('site_name', '');
//
//		if ($site_name) {
//			$condition['ns.site_name'] = [
//				'like',
//				'%' . $site_name . '%'
//			];
//		}
//
//		$list = $user_model->getUserSiteList($condition);
//
//		$list_new = $list;
// 		foreach ($list['data'] as $k => $v) {
//			if ($v['type'] == 'ADDON_APP') {
//				$app_class = get_addon_class($v['addon_app']);
//				$app_class = new $app_class();
//				$style = isset($app_class->css) ? $app_class->css : '';
//				$v['style'] = $style;
//			} else {
//				$v['style'] = '';
//			}
//			$v["app_type"] = getSupportPort($v["app_type"]);
//
//			$list_new['data'][ $k ] = $v;
//			unset($v);
//		}
//
//		if (IS_AJAX) {
//			return $list_new;
//		} else {
//
//			$res = $addon_model->getAddonList([ 'type' => 'ADDON_APP', 'visble' => 1 ], 'name, title');
//			$apps = $res['data'];
//			$this->assign('app_list', $apps);
//
//			$this->assign('site_list', $list_new['data']);
//			$this->assign("title", "站点列表");

        //获取最新版本
       	$upgrade_model = new UpgradeModel();
        $last_version = $upgrade_model->getLatestVersion();
        $last_release = $last_version['data']['version_no'] ?? 0;
        $need_upgrade = 0;
        $app_info = config('info');
        if ($app_info['version_no'] < $last_release) {
            $need_upgrade = 1;
        }

		$system_config = $config_model->getSystemConfig();

        $this->assign('need_upgrade', $need_upgrade);
        $this->assign('sys_product_name', $app_info['name']);
        $this->assign('sys_version_no', $app_info['version_no']);
        $this->assign('sys_version_name', $app_info['version']);
        $this->assign('system_config', $system_config['data']);
		
		$this->assign('system_config', $system_config['data']);

		//待处理的事物
            $this->todo();
		return $this->fetch('index/index');
//		}
	}

    /**
     * 待处理项
     */
	public function todo(){
            //待审核商品
          $goods_model = new \app\model\goods\Goods();
          $goods_count_result = $goods_model->getGoodsTotalCount([["verify_state", "=", 0], ["is_delete", "=", 0]]);
          $this->assign("verify_goods_count", $goods_count_result["data"]);
          //举报商品
          $inform_model = new \app\model\goods\Inform();
          $inform_count_result = $inform_model->getInformCount([["state", "=", 0]]);
          $this->assign("inform_count", $inform_count_result["data"]);

          //查询平台维权数量
          $complain_model = new \app\model\order\Complain();
          $complain_count_result =$complain_model->getComplainCount([["complain_status", "=", 1]]);
          $this->assign("complain_count_result", $complain_count_result["data"]);

          //店铺入驻申请
            $shop_apply_model = new \app\model\shop\ShopApply();
          $shop_apply_count_result = $shop_apply_model->getShopApplyCount([["apply_state", "in", [1,2]]]);
          $this->assign("shop_apply_count", $shop_apply_count_result["data"]);
          //店铺续签申请

          $shop_reopen_model = new \app\model\shop\ShopReopen();
          $shop_reopen_count_result = $shop_reopen_model->getApplyReopenCount([["apply_state", "=", 1]]);
          $this->assign("shop_reopen_count", $shop_reopen_count_result["data"]);

      }

    /**
     * 官网资讯
     */
      public function news(){
          $web_model = new Web();
          $result = $web_model->news();
          return $result;
      }
	/**
	 * 账号及安全
	 */
//	public function security()
//	{
//		$user_model = new UserModel();
//		$bind_mobile_info = $user_model->getUserBindMobileInfo(UID);
//		$this->assign('bind_mobile_info', $bind_mobile_info);
//		$this->assign('uid', UID);
//		$user_info = $user_model->getUserInfo([ 'uid' => UID ]);
//		$this->assign('user_info', $user_info['data']);
//		return $this->fetch('index/security');
//	}
	
	/**
	 * 校验当前域名
	 */
//	public function checkSiteDomain()
//	{
//		$site_model = new Site();
//		//查询所有站点绑定域名
//		$domains = cache('domains');
//		if (!$domains) {
//
//			$domains = $site_model->getSiteDomains();
//			cache('domains', $domains['data']);
//		}
//		//获取当前域名用于检测当前域名是否是站点绑定域名
//		$domain = request()->domain();
//		//检测是否存在绑定站点域名
//		if (array_key_exists($domain, $domains)) {
//			request()->siteid($domains[ $domain ]);
//			$site_info = $site_model->getSiteInfo([ 'site_id' => $domains[ $domain ] ]);
//			if (!empty($site_info)) {
//				$this->error("当前站点域名已配置！", $domain . "/admin/index");
//			}
//		}
//	}
	
	/**
	 * 用户绑定手机操作
	 * @return mixed
	 */
//	public function bindMobile()
//	{
//		if (IS_AJAX) {
//			$mobile = input('mobile', '');
//            $sms_code = input('sms_code', '');
//			if(empty($mobile)){
//			    return error(-1, "手机号不可以为空");
//            }
//            $key = md5("bind_mobile_code_" . 0 . "_" . $mobile);
//            $code = Cache::get($key);
//            if (empty($code)) {
//                return error(-1, "短信动态码已失效");
//            }
//            if ($sms_code != $code) {
//                return error(-1, "短信动态码错误");
//            }
//
//			$user_model = new UserModel();
//			$res = $user_model->bindMobile($mobile, UID);
//			return $res;
//		}
//	}
	
	/**
	 * 更改绑定手机号
	 * @return \multitype
	 */
//	public function updateMobile(){
//        if (IS_AJAX) {
//            $mobile = input('mobile', '');
//            $sms_code = input('sms_code', '');
//            if(empty($mobile)){
//                return error(-1, "手机号不可以为空");
//            }
//            //验证当前手机号
//            $key = md5("bind_mobile_code_" . 0 . "_" . $mobile);
//            $code = Cache::get($key);
//            if (empty($code)) {
//                return error(-1, "短信动态码已失效");
//            }
//            if ($sms_code != $code) {
//                return error(-1, "短信动态码错误");
//            }
//
//            $user_model = new UserModel();
//            $res = $user_model->bindMobile($mobile, UID);
////	        $user_model->refreshUserInfoSession(UID);
//            return $res;
//        }
//    }
	
	/**
	 * 检测手机号是否存在
	 */
//	public function checkMobileIsExist()
//	{
//		if (IS_AJAX) {
//			$mobile = input('mobile', '');
//			$user_model = new UserModel();
//			$res = $user_model->checkMobileIsExist($mobile, UID);
//			return $res;
//		}
//	}
	
	/**
	 * 绑死手机发送验证码
	 */
//	public function sendSmsCode(){
//        $mobile = input("mobile", '');
//        if (empty($mobile)) {
//            return error(-1, "手机号不可以为空!");
//        }
//
//        $user_model = new User();
//        $exist_result = $user_model->checkMobileIsExist($mobile);
//        $exist_count = $exist_result["data"];
//        if($exist_count > 0){
//            return error(-1, "当前手机号已存在!");
//        }
//
//       $code = rand(100000, 999999);
//       $data = [ "keyword" => "BIND_MOBILE", "site_id" => 0, 'code' => $code, 'support_type' => "Sms", 'mobile' => $mobile ];//仅支持短信发送
//       $res = hook("SendMessage", $data);
//       if($res[0]["code"] == 0){
//           $key = md5("bind_mobile_code_" . 0 . "_" . $mobile);
//           Cache::set($key, $code, 3600);
//       }
//       return $res[0];
//    }

//    public function test(){
//        $addon = new Addon();
//        $data = $addon->getAddons();
//        $event = include 'app/event.php';
//         foreach ($data['addon_path'] as $k => $v)
//        {
//            $addon_event = require_once $v.'config/event.php';
//            $event['bind'] = array_merge($event['bind'], $addon_event['bind']);
//            $event['listen'] = array_merge($event['listen'], $addon_event['listen']);
//
//        }
//
//    }



}
