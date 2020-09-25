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


use app\model\shop\ShopApply;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\shop\ShopCategory as ShopCategoryModel;
use app\model\shop\ShopApply as ShopApplyModel;
use app\model\shop\Config as ConfigModel;
use app\model\system\Address as AddressModel;
use app\model\web\WebSite as WebsiteModel;
use app\model\shop\ShopAccount as ShopaccountModel;
use app\model\system\Promotion as PrmotionModel;
use addon\shopwithdraw\model\Config as ShopWithdrawConfig;
use app\model\system\User as userModel;

class Apply extends BaseApi
{

    /**
     * 申请入驻首页
     * @return false|string
     */
    public function index()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        //店铺主营行业
        $shop_category_model = new ShopCategoryModel();
        $shop_category = $shop_category_model->getCategoryList('', '*', 'sort asc', '');
        $data['shop_category'] = $shop_category;

        //入驻协议
        $config_model = new ConfigModel();
        $shop_apply_agreement = $config_model->getShopApplyAgreement();
        $data['shop_apply_agreement'] = $shop_apply_agreement;

        //查询省级数据列表
        $address_model = new AddressModel();
        $list = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
        $data['province_list'] = $list["data"];

        //获取申请完整信息
        $shop_apply_model = new ShopApplyModel();

        $condition[] = ['uid', '=', $this->uid ];
        $shop_apply_info = $shop_apply_model->getApplyDetail($condition);
        if($shop_apply_info['data'] == null){//未填写申请信息
            //第一步
            $procedure = 1;
        }else{//已填写申请信息
            //判断审核状态
            if($shop_apply_info['data']['apply_state'] == 1){
                $procedure = 2;//审核中
            }elseif ($shop_apply_info['data']['apply_state'] == 2){
                if($shop_apply_info['data']['paying_money_certificate'] != ''){
                    $procedure = 3;//财务凭据审核中
                }else{
                    $procedure = 6;//财务凭据提交中
                }
            }elseif ($shop_apply_info['data']['apply_state'] == 3){
                $procedure = 5;//入驻成功
            }elseif ($shop_apply_info['data']['apply_state'] == -2){
                $procedure = 7;//财务审核失败
            }else{
                $procedure = 4;//审核失败
            }
        }
        $data['procedure'] = $procedure;
        $data['shop_apply_info'] = $shop_apply_info["data"];

        //收款信息
        $receivable_config = $config_model->getSystemBankAccount();
        $data['receivable_config'] = $receivable_config['data'];

        //平台配置信息
        $website_model = new WebsiteModel();
        $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'web_phone');
        $data['website_info'] = $website_info['data'];

        //快捷入驻
        $account_model = new ShopaccountModel();
        $config_info = $account_model->getShopWithdrawConfig();
        if(empty($config_info['data'])){
            $id_experience = 0;
        }else{
            $id_experience = $config_info['data']['value']['id_experience'];
        }
        $data['id_experience'] = $id_experience;

        $promotion_model = new PrmotionModel();
        //插件
        $promotions = $promotion_model->getPromotions();
        $promotions = $promotions['shop'];
        //店铺等级
        $shop_group_model = new ShopGroupModel();
        $shop_group = $shop_group_model->getGroupList([['is_own','=',0]],'*','fee asc');
        $shop_group = $shop_group['data'];

        foreach($shop_group as $k=>$v){
            $addon_array = !empty($v['addon_array']) ? explode(',', $v['addon_array']) : [];

            foreach($promotions as $key => &$promotion) {
                if (!empty($promotion['is_developing'])) {
                    unset($promotions[$key]);
                    continue;
                }
                $promotion['is_checked'] = 0;
                if (in_array($promotion['name'], $addon_array)) {
                    $promotion['is_checked'] = 1;
                }
                $shop_group[$k]['promotion'][] = $promotion;
            }
            array_multisort(array_column($shop_group[$k]['promotion'],'is_checked'),SORT_DESC,$shop_group[$k]['promotion']);
        }
        $data['group_info'] = $shop_group;

        //城市分站
        $is_city_addon = addon_is_exit('city');
        $data['is_city'] = $is_city_addon;

        if($is_city_addon == 1){//存在
            //获取城市分站信息
            $city = $website_model->getWebsiteList([['site_id ','>=',0],['status','=',1]], 'site_id,site_area_id,site_area_name');
            $data['web_city'] = $city['data'];

        }else{//不存在

            $data['web_city'] = [];
        }
        $data['support_transfer_type'] = $this->getTransferType();

        return $this->response($this->success($data));

    }

    /*
     *  申请入住
     * */
    public function apply()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        //申请信息
        $apply_data = [
            'site_id' => isset($this->params['site_id']) ? $this->params['site_id'] : 0,
            'website_id' => isset($this->params['website_id']) ? $this->params['website_id'] : 0,
            'uid' => $this->uid,//用户ID
            'username' => $this->user_info['username'],//账户
            'shop_name' => isset($this->params['shop_name']) ? $this->params['shop_name'] : '',//申请店铺名称
            'apply_state' => 1,//审核状态（待审核）
            'apply_year' => isset($this->params['apply_year']) ? $this->params['apply_year'] : '',//入驻年长
            'category_name' => isset($this->params['category_name']) ? $this->params['category_name'] : '',//店铺分类名称
            'category_id' => isset($this->params['category_id']) ? $this->params['category_id'] : '',//店铺分类id
            'group_name' => isset($this->params['group_name']) ? $this->params['group_name'] : '',//开店套餐名称
            'group_id' => isset($this->params['group_id']) ? $this->params['group_id'] : ''// 开店套餐ID
        ];

        //认证信息
        $cert_type = input("cert_type", '');//申请类型

        if($cert_type == 1){//个人
            $cert_data = [
                'cert_type' => $cert_type,
                'contacts_name' => isset($this->params['contacts_name']) ? $this->params['contacts_name'] : '',//联系人姓名
                'contacts_mobile' => isset($this->params['contacts_mobile']) ? $this->params['contacts_mobile'] : '',//联系人手机
                'contacts_card_no' => isset($this->params['contacts_card_no']) ? $this->params['contacts_card_no'] : '',//联系人身份证
                'contacts_card_electronic_2' => isset($this->params['contacts_card_electronic_2']) ? $this->params['contacts_card_electronic_2'] : '',//申请人手持身份证正面
                'contacts_card_electronic_3' => isset($this->params['contacts_card_electronic_3']) ? $this->params['contacts_card_electronic_3'] : '',//申请人手持身份证反面
                'bank_account_name' => isset($this->params['bank_account_name']) ? $this->params['bank_account_name'] : '',//银行开户名
                'bank_account_number' => isset($this->params['bank_account_number']) ? $this->params['bank_account_number'] : '',//公司银行账号
                'bank_name' => isset($this->params['bank_name']) ? $this->params['bank_name'] : '',//联系人姓名
                'bank_address' => isset($this->params['bank_address']) ? $this->params['bank_address'] : '',//开户银行所在地
                'bank_code' => isset($this->params['bank_code']) ? $this->params['bank_code'] : '',//支行联行号
                'bank_type' => isset($this->params['bank_type']) ? $this->params['bank_type'] : '',//结算账户类型
                'settlement_bank_account_name' => isset($this->params['settlement_bank_account_name']) ? $this->params['settlement_bank_account_name'] : '',
                'settlement_bank_account_number' => isset($this->params['settlement_bank_account_number']) ? $this->params['settlement_bank_account_number'] : '',
                'settlement_bank_name' => isset($this->params['settlement_bank_name']) ? $this->params['settlement_bank_name'] : '',//结算开户银行支行名称
                'settlement_bank_address' => isset($this->params['settlement_bank_address']) ? $this->params['settlement_bank_address'] : ''//结算开户银行所在地
            ];
        }else{//公司
            $cert_data = [
                'cert_type' => $cert_type,
                'company_name' => isset($this->params['company_name']) ? $this->params['company_name'] : '',//公司名称
                'company_province_id' => isset($this->params['company_province_id']) ? $this->params['company_province_id'] : '',//公司所在省
                'company_city_id' => isset($this->params['company_city_id']) ? $this->params['company_city_id'] : '',//公司所在市
                'company_district_id' => isset($this->params['company_district_id']) ? $this->params['company_district_id'] : '',//公司所在区/县
                'company_address' => isset($this->params['company_address']) ? $this->params['company_address'] : '',//公司地址
                'company_full_address' => isset($this->params['company_full_address']) ? $this->params['company_full_address'] : '',//公司完整地址
                'business_licence_number' => isset($this->params['business_licence_number']) ? $this->params['business_licence_number'] : '',//统一社会信用码
                'business_licence_number_electronic' => isset($this->params['business_licence_number_electronic']) ? $this->params['business_licence_number_electronic'] : '',//营业执照电子版
                'business_sphere' => isset($this->params['business_sphere']) ? $this->params['business_sphere'] : '',//法定经营范围
                'taxpayer_id' => isset($this->params['taxpayer_id']) ? $this->params['taxpayer_id'] : '',//纳税人识别号
                'tax_registration_certificate' => isset($this->params['tax_registration_certificate']) ? $this->params['tax_registration_certificate'] : '',//税务登记证号
                'tax_registration_certificate_electronic' => isset($this->params['tax_registration_certificate_electronic']) ? $this->params['tax_registration_certificate_electronic'] : '',//税务登记证号电子版
                'contacts_name' => isset($this->params['contacts_name']) ? $this->params['contacts_name'] : '',//联系人姓名
                'contacts_mobile' => isset($this->params['contacts_mobile']) ? $this->params['contacts_mobile'] : '',//联系人手机
                'contacts_card_no' => isset($this->params['contacts_card_no']) ? $this->params['contacts_card_no'] : '',//联系人身份证
                'contacts_card_electronic_2' => isset($this->params['contacts_card_electronic_2']) ? $this->params['contacts_card_electronic_2'] : '',//申请人手持身份证正面
                'contacts_card_electronic_3' => isset($this->params['contacts_card_electronic_3']) ? $this->params['contacts_card_electronic_3'] : '',//申请人手持身份证反面
                'bank_account_name' => isset($this->params['bank_account_name']) ? $this->params['bank_account_name'] : '',//银行开户名
                'bank_account_number' => isset($this->params['bank_account_number']) ? $this->params['bank_account_number'] : '',//公司银行账号
                'bank_name' => isset($this->params['bank_name']) ? $this->params['bank_name'] : '',//联系人姓名
                'bank_address' => isset($this->params['bank_address']) ? $this->params['bank_address'] : '',//开户银行所在地
                'bank_code' => isset($this->params['bank_code']) ? $this->params['bank_code'] : '',//支行联行号
                'bank_type' => isset($this->params['bank_type']) ? $this->params['bank_type'] : '',//结算账户类型
                'settlement_bank_account_name' => isset($this->params['settlement_bank_account_name']) ? $this->params['settlement_bank_account_name'] : '',
                'settlement_bank_account_number' => isset($this->params['settlement_bank_account_number']) ? $this->params['settlement_bank_account_number'] : '',
                'settlement_bank_name' => isset($this->params['settlement_bank_name']) ? $this->params['settlement_bank_name'] : '',//结算开户银行支行名称
                'settlement_bank_address' => isset($this->params['settlement_bank_address']) ? $this->params['settlement_bank_address'] : ''//结算开户银行所在地
            ];
        }
        $model = new ShopApplyModel();
        $result = $model->apply($apply_data,$cert_data);
        return $this->response($result);
    }

    /**
     * 判断店铺名称是否存在
     */
    public function shopNameExist()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $shop_name = isset($this->params['shop_name']) ? $this->params['shop_name'] : '';

        $model = new ShopApplyModel();
        $res = $model->shopNameExist($shop_name);
        return $this->response($res);
    }

    /*
     * 获取申请金额
     * */
    public function getApplyMoney()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $apply_year = isset($this->params['apply_year']) ? $this->params['apply_year'] : '';//入驻年长
        $category_id = isset($this->params['category_id']) ? $this->params['category_id'] : '';//店铺分类id
        $group_id = isset($this->params['group_id']) ? $this->params['group_id'] : '';//店铺等级ID

        $model = new ShopApplyModel();
        $result = $model->getApplyMoney($apply_year, $group_id, $category_id);
        return $this->response($result);
    }

    /*
    *   提交支付凭证
    **/
    public function editApply()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        //申请信息
        $apply_data = [
            'paying_money_certificate' => isset($this->params['paying_money_certificate']) ? $this->params['paying_money_certificate'] : '',// 付款凭证
            'paying_money_certificate_explain' => isset($this->params['paying_money_certificate_explain']) ? $this->params['paying_money_certificate_explain'] : '',// 付款凭证说明
            'apply_state' => 2
        ];

        $model = new ShopApplyModel();
        $condition[] = ['uid', '=', $this->uid];
        $result = $model->editApply($apply_data, $condition);
        return $this->response($result);
    }


    /**
     * 体验入驻
     */
    public function experienceApply()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $shop_data = [
            'site_name' => isset($this->params['site_name']) ? $this->params['site_name'] : '',    //店铺名称
            'category_id' => isset($this->params['category_id']) ? $this->params['category_id'] : '',      //主营行业id
            'category_name' => isset($this->params['category_name']) ? $this->params['category_name'] : '',  //主营行业名称
            'website_id' => isset($this->params['website_id']) ? $this->params['website_id'] : '',
        ];

        $model = new ShopApply();
        $res =  $model->experienceApply($shop_data,$this->user_info);
        return $this->response($res);

    }



    /**
     * 判断结算类型
     */
    public function getTransferType(){

        $support_type = [];
        if(addon_is_exit("shopwithdraw")){
            $config_model = new ShopWithdrawConfig();
            $config_result = $config_model->getConfig();
            $config = $config_result["data"];
            if($config["is_use"]){
                $support_type = explode(",", $config['value']["transfer_type"]);
            }else{
                $support_type = ["alipay", "bank"];
            }
        }else{
            $support_type = ["alipay", "bank"];
        }
        return $support_type;
    }

    /**
     *  模拟登陆
     */
    public function simulatedLogin()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);

        $user_model = new UserModel;
        $res = $user_model->simulatedLogin($this->user_info['username'], 'shop','shopapi');

        if ($res['code'] >= 0) {
            $token = $this->createToken($res['data']);
            return $this->response($this->success([ 'token' => $token ,'site_id' => $res['data']['site_id']]));
        }
        return $this->response($res);
    }
}