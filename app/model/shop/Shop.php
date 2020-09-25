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


namespace app\model\shop;


use app\model\goods\Goods;
use app\model\system\Config as ConfigModel;
use app\model\system\Group;
use app\model\BaseModel;
use app\model\web\WebSite as WebsiteModel;

/**
 * 店铺信息（无缓存）
 */
class Shop extends BaseModel
{

    /**
     * 添加店铺(注意入驻时长)
     * @param unknown $shop_data 店铺信息(注意是否自营)
     * @param unknown $cert_data 认证信息
     * @param unknown $user_info 用户信息
     */
    public function addShop($shop_data, $cert_data, $user_info)
    {
        model('shop')->startTrans();
        try {
            //添加系统站
            $site_id = model("site")->add(['site_type' => 'shop']);
            $shop_data[ 'site_id' ] = $site_id;
            if ($shop_data[ 'is_own' ] == 1) {
                $shop_data[ 'expire_time' ] = 0;
            } else {
                $shop_data[ 'expire_time' ] = time() + 365 * 24 * 3600 * $shop_data[ 'year' ];
            }
            unset($shop_data[ 'year' ]);
            $shop_data[ 'username' ] = $user_info[ 'username' ];

            //关联前台会员检测
            $member_id = $shop_data[ 'member_id' ];
            if ($member_id != 0) {
                //检测是否有商家申请存在
                $shop_apply_count = model("shop_apply")->getCount([['member_id', '=', $member_id]]);
                if ($shop_apply_count > 0) {
                    model("shop")->rollback();
                    return $this->error('', 'SHOP_APPLY_EXISTED');
                }
                //检测是否已被其他商家关联
                $shop_count = model('shop')->getCount([['member_id', '=', $member_id]]);
                if ($shop_count > 0) {
                    model("shop")->rollback();
                    return $this->error('', 'MEMBER_SHOP_BIND_EXISTED');
                }
            }

            $shop_data[ 'create_time' ] = time();

            $cert_data[ 'site_id' ] = $site_id;
            //添加店铺认证信息
            $cert_id = model("shop_cert")->add($cert_data);

            //添加店铺
            $shop_data[ 'cert_id' ] = $cert_id;
            $res = model("shop")->add($shop_data);
            //添加系统用户组
            $group = new Group();
            $group_data = [
                'site_id' => $site_id,
                'app_module' => 'shop',
                'group_name' => '管理员组',
                'is_system' => 1,
                'create_time' => time()
            ];
            $group_id = $group->addGroup($group_data)[ 'data' ];
            // 添加店铺相册默认分组
            model("album")->add(['site_id' => $site_id, 'album_name' => "默认分组", 'update_time' => time(), 'is_default' => 1]);

            //用户检测
            if (empty($user_info[ 'username' ])) {
                model("shop")->rollback();
                return $this->error('', 'USER_NOT_EXIST');
            }
            $user_count = model("user")->getCount([['username', '=', $user_info[ 'username' ]], ['app_module', '=', 'shop']]);
            if ($user_count > 0) {
                model("shop")->rollback();
                return $this->error('', 'USERNAME_EXISTED');
            }

            //添加用户
            $data_user = [
                'app_module' => 'shop',
                'app_group' => $shop_data[ 'group_id' ],
                'is_admin' => 1,
                'group_id' => $group_id,
                'group_name' => '管理员组',
                'site_id' => $site_id
            ];
            $user_info = array_merge($data_user, $user_info);
            model("user")->add($user_info);
            //执行事件
            event("AddShop", ['site_id' => $site_id]);

            model("shop")->commit();
            return $this->success($res);
        } catch ( \Exception $e ) {
            model("shop")->rollback();
            return $this->error("", $e->getMessage());
        }
    }

    /**
     * 修改店铺(不能随意修改组)
     * @param array $data
     */
    public function editShop($data, $condition)
    {
        $res = model('shop')->update($data, $condition);

        //订单关闭
        if (isset($data[ "shop_status" ]) && $data[ "shop_status" ] == 0) {
            $check_condition = array_column($condition, 2, 0);
            $close_result = event("ShopClose", ["site_id" => $check_condition[ "site_id" ]], true);
            if (!empty($close_result) && $close_result[ "code" ] < 0) {
                return $close_result;
            }
        }
        return $this->success($res);
    }

    /**
     * 获取店铺信息
     * @param array $condition
     * @param string $field
     */
    public function getShopInfo($condition, $field = '*')
    {
        $res = model('shop')->getInfo($condition, $field);
        return $this->success($res);
    }

    /**
     * 获取店铺详情
     * @param int $site_id
     */
    public function getShopDetail($site_id)
    {
        $res = [];
        $shop_info = model('shop')->getInfo([['site_id', '=', $site_id]], 'site_id,expire_time,site_name,username,website_id,cert_id,is_own,level_id,level_name,category_id,category_name,group_id,group_name,shop_status,logo,avatar,banner,seo_description,qq,ww,telephone,is_recommend,shop_desccredit,shop_servicecredit,shop_deliverycredit,workingtime,shop_baozh,shop_baozhopen,shop_baozhrmb,shop_qtian,shop_zhping,shop_erxiaoshi,shop_tuihuo,shop_shiyong,shop_shiti,shop_xiaoxie,shop_sales,shop_adv,sub_num,email');

        $res [ 'shop_info' ] = $shop_info;

//		客服配置信息

        return $this->success($res);
    }

    /**
     * 获取店铺列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getShopList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('shop')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取店铺列表(带认证信息)
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getShopCertList($condition = [], $order = '', $limit = null)
    {
        $alias = 's';
        $join = [
            [
                'shop_cert sc',
                's.cert_id = sc.cert_id',
                'left'
            ]
        ];

        //店铺字段
        $shop_field = 's.site_id,s.expire_time,s.site_name,s.username,s.is_own,s.level_name,s.category_name,s.group_name,
            s.member_name,s.shop_status,s.close_info,s.start_time,s.end_time,s.seo_keywords,s.seo_description,s.qq,
            s.ww,s.telephone,s.is_recommend,s.shop_desccredit,s.shop_servicecredit,s.shop_deliverycredit,s.workingtime,
            s.shop_baozh,s.shop_baozhopen,s.shop_baozhrmb,s.shop_qtian,s.shop_zhping,s.shop_erxiaoshi,s.shop_tuihuo,s.shop_shiyong,
            s.shop_shiti,s.shop_xiaoxie,s.shop_free_time,s.shop_sales,s.account,s.work_week,s.full_address,s.sub_num,';

        //认证字段
        $cert_field = 'sc.cert_type,sc.company_name,sc.company_address,sc.contacts_name,sc.contacts_mobile,sc.contacts_card_no,
            sc.business_licence_number,sc.business_sphere,sc.taxpayer_id,sc.general_taxpayer,sc.tax_registration_certificate,
            sc.bank_account_name,sc.bank_account_number,sc.bank_name,sc.bank_address,sc.bank_code,sc.bank_type,sc.settlement_bank_account_name,
            sc.settlement_bank_account_number,sc.settlement_bank_name,sc.settlement_bank_address';

        $list = model('shop')->getList($condition, $shop_field . $cert_field, $order, $alias, $join);
        return $this->success($list);
    }

    /**
     * 获取店铺分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getShopPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('shop')->rawPageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 编辑店铺认证信息
     * @param unknown $data
     * @param unknown $condition
     */
    public function editShopCert($data, $condition)
    {
        $res = model('shop_cert')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 获取店铺认证信息(包含结算账户)
     * @param unknown $site_id
     */
    public function getShopCert($condition, $field = '*')
    {
        $res = model('shop_cert')->getInfo($condition, $field);
        return $this->success($res);
    }


    /**
     * 获取申请店铺数
     * @param array $condition
     */
    public function getShopCount($condition = [])
    {
        $res = model('shop')->getCount($condition);
        return $this->success($res);
    }

    /**
     * 店铺推广二维码
     * @param $site_id
     * @param string $type
     * @return array
     */
    public function qrcode($site_id, $type = "create")
    {
        $data = [
            'app_type' => "all", // all为全部
            'type' => $type, // 类型 create创建 get获取
            'data' => [
                "site_id" => $site_id
            ],
            'page' => '/otherpages/shop/index/index',
            'qrcode_path' => 'upload/qrcode/shop/',
            'qrcode_name' => "shop_qrcode_" . $site_id,
        ];

        event('Qrcode', $data);
        $app_type_list = config('app_type');


        $path = [];

        //获取站点信息
        $website_model = new WebsiteModel();
        $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'wap_domain');

        foreach ($app_type_list as $k => $v) {
            switch ($k) {
                case 'h5':
                    if (!empty($website_info[ 'data' ][ 'wap_domain' ])) {
                        $path[ $k ][ 'status' ] = 1;
                        $path[ $k ][ 'url' ] = $website_info[ 'data' ][ 'wap_domain' ] . $data[ 'page' ] . '?site_id=' . $site_id;
                        $path[ $k ][ 'img' ] = $data[ 'qrcode_path' ] . $data[ 'qrcode_name' ] . '_' . $k . '.png';
                    } else {
                        $path[ $k ][ 'status' ] = 2;
                        $path[ $k ][ 'message' ] = '未配置手机端域名';
                    }
                    break;
                case 'weapp' :
                    $config = new ConfigModel();
                    $res = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'WEAPP_CONFIG']]);
                    if (!empty($res[ 'data' ])) {
                        if (empty($res[ 'data' ][ 'value' ][ 'qrcode' ])) {
                            $path[ $k ][ 'status' ] = 2;
                            $path[ $k ][ 'message' ] = '未配置微信小程序';
                        } else {
                            $path[ $k ][ 'status' ] = 1;
                            $path[ $k ][ 'img' ] = $data[ 'qrcode_path' ] . $data[ 'qrcode_name' ] . '_' . $k . '.png';;
                        }

                    } else {
                        $path[ $k ][ 'status' ] = 2;
                        $path[ $k ][ 'message' ] = '未配置微信小程序';
                    }
                    break;

                case 'wechat' :
                    $config = new ConfigModel();
                    $res = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'WECHAT_CONFIG']]);
                    if (!empty($res[ 'data' ])) {
                        if (empty($res[ 'data' ][ 'value' ][ 'qrcode' ])) {
                            $path[ $k ][ 'status' ] = 2;
                            $path[ $k ][ 'message' ] = '未配置微信公众号';
                        } else {
                            $path[ $k ][ 'status' ] = 1;
                            $path[ $k ][ 'img' ] = $res[ 'data' ][ 'value' ][ 'qrcode' ];
                        }
                    } else {
                        $path[ $k ][ 'status' ] = 2;
                        $path[ $k ][ 'message' ] = '未配置微信公众号';
                    }
                    break;
            }

        }

        $return = [
            'path' => $path,
        ];
        return $this->success($return);
    }

    /**
     * 添加店铺(安装时调用)
     * @param unknown $shop_data 店铺信息(注意是否自营)
     * @param unknown $user_info 用户信息
     */
    public function installShop($shop_data, $user_info)
    {
        model('shop')->startTrans();
        try {
            //添加系统站
            $site_id = model("site")->add(['site_type' => 'shop']);

            $cert_data = ['site_id' => $site_id];

            //添加店铺认证信息
            $cert_id = model("shop_cert")->add($cert_data);

            $shop_data[ 'site_id' ] = $site_id;

            $shop_data[ 'expire_time' ] = 0;
            $shop_data[ "cert_id" ] = $cert_id;

            $shop_data[ 'username' ] = $user_info[ 'username' ];

            $shop_data[ 'create_time' ] = time();
            //添加店铺
            $res = model("shop")->add($shop_data);
            $cert_data[ 'site_id' ] = $site_id;

            //添加系统用户组
            $group = new Group();
            $group_data = [
                'site_id' => $site_id,
                'app_module' => 'shop',
                'group_name' => '管理员组',
                'is_system' => 1,
                'menu_array' => '',
                'create_time' => time()
            ];
            $group_id = $group->addGroup($group_data)[ 'data' ];
            // 添加店铺相册默认分组
            model("album")->add(['site_id' => $site_id, 'album_name' => "默认分组", 'update_time' => time(), 'is_default' => 1]);

            //添加用户
            $data_user = [
                'app_module' => 'shop',
                'app_group' => $shop_data[ 'group_id' ],
                'is_admin' => 1,
                'group_id' => $group_id,
                'group_name' => '管理员组',
                'site_id' => $site_id
            ];
            $user_info = array_merge($data_user, $user_info);
            model("user")->add($user_info);
            //执行事件
            event("AddShop", ['site_id' => $site_id]);

            model("shop")->commit();
            return $this->success($res);
        } catch ( \Exception $e ) {
            model("shop")->rollback();
            return $this->error("", $e->getMessage());
        }
    }

    /**
     * 店铺关闭
     * @param $site_id
     */
    public function shopClose($site_id)
    {
        $res = model("shop")->update(["shop_status" => 0], [["site_id", "=", $site_id]]);
        if ($res === false)
            return $this->error();

        $close_result = event("ShopClose", ["site_id" => $site_id], true);
        if (!empty($close_result) && $close_result[ "code" ] < 0) {
            return $close_result;
        }
        return $this->success();
    }

}