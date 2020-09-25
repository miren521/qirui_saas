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
use app\model\web\WebSite as WebsiteModel;
use Exception;
use think\facade\Cache;
use app\model\BaseModel;
use app\model\system\Group;

/**
 * 系统站点信息管理
 * @author Administrator
 *
 */
class WebSite extends BaseModel
{
    public $from_type = [
        'order'    => [
            'type_name' => '订单结算',
            'type_url'  => '',
        ],
        'shop'     => [
            'type_name' => '店铺入驻',
            'type_url'  => '',
        ],
        'withdraw' => [
            'type_name' => '提现',
            'type_url'  => '',
        ],
    ];

    /**
     * 获取站点信息
     * @param $condition
     * @param string $field
     * @param bool $cache_use
     * @return array
     */
    public function getWebSite($condition, $field = '*', $cache_use = true)
    {
        $data  = json_encode([$condition, $field]);
        $cache = Cache::get("weisite_getWebSite_" . $data);
        if ($cache_use && !empty($cache)) {
            return $this->success($cache);
        }
        $res = model('website')->getInfo($condition, $field);
        if (empty($res)) {
            $check_condition = array_column($condition, 2, 0);
            //初始化站点信息
            if (isset($check_condition['site_id']) && $check_condition['site_id'] == 0) {
                model('website')->add(['site_id' => $check_condition['site_id']]);
                $res = model('website')->getInfo($condition, $field);
            }
        }
        Cache::tag("website")->set("weisite_getWebSite_" . $data, $res);
        return $this->success($res);
    }

    /**
     * 设置站点信息
     * @param $data
     * @param $condition
     * @return array
     */
    public function setWebSite($data, $condition)
    {
        $website_count = model('website')->getCount($condition);
        if ($website_count == 0) {
            $data['create_time'] = time();
            $res                 = model('website')->add($data);
        } else {
            $data['modify_time'] = time();
            $res                 = model('website')->update($data, $condition);
        }
        Cache::tag("website")->clear();
        return $this->success($res);
    }

    /**
     * 添加分站
     * @param $data
     * @param $user_data
     * @return array
     */
    public function addWebsite($data, $user_data)
    {
        $data['create_time'] = time();

        model('website')->startTrans();
        try {
            if ($data['site_area_id'] == 0) {
                return $this->error('', '请选择城市分站地址');
            }
            //判断分站是否已存在
            $city_count = model('website')->getCount([['site_area_id', '=', $data['site_area_id']]]);
            if ($city_count > 0) {
                return $this->error('', '该城市分站已存在');
            }
            $site_id = model("site")->add(['site_type' => 'city']);

            $data['site_id'] = $site_id;
            $website_id      = model('website')->add($data);

            //添加系统用户组
            $group      = new Group();
            $group_data = [
                'site_id'     => $site_id,
                'app_module'  => 'city',
                'group_name'  => '管理员组',
                'is_system'   => 1,
                'create_time' => time()
            ];
            $group_id   = $group->addGroup($group_data)['data'];

            //用户检测
            if (empty($user_data['username'])) {
                model("website")->rollback();
                return $this->error('', 'USER_NOT_EXIST');
            }
            $user_count = model("user")->getCount([['username', '=', $user_data['username']], ['app_module', '=', 'city']]);
            if ($user_count > 0) {
                model("website")->rollback();
                return $this->error('', 'USERNAME_EXISTED');
            }

            //添加用户
            $data_user = [
                'app_module' => 'city',
                'app_group'  => 0,
                'is_admin'   => 1,
                'group_id'   => $group_id,
                'group_name' => '管理员组',
                'site_id'    => $site_id
            ];
            $user_info = array_merge($data_user, $user_data);
            model("user")->add($user_info);

            // 添加自定义首页
            $diy_view_model = new DiyView();
            $diy_view_info  = [
                'id'      => 0,
                'site_id' => $site_id,
                'title'   => $data['title'],
                'name'    => "DIYVIEW_INDEX",
                'type'    => "city",
                'value'   => json_encode([
                    "global" => [
                        "title"         => "网站主页",
                        "openBottomNav" => false,
                        "bgColor"       => "#ffffff",
                        "bgUrl"         => ""
                    ],
                    "value"  => [
                        [
                            "left_img_url"     => "",
                            "left_link"        => [],
                            "right_img_url"    => "",
                            "right_link"       => [],
                            "background_color" => "#e43130",
                            "addon_name"       => "",
                            "type"             => "SEARCH",
                            "name"             => "顶部搜索",
                            "controller"       => "Search"
                        ],
                        [
                            "sources"    => "default",
                            "skuId"      => "",
                            "categoryId" => 0,
                            "goodsCount" => 12,
                            "addon_name" => "",
                            "type"       => "GOODS_LIST",
                            "name"       => "商品列表",
                            "controller" => "GoodsList"
                        ]
                    ]
                ])
            ];
            $diy_view_model->addSiteDiyView($diy_view_info);

            model('website')->commit();
            return $this->success($website_id);
        } catch (Exception $e) {
            model('website')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 删除分站
     * @param $site_id
     * @return array
     */
    public function deleteWebsite($site_id)
    {
        $shop_count = model('shop')->getCount([['website_id', '=', $site_id]]);
        if ($shop_count > 0) {
            return $this->error('', '该分站下已有商家，不能删除');
        }

        $shop_apply_count = model('shop_apply')->getCount([['website_id', '=', $site_id]]);
        if ($shop_apply_count > 0) {
            return $this->error('', '该分站下已有商家，不能删除');
        }

        model('website')->startTrans();
        try {
            //站点信息
            $website_info = model('website')->getInfo([['site_id', '=', $site_id]], 'username');
            $res          = model('website')->delete([['site_id', '=', $site_id]]);
            if (!empty($website_info)) {
                //获取用户信息
                $user_info = model('user')->getInfo(
                    [['username', '=', $website_info['username']], ['app_module', '=', 'city']],
                    'group_id'
                );
                if (!empty($user_info)) {
                    //删除用户
                    model('user')->delete([['username', '=', $website_info['username']], ['app_module', '=', 'city']]);
                    //删除用户组
                    model('group')->delete([['group_id', '=', $user_info['group_id']]]);
                }
            }
            model('website')->commit();
            return $this->success($res);
        } catch (Exception $e) {
            model('website')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 冻结分站
     * @param $site_id
     * @return array
     */
    public function frozenWebsite($site_id)
    {
        $res = model('website')->update(['status' => -1, 'modify_time' => time()], [['site_id', '=', $site_id]]);
        return $this->success($res);
    }

    /**
     * 解冻分站
     * @param $site_id
     * @return array
     */
    public function unfrozenWebsite($site_id)
    {
        $res = model('website')->update(['status' => 1], [['site_id', '=', $site_id]]);
        return $this->success($res);
    }

    /**
     * 添加分站账户数据
     * @param $website_id
     * @param string $account_type
     * @param $account_data
     * @param $from_type
     * @param $relate_tag
     * @param $remark
     * @return array
     */
    public function addWebsiteAccount($website_id, $account_type = 'account', $account_data, $from_type, $relate_tag, $remark)
    {

        $data = array(
            'site_id'      => $website_id,
            'account_no'   => date('YmdHi') . rand(1000, 9999),
            'account_type' => $account_type,
            'account_data' => $account_data,
            'from_type'    => $from_type,
            'type_name'    => $this->from_type[$from_type]['type_name'],
            'relate_tag'   => $relate_tag,
            'create_time'  => time(),
            'remark'       => $remark
        );

        $res = model('account')->add($data);

        model('website')->setInc([['site_id', '=', $website_id]], $account_type, $account_data);

        return $this->success($res);
    }

    /**
     * 分站账户统计
     */
    public function getWebsiteSum($condition)
    {
        $field = 'sum(account) as account,sum(account_withdraw) as account_withdraw,sum(account_shop) as account_shop,sum(account_order) as account_order';
        $res   = model('website')->getInfo($condition, $field);
        if ($res['account'] == null) {
            $res['account'] = '0.00';
        }
        if ($res['account_withdraw'] == null) {
            $res['account_withdraw'] = '0.00';
        }
        if ($res['account_shop'] == null) {
            $res['account_shop'] = '0.00';
        }
        if ($res['account_order'] == null) {
            $res['account_order'] = '0.00';
        }
        return $this->success($res);
    }

    /**
     * 获取分站列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getWebsiteList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('website')->getList($condition, $field, $order, '', '', '', $limit);

        return $this->success($list);
    }

    /**
     * 获取分站分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getWebsitePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('website')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 推广二维码
     * @param $site_id
     * @param string $type
     * @return array
     */
    public function qrcode($site_id, $type = "create")
    {
        $data = [
            'app_type'    => "all", // all为全部
            'type'        => $type, // 类型 create创建 get获取
            'data'        => [
            ],
            'page'        => '/pages/index/index/index',
            'qrcode_path' => 'upload/qrcode/index',
            'qrcode_name' => "index_qrcode_" . $site_id,
        ];

        event('Qrcode', $data, true);
        $app_type_list = config('app_type');

        $path = [];

        //获取站点信息
        $website_model = new WebsiteModel();
        $website_info  = $website_model->getWebSite([['site_id', '=', 0]], 'wap_domain');

        foreach ($app_type_list as $k => $v) {
            switch ($k) {
                case 'h5':
                    if (!empty($website_info['data']['wap_domain'])) {
                        $path[$k]['status'] = 1;
                        $path[$k]['url']    = $website_info['data']['wap_domain'] . $data['page'];
                        if (!empty($diy_view_info['site_id'])) {
                            $path[$k]['url'] .= '?site_id=' . $diy_view_info['site_id'];
                        }
                        $path[$k]['img'] = "upload/qrcode/index/index_qrcode_" . $site_id . "_" . $k . ".png";
                    } else {
                        $path[$k]['status']  = 2;
                        $path[$k]['message'] = '未配置手机端域名';
                    }
                    break;
                case 'weapp':
                    $config = new ConfigModel();
                    $res    = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'WEAPP_CONFIG']]);
                    if (!empty($res['data'])) {
                        if (empty($res['data']['value']['qrcode'])) {
                            $path[$k]['status']  = 2;
                            $path[$k]['message'] = '未配置微信小程序';
                        } else {
                            $path[$k]['status'] = 1;
                            $path[$k]['img']    = $res['data']['value']['qrcode'];
                        }

                    } else {
                        $path[$k]['status']  = 2;
                        $path[$k]['message'] = '未配置微信小程序';
                    }
                    break;

                case 'wechat':
                    $config = new ConfigModel();
                    $res    = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'WECHAT_CONFIG']]);
                    if (!empty($res['data'])) {
                        if (empty($res['data']['value']['qrcode'])) {
                            $path[$k]['status']  = 2;
                            $path[$k]['message'] = '未配置微信公众号';
                        } else {
                            $path[$k]['status'] = 1;
                            $path[$k]['img']    = $res['data']['value']['qrcode'];
                        }
                    } else {
                        $path[$k]['status']  = 2;
                        $path[$k]['message'] = '未配置微信公众号';
                    }
                    break;
            }

        }

        $return = [
            'path' => $path
        ];

        return $this->success($return);
    }
}
