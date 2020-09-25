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


namespace addon\supply\supply\controller;

use addon\supply\model\Config;
use addon\supply\model\SupplyCategory;
use app\Controller;
use addon\shopwithdraw\model\Config as ShopWithdrawConfig;
use addon\supply\model\SupplyApply as SupplyApplyModel;
use addon\supply\model\Config as ConfigModel;
use app\model\system\Address as AddressModel;
use app\model\web\WebSite as WebsiteModel;
use app\model\system\User as userModel;
use think\facade\Cache;
use think\facade\Session;

/**
 * 供应商申请
 * @package addon\supply\supply\controller
 */
class Apply extends Controller
{
    protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'
    protected $app_module = "supply";

    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
        $this->replace = [
            'SUPPLY_CSS' => __ROOT__ . '/addon/supply/supply/view/public/css',
            'SUPPLY_JS'  => __ROOT__ . '/addon/supply/supply/view/public/js',
            'SUPPLY_IMG' => __ROOT__ . '/addon/supply/supply/view/public/img',
        ];
    }

    /*
     *  申请入驻首页
     * */
    public function index()
    {
        $this->assign("menu_info", ['title' => "供应商申请"]);
        $this->assign("supply_info", ['title' => "供货商端"]);
        //用户信息
        $userInfo = session($this->app_module);
        $this->assign('userInfo', $userInfo);

        //供应商主营行业
        $category_model = new SupplyCategory();
        $category       = $category_model->getCategoryList('', '*', 'sort asc', '');
        $this->assign('category', $category);

        //入驻协议
        $config_model = new ConfigModel();
        $agreement    = $config_model->getApplyAgreement();
        $this->assign('agreement', $agreement);

        //查询省级数据列表
        $address_model = new AddressModel();
        $list          = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
        $this->assign("province_list", $list["data"]);

        //获取申请完整信息
        $apply_model = new SupplyApplyModel();

        $condition[]     = ['uid', '=', $userInfo['uid']];
        $apply_info = $apply_model->getApplyDetail($condition);
        if ($apply_info['data'] == null) {//未填写申请信息
            //第一步
            $procedure = 1;
        } else {//已填写申请信息
            //判断审核状态
            if ($apply_info['data']['apply_state'] == 1) {
                $procedure = 2;//审核中
            } elseif ($apply_info['data']['apply_state'] == 2) {
                if ($apply_info['data']['paying_money_certificate'] != '') {
                    $procedure = 3;//财务凭据审核中
                } else {
                    $procedure = 6;//财务凭据提交中
                }
            } elseif ($apply_info['data']['apply_state'] == 3) {
                $procedure = 5;//入驻成功
            } elseif ($apply_info['data']['apply_state'] == -2) {
                $procedure = 7;//财务审核失败
            } else {
                $procedure = 4;//审核失败
            }
        }
        $this->assign('procedure', $procedure);
        $this->assign('apply_info', $apply_info["data"]);

        //收款信息
        $receivable_config = $config_model->getSystemBankAccount();
        $this->assign('receivable_config', $receivable_config);

        //平台配置信息
        $website_model = new WebsiteModel();
        $website_info  = $website_model->getWebSite([['site_id', '=', 0]], 'web_phone');
        $this->assign('website_info', $website_info['data']);

        $this->assign("support_transfer_type", $this->getTransferType());

        // 服务费
        $config_model = new Config();
        $config       = $config_model->getSupplyConfig();
        $this->assign("service_fee", $config['data']['value']['fee']);

        return $this->fetch("apply/index", [], $this->replace);
    }

    /*
     *  申请入住
     * */
    public function apply()
    {
        //用户信息
        $userInfo = session($this->app_module);
        //申请信息
        $apply_data = [
            'site_id'       => input('site_id', 0),
            'website_id'    => input('website_id', 0),
            'uid'           => $userInfo['uid'],//用户ID
            'username'      => $userInfo['user_info']['username'],//账户
            'supplier_name' => input("supplier_name", ''),//申请供应商名称
            'apply_state'   => 1,//审核状态（待审核）
            'apply_year'    => input("apply_year", ''),//入驻年长
            'category_name' => input("category_name", ''),//供应商分类名称
            'category_id'   => input("category_id", ''),//供应商分类id
        ];

        //认证信息
        $cert_type = input("cert_type", '');//申请类型

        if ($cert_type == 1) {//个人
            $cert_data = [
                'cert_type'                      => $cert_type,
                'contacts_name'                  => input("contacts_name", ''),//联系人姓名
                'contacts_mobile'                => input("contacts_mobile", ''),//联系人手机
                'contacts_card_no'               => input("contacts_card_no", ''),//联系人身份证
                'contacts_card_electronic_1'     => input("contacts_card_electronic_1", ''),//申请人手持身份证电子版
                'contacts_card_electronic_2'     => input("contacts_card_electronic_2", ''),//申请人身份证正面
                'contacts_card_electronic_3'     => input("contacts_card_electronic_3", ''),//申请人身份证反面
                'bank_account_name'              => input("bank_account_name", ''),//银行开户名
                'bank_account_number'            => input("bank_account_number", ''),//公司银行账号
                'bank_name'                      => input("bank_name", ''),//联系人姓名
                'bank_address'                   => input("bank_address", ''),//开户银行所在地
                'bank_code'                      => input("bank_code", ''),//支行联行号
                'bank_type'                      => input("bank_type", ''),//结算账户类型
                'settlement_bank_account_name'   => input('settlement_bank_account_name', ''),
                'settlement_bank_account_number' => input('settlement_bank_account_number', ''),
                'settlement_bank_name'           => input("settlement_bank_name", ''),//结算开户银行支行名称
                'settlement_bank_address'        => input("settlement_bank_address", '')//结算开户银行所在地
            ];
        } else {//公司
            $cert_data = [
                'cert_type'                               => $cert_type,
                'company_name'                            => input("company_name", ''),//公司名称
                'company_province_id'                     => input("province_id", ''),//公司所在省
                'company_city_id'                         => input("city_id", ''),//公司所在市
                'company_district_id'                     => input("district_id", ''),//公司所在区/县
                'company_address'                         => input("company_address", ''),//公司地址
                'company_full_address'                    => input("company_full_address", ''),//公司完整地址
                'business_licence_number'                 => input("business_licence_number", ''),//统一社会信用码
                'business_licence_number_electronic'      => input("business_licence_number_electronic", ''),//营业执照电子版
                'business_sphere'                         => input("business_sphere", ''),//法定经营范围
                'taxpayer_id'                             => input("taxpayer_id", ''),//纳税人识别号
                'tax_registration_certificate'            => input("tax_registration_certificate", ''),//税务登记证号
                'tax_registration_certificate_electronic' => input("tax_registration_certificate_electronic", ''),//税务登记证号电子版
                'contacts_name'                           => input("contacts_name", ''),//联系人姓名
                'contacts_mobile'                         => input("contacts_mobile", ''),//联系人手机
                'contacts_card_no'                        => input("contacts_card_no", ''),//联系人身份证
                'contacts_card_electronic_1'              => input("contacts_card_electronic_1", ''),//申请人手持身份证电子版
                'contacts_card_electronic_2'              => input("contacts_card_electronic_2", ''),//申请人身份证正面
                'contacts_card_electronic_3'              => input("contacts_card_electronic_3", ''),//申请人身份证反面
                'bank_account_name'                       => input("bank_account_name", ''),//银行开户名
                'bank_account_number'                     => input("bank_account_number", ''),//公司银行账号
                'bank_name'                               => input("bank_name", ''),//联系人姓名
                'bank_address'                            => input("bank_address", ''),//开户银行所在地
                'bank_code'                               => input("bank_code", ''),//支行联行号
                'bank_type'                               => input("bank_type", ''),//结算账户类型
                'settlement_bank_account_name'            => input('settlement_bank_account_name', ''),
                'settlement_bank_account_number'          => input('settlement_bank_account_number', ''),
                'settlement_bank_name'                    => input("settlement_bank_name", ''),//结算开户银行支行名称
                'settlement_bank_address'                 => input("settlement_bank_address", '')//结算开户银行所在地
            ];
        }
        $model  = new SupplyApplyModel();
        $result = $model->apply($apply_data, $cert_data);
        return $result;
    }

    /**
     * 判断结算类型
     */
    public function getTransferType()
    {

        $support_type = [];
        if (addon_is_exit("shopwithdraw")) {
            $config_model  = new ShopWithdrawConfig();
            $config_result = $config_model->getConfig();
            $config        = $config_result["data"];
            if ($config["is_use"]) {
                $support_type = explode(",", $config['value']["transfer_type"]);
            } else {
                $support_type = ["alipay", "bank"];
            }
        } else {
            $support_type = ["alipay", "bank"];
        }
        return $support_type;
    }


    /**
     * 判断供应商名称是否存在
     */
    public function supplierNameExist()
    {
        $supplier_name = input('supplier_name', '');

        $model     = new SupplyApplyModel();
        $condition = [['supplier_name', '=', $supplier_name]];
        $res       = $model->getApplyInfo($condition, 'apply_id');
        return $res['data'];
    }

    /*
     * 获取申请金额
     * */
    public function getApplyMoney()
    {
        $apply_year  = input("apply_year", '');//入驻年长
        $category_id = input("category_id", '');//主营行业
        $supplier_id = input("supplier_id", 0); //供应商ID

        $model  = new SupplyApplyModel();
        $result = $model->getApplyMoney($apply_year, $category_id);

        if (!empty($supplier_id)) {
            $info = model('supplier')->getInfo([['supplier_site_id', '=', $supplier_id]], 'bond');
            if ($info['bond'] > 0) {
                $result['code']['paying_deposit'] = 0;
                $result['code']['paying_amount']  = $result['code']['paying_apply'];
            }
        }
        return $result;
    }

    /*
     *   提交支付凭证
     **/
    public function editApply()
    {
        if (request()->isAjax()) {
            //用户信息
            $userInfo = session($this->app_module);
            //申请信息
            $apply_data = [
                'paying_money_certificate'         => input("paying_money_certificate", ''),// 付款凭证
                'paying_money_certificate_explain' => input("paying_money_certificate_explain", ''),// 付款凭证说明
                'apply_state'                      => 2
            ];

            $model       = new SupplyApplyModel();
            $condition[] = ['uid', '=', $userInfo['uid']];
            $result      = $model->editApply($apply_data, $condition);
            return $result;
        }
    }

    /**
     *  模拟登陆
     */
    public function simulatedLogin()
    {
        if (request()->isAjax()) {
            $username   = input('username', '');
            $user_model = new UserModel;
            $result     = $user_model->simulatedLogin($username, $this->app_module);
            return $result;
        }
    }


    /**
     * 供应商绑定openid 二维码
     */
    public function supplyBindQrcode()
    {
        $key = 'bing_supply_openid' . md5(uniqid(null, true));
        $url = addon_url("wechat://api/auth/supplyBindOpenid", ["key" => $key]);
        Session::set("bing_supply_openid", $key);
        Cache::tag("bing_supply_openid")->set($key, [], 600);
        \extend\QRcode::png($url, false, '', 4, 1);
    }

    /**
     * 验证供应商绑定情况(成功返回openid)
     */
    public function checkSupplyBind()
    {
        $key  = Session::get("bing_supply_openid");
        $data = Cache::get($key);
        $apply_model = new SupplyApplyModel();
        if (!isset($data)) {
            return $apply_model->error(["is_expire" => 1], "二维码已过期");
        }

        if (empty($data)) {
            return $apply_model->error(["is_expire" => 0], "二维码还没有被扫描");
        }

        return $apply_model->success($data);
    }
}
