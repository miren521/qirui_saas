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

use app\model\BaseModel;
use app\model\system\Config as ConfigModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;
use app\model\web\WebSite as WebsiteModel;

/**
 * 自定义模板
 */
class DiyView extends BaseModel
{
    /**
     * 系统页面，格式：端口，页面，关键词
     * @var array
     */
    private $page = [
        'admin' => [
            'port'           => 'admin',
            'index'          => [
                'name' => 'DIYVIEW_INDEX',
            ],
            'goods_category' => [
                'name' => 'DIYVIEW_GOODS_CATEGORY'
            ]
        ],
        'shop'  => [
            'port'           => 'shop',
            'index'          => [
                'name' => 'DIYVIEW_SHOP_INDEX',
            ],
            'goods_category' => [
                'name' => 'DIYVIEW_SHOP_GOODS_CATEGORY'
            ]
        ],
    ];

    /**
     * 获取系统页面
     * @return array
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * 获取自定义模板组件集合
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getDiyViewUtilList($condition = [], $field = 'id,name,title,type,controller,value,addon_name,support_diy_view,max_count', $order = 'sort asc', $limit = null)
    {
        $res = model('diy_view_util')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($res);
    }

    /**
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $alias
     * @param array $join
     * @param string $group
     * @param null $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getDiyLinkList($condition = [], $field = 'lk.id,lk.addon_name,nsa.title as addon_title,lk.name,lk.title,lk.web_url,lk.wap_url,lk.icon,nsa.icon as addon_icon', $order = 'nsa.id asc', $alias = 'lk', $join = [['addon nsa', 'lk.addon_name=nsa.name', 'left']], $group = '', $limit = null)
    {
        $res = model('link')->getList($condition, $field, $order, $alias, $join, $group, $limit);
        return $this->success($res);
    }

    /**
     * @param $data
     * @return array
     */
    public function addSiteDiyView($data)
    {
        $res = model('site_diy_view')->add($data);
        if ($res) {
            Cache::tag("site_diy_view")->clear();
            return $this->success($res);
        } else {
            return $this->error($res);
        }
    }

    /**
     * 添加多条自定义模板数据
     * @param $data
     * @return array
     */
    public function addSiteDiyViewList($data)
    {
        $res = model('site_diy_view')->addList($data);
        if ($res) {
            Cache::tag("site_diy_view")->clear();
            return $this->success($res);
        } else {
            return $this->error($res);
        }
    }

    /**
     * 修改自定义模板
     * @param $data
     * @param $condition
     * @return array
     * @throws DbException
     */
    public function editSiteDiyView($data, $condition)
    {
        $res = model('site_diy_view')->update($data, $condition);
        if ($res) {
            Cache::tag("site_diy_view")->clear();
            return $this->success($res);
        } else {
            return $this->error($res);
        }
    }

    /**
     * 删除站点微页面
     * @param array $condition
     * @return array
     * @throws DbException
     */
    public function deleteSiteDiyView($condition = [])
    {
        $res = model('site_diy_view')->delete($condition);
        if ($res) {
            Cache::tag("site_diy_view")->clear();
            return $this->success($res);
        } else {
            return $this->error($res);
        }
    }

    /**
     * 获取自定义模板分页数据集合
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getSiteDiyViewPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = 'sdv.*,ndva.addon_name as addon_name_temp')
    {
        $data  = json_encode([$condition, $field, $order, $page, $page_size]);
        $cache = Cache::get("site_diy_view_getSiteDiyViewPageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $alias = "sdv";
        $join  = [
            [
                'diy_view_temp ndva',
                'sdv.name=ndva.name',
                'left'
            ]
        ];

        $res = model('site_diy_view')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        Cache::tag("site_diy_view")->set("site_diy_view_getSiteDiyViewPageList_" . $data, $res);
        return $this->success($res);
    }

    /**
     * 获取自定义模板信息
     * @param array $condition
     * @param string $field
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getSiteDiyViewInfo($condition = [], $field = 'id,site_id,name,title,value,type')
    {
        $data  = json_encode($condition);
        $cache = Cache::get("site_diy_view_getSiteDiyViewInfo_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }

        $info = model('site_diy_view')->getInfo($condition, $field);

        Cache::tag("site_diy_view")->set("diy_view_getSiteDiyViewInfo_" . $data, $info);
        return $this->success($info);
    }

    /**
     * 获取自定义模板详细信息
     * @param array $condition
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getSiteDiyViewDetail($condition = [])
    {
        $data  = json_encode($condition);
        $cache = Cache::get("site_diy_view_getSiteDiyViewDetail_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $alias = 'sdv';
        $join  = [
            [
                'diy_view_temp dvt',
                'sdv.name=dvt.name',
                'left'
            ]
        ];
        $field = 'sdv.id,sdv.site_id,sdv.name,sdv.title,sdv.value,sdv.type,dvt.addon_name';

        $info = model('site_diy_view')->getInfo($condition, $field, $alias, $join);

        Cache::tag("site_diy_view")->set("diy_view_getSiteDiyViewDetail_" . $data, $info);
        return $this->success($info);
    }

    /**
     * 组件分类
     * @param $type
     * @return mixed
     */
    public function getTypeName($type)
    {
        $arr = [
            'SYSTEM' => '系统组件',
            'ADDON'  => '营销插件',
            'OTHER'  => '其他插件',
        ];
        return $arr[$type];
    }

    /**
     * 获取平台端的底部导航配置
     * @return array
     */
    public function getBottomNavConfig()
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'DIY_VIEW_BOTTOM_NAV_CONFIG_ADMIN']]);
        return $res;
    }

    /**
     * 设置平台端的底部导航配置
     * @param $data
     * @return array
     */
    public function setBottomNavConfig($data)
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '自定义底部导航', 1, [['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'DIY_VIEW_BOTTOM_NAV_CONFIG_ADMIN']]);
        return $res;
    }

    /**
     * 获取平台端的底部导航配置
     * @param $site_id
     * @return array
     */
    public function getShopBottomNavConfig($site_id)
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'DIY_VIEW_SHOP_BOTTOM_NAV_CONFIG_ADMIN_' . $site_id]]);
        return $res;
    }

    /**
     * 设置平台端的底部导航配置
     * @param $data
     * @param $site_id
     * @return array
     */
    public function setShopBottomNavConfig($data, $site_id)
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '店铺端自定义底部导航', 1, [['site_id', '=', $site_id], ['app_module', '=', 'shop'], ['config_key', '=', 'DIY_VIEW_SHOP_BOTTOM_NAV_CONFIG_ADMIN_' . $site_id]]);
        return $res;
    }

    /**
     * 推广二维码
     * @param $condition
     * @param string $type
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function qrcode($condition, $type = "create")
    {
        $diy_view_info = $this->getSiteDiyViewInfo($condition, 'site_id,name');
        $page          = $this->getPage();
        $diy_view_info = $diy_view_info['data'];
        $data          = [
            'app_type'    => "all", // all为全部
            'type'        => $type, // 类型 create创建 get获取
            'data'        => [],
            'page'        => '/otherpages/diy/diy/diy',
            'qrcode_path' => 'upload/qrcode/diy',
            'qrcode_name' => "diy_qrcode_" . $diy_view_info['name'],
        ];

        // 平台主页
        if ($diy_view_info['name'] == $page['admin']['index']['name']) {
            $data['page'] = '/pages/index/index/index';
        } elseif ($diy_view_info['name'] == $page['shop']['index']['name']) {
            // 店铺主页
            $data['page'] = '/otherpages/shop/index/index';
            $data['data'] = [
                'site_id' => $diy_view_info['site_id']
            ];
        } else {
            $data['data'] = [
                "name" => $diy_view_info['name']
            ];
        }

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
                        // 平台主页
                        if ($diy_view_info['name'] == $page['admin']['index']['name']) {
                            $path[$k]['url'] = $website_info['data']['wap_domain'] . $data['page'];
                            if (!empty($diy_view_info['site_id'])) {
                                $path[$k]['url'] .= '?site_id=' . $diy_view_info['site_id'];
                            }
                        } elseif ($diy_view_info['name'] == $page['shop']['index']['name']) {
                            // 店铺主页
                            $path[$k]['url'] = $website_info['data']['wap_domain'] . $data['page'] . '?site_id=' . $diy_view_info['site_id'];
                        } else {
                            //自定义
                            $path[$k]['url'] = $website_info['data']['wap_domain'] . $data['page'] . '?name=' . $diy_view_info['name'];
                            if (!empty($diy_view_info['site_id'])) {
                                $path[$k]['url'] .= '&site_id=' . $diy_view_info['site_id'];
                            }
                        }
                        $path[$k]['img'] = "upload/qrcode/diy/diy_qrcode_" . $diy_view_info['name'] . "_" . $k . ".png";
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
