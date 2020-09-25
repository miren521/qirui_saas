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


namespace app\shop\controller;


use app\model\shop\Shop;
use app\model\system\Stat;
use Carbon\Carbon;
use app\model\system\User as ShopUser;
use app\model\system\Promotion as PrmotionModel;
use app\model\shop\Config as ConfigModel;
use app\model\web\Help as HelpModel;
use app\model\web\WebSite as WebsiteModel;
use app\model\shop\Config as ShopConfigModel;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\goods\Goods as GoodsModel;
use app\model\shop\ShopReopen as ShopReopenModel;
use app\model\web\Notice as NoticeModel;

class Index extends BaseShop
{

    /**
     * 首页
     * @return mixed
     */
	public function index()
	{
	    //店铺基础信息
	    $shop_model = new Shop();
	    $shop_info = $shop_model->getShopInfo([['site_id', '=', $this->site_id]], 'site_id, expire_time, site_name, username, website_id, 
	        cert_id, is_own, level_id, level_name, category_id, category_name, group_id, group_name, member_id, member_name,
	        shop_status, close_info, sort, start_time, end_time, logo, avatar, banner, seo_keywords, seo_description, qq, ww, 
	        telephone, is_recommend, shop_desccredit, shop_servicecredit, shop_deliverycredit, workingtime, shop_baozh,
	        shop_baozhopen, shop_baozhrmb, shop_qtian, shop_zhping, shop_erxiaoshi, shop_tuihuo, shop_shiyong, shop_shiti, 
	        shop_xiaoxie, shop_free_time, shop_sales, shop_adv, account, account_withdraw, work_week, province, province_name, 
	        city, city_name, district, district_name, community, community_name, address, full_address, longitude, latitude, 
	        sub_num');

        $shop_info = $shop_info['data'];
        
        if ($shop_info['expire_time'] == 0) {

            $shop_info['is_reopen'] = 1;//永久有效
        }elseif ($shop_info['expire_time'] > time()) {

            $cha = $shop_info['expire_time'] - time();
            $date = ceil(($cha/86400));
            if($date < 30){
                $shop_info['is_reopen'] = 2;//离到期一月内才可以申请续签
                $shop_info['expires_date'] = (int)$date;
            }
        }else{
            $shop_info['is_reopen'] = 3;
            $shop_info['expires_date'] = 0;
        }
	    $this->assign("shop", $shop_info);
        //判断是否有续签
        $reopen_model = new ShopReopenModel();
        $reopen_info = $reopen_model->getReopenInfo([['sr.site_id','=',$this->site_id],['sr.apply_state','in',[1,-1]]]);
        if(empty($reopen_info['data'])){
            $is_reopen = 1;
        }else{
            $is_reopen = 2;
        }
        $this->assign('is_reopen',$is_reopen);

	    //会员基础信息
	    $user_model = new ShopUser();
	    $user_info = $user_model->getUserInfo([['uid', '=', $this->uid]], 'username,group_name,login_time');
	    $this->assign("shop_user_info", $user_info['data']);
	    //基础统计信息
	    $stat_shop_model = new Stat();
	    $today = Carbon::now();
	    $yesterday  = Carbon::yesterday();
	    $stat_today = $stat_shop_model->getStatShop($this->site_id, $today->year, $today->month, $today->day);
	    $stat_yesterday = $stat_shop_model->getStatShop($this->site_id, $yesterday->year, $yesterday->month, $yesterday->day);
	    $this->assign("stat_day", $stat_today['data']);
	    $this->assign("stat_yesterday", $stat_yesterday['data']);
        $this->assign("today", $today);

        //获取总数
        $shop_stat_sum = $stat_shop_model->getShopStatSum($this->site_id);
        $goods_model = new GoodsModel();
        $goods_sum = $goods_model->getGoodsTotalCount(['site_id' => $this->site_id]);
        $shop_stat_sum['data']['goods_count'] = $goods_sum['data'];
        $this->assign('shop_stat_sum', $shop_stat_sum['data']);
        //营销活动
        $promotion_model = new PrmotionModel();
        $promotions = $promotion_model->getPromotions();
        $shop_group_model = new ShopGroupModel();
        $addon_array = $shop_group_model->getGroupInfo(['group_id' => $this->shop_info['group_id']], 'addon_array');
        $addon_array = explode(',', $addon_array['data']['addon_array']);
        foreach ($promotions['shop'] as $key => $promotion) {
            if (!empty($promotion['is_developing'])) {
                unset($promotions['shop'][$key]);
                continue;
            }
            if (!in_array($promotion['name'], $addon_array)) {
                unset($promotions['shop'][$key]);
            }
        }

        $this->assign("promotion", $promotions['shop']);

        //入驻指南
        $config_model = new ConfigModel();
        $shop_join_guide_list = $config_model->getShopJoinGuide();
        $this->assign("shop_join_guide_list", $shop_join_guide_list['data']);

        //入驻帮助
        $help_model = new HelpModel();

        $help_condition = [
            ['app_module', '=', 'shop']
        ];

        $order = 'create_time desc';
        $help_list = $help_model->getHelpPageList($help_condition, 1, 5, $order);
        $this->assign("help_list", $help_list['data']['list']);

        //平台配置信息
//        $website_model = new WebsiteModel();
//        $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
//        $this->assign('website_info',$website_info['data']);

        //店铺等级
        $shop_group_model = new ShopGroupModel();
        $shop_group_list = $shop_group_model->getGroupList([['is_own','=',0]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
        $this->assign('shop_group_list', $shop_group_list['data']);

        //网站公告
        $notice_model = new NoticeModel();
        $notice_list = $notice_model->getNoticePageList([['receiving_type','like','%shop%']], 1, 5,'is_top desc,create_time desc','id,title');
        $this->assign('notice_list', $notice_list['data']['list']);

	    return $this->fetch("index/index");
	}

}