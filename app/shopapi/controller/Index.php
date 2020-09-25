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


namespace app\shopapi\controller;

use app\model\shop\Shop as ShopModel;
use app\model\shop\ShopReopen as ShopReopenModel;
use app\model\system\Stat;
use Carbon\Carbon;
use app\model\web\WebSite as WebsiteModel;
use app\model\goods\Goods as GoodsModel;
use app\model\system\User as ShopUser;

class Index extends BaseApi
{

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        //店铺基础信息
        $shop_model = new ShopModel();
        $shop_info = $shop_model->getShopInfo([ ['site_id', '=', $this->site_id] ], 'site_id, expire_time, site_name, username, website_id, 
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
        $data['shop_info'] = $shop_info;

        //判断是否有续签
        $reopen_model = new ShopReopenModel();
        $reopen_info = $reopen_model->getReopenInfo([['sr.site_id','=',$this->site_id],['sr.apply_state','in',[1,-1]]]);
        if(empty($reopen_info['data'])){
            $is_reopen = 1;
        }else{
            $is_reopen = 2;
        }
        $data['is_reopen'] = $is_reopen;

        //基础统计信息
        $stat_shop_model = new Stat();
        $today = Carbon::now();
        $yesterday  = Carbon::yesterday();
        $stat_today = $stat_shop_model->getStatShop($this->site_id, $today->year, $today->month, $today->day);
        $stat_yesterday = $stat_shop_model->getStatShop($this->site_id, $yesterday->year, $yesterday->month, $yesterday->day);

        $data['stat_day'] = $stat_today['data'];
        $data['stat_yesterday'] = $stat_yesterday['data'];
        $data['today'] = $today;

        //获取总数
        $shop_stat_sum = $stat_shop_model->getShopStatSum($this->site_id);
        $goods_model = new GoodsModel();
        $goods_sum = $goods_model->getGoodsTotalCount(['site_id' => $this->site_id]);
        $shop_stat_sum['data']['goods_count'] = $goods_sum['data'];
        $data['shop_stat_sum'] = $shop_stat_sum['data'];

        //平台配置信息
        $website_model = new WebsiteModel();
        $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
        $data['website_info'] = $website_info['data'];

        //会员基础信息
        $user_model = new ShopUser();
        $user_info = $user_model->getUserInfo([['uid', '=', $this->uid]], 'username,group_name,login_time');
        $data['shop_user_info'] = $user_info['data'];
        return $this->response($this->success($data));
    }

}