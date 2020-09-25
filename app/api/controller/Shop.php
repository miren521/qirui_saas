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


namespace app\api\controller;

use app\model\shop\Poster;
use app\model\shop\Shop as ShopModel;
use app\model\web\WebSite;

class Shop extends BaseApi
{

    /**
     * 基础信息
     */
    public function info()
    {
        $site_id = isset($this->params[ 'site_id' ]) ? $this->params[ 'site_id' ] : 0;
        if (empty($site_id)) {
            return $this->response($this->error('', 'REQUEST_SITE_ID'));
        }
        $shop = new ShopModel();
        $field = 'site_id,expire_time,site_name,website_id,is_own,level_name,category_name,shop_status,start_time,logo,avatar,banner,
		seo_description,qq,ww,telephone,shop_desccredit,shop_servicecredit,shop_deliverycredit,workingtime,shop_baozh,shop_baozhopen,shop_baozhrmb,
		shop_qtian,shop_zhping,shop_erxiaoshi,shop_tuihuo,shop_shiyong,shop_shiti,shop_xiaoxie,shop_free_time,shop_sales,shop_adv,work_week,address,full_address,longitude,latitude,sub_num';
        $info = $shop->getShopInfo([['site_id', '=', $site_id]], $field);
        return $this->response($info);
    }

    public function page()
    {
        $page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
        $page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
        $keyword = isset($this->params[ 'keyword' ]) ? $this->params[ 'keyword' ] : '';//关键词
        $order = isset($this->params[ 'order' ]) ? $this->params[ 'order' ] : "site_id";//排序（综合、销量、信用）
        $sort = isset($this->params[ 'sort' ]) ? $this->params[ 'sort' ] : "desc";//升序、降序
        $web_city = isset($this->params[ 'web_city' ]) ? $this->params[ 'web_city' ] : "";
        $lat = isset($this->params[ 'lat' ]) ? $this->params[ 'lat' ] : ""; // 纬度
        $lng = isset($this->params[ 'lng' ]) ? $this->params[ 'lng' ] : ""; // 经度

        $shop = new ShopModel();
        $condition = [
            ['shop_status', '=', 1],
            ['cert_id', '<>', 0]
        ];

        if (!empty($keyword)) {
            $condition[] = ['site_name', 'like', '%' . $keyword . '%'];
        }

        // 非法参数进行过滤
        if ($sort != "desc" && $sort != "asc") {
            $sort = "";
        }

        // 非法参数进行过滤
        if ($order != '') {
            if ($order != "shop_sales" && $order != "shop_desccredit") {
                $order = 'site_id';
            }
            $order_by = $order . ' ' . $sort;
        } else {
            $order_by = 'is_recommend desc,sort desc,site_id desc';
        }

        // 查询是否存在城市分站
        if (addon_is_exit('city') && !empty($web_city)) {
            $website_model = new WebSite();
            $website_info = $website_model->getWebSite([['site_area_id', '=', $web_city]], 'site_id');
            if (!empty($website_info[ 'data' ])) {
                $order_by = "INSTR('{$website_info['data']['site_id']}', website_id) desc," . $order_by;
            }
        }
        $list = $shop->getShopPageList($condition, $page, $page_size, $order_by, 'site_id,site_name,category_name,group_name,logo,avatar,banner,seo_description,shop_desccredit,shop_servicecredit,shop_deliverycredit,shop_sales,sub_num,is_own,longitude,latitude,telephone,address,full_address');

        if (!empty($list[ 'data' ][ 'list' ])) {
            foreach ($list[ 'data' ][ 'list' ] as $k => $item) {
                if ($item[ 'longitude' ] && $item[ 'latitude' ] && $lng && $lat) {
                    $list[ 'data' ][ 'list' ][ $k ][ 'distance' ] = round(getDistance((float)$item[ 'longitude' ], (float)$item[ 'latitude' ], (float)$lng, (float)$lat));
                } else {
                    $list[ 'data' ][ 'list' ][ $k ][ 'distance' ] = 0;
                }
            }
        }

        return $this->response($list);
    }

    /**
     * 是否显示店铺相关功能，用于审核小程序
     */
    public function isShow()
    {
        $res = 1;// 0 隐藏，1 显示
        return $this->response($this->success($res));
    }


    /**
     * 获取商品海报
     */
    public function poster()
    {
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));

        $qrcode_param = json_decode($this->params[ 'qrcode_param' ], true);
        $qrcode_param[ 'source_member' ] = $qrcode_param[ 'source_member' ] ?? 0;
        $poster = new Poster();
        $res = $poster->shop($this->params[ 'app_type' ], $this->params[ 'page' ], $qrcode_param);
        return $this->response($res);
    }
}