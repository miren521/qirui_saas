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

use app\model\shop\Shop as ShopModel;
use app\model\shop\ShopAccount;
use app\model\shop\ShopApply as ShopApplyModel;
use app\model\shop\ShopCategory as ShopCategoryModel;
use app\model\shop\ShopExport;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\member\Member as MemberModel;
use app\model\order\OrderCommon;
use app\model\shop\ShopDeposit;
use app\model\web\WebSite;
use phpoffice\phpexcel\Classes\PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\Writer\Excel2007;
use think\facade\Cache;
use think\facade\Session;

/**
 * 商家管理 控制器
 */
class Shop extends BaseAdmin
{

    /******************************* 商家列表及相关操作 ***************************/

    /**
     * 商家列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $category_id = input('category_id', 0);
            $group_id = input('group_id', 0);
            $shop_status = input('shop_status', '');
			$cert_id = input('cert_id', '');
            $is_own = input('is_own', '');
			$start_time = input("start_time", '');
			$end_time = input("end_time", '');

            $condition = [];
            if($search_text){
                $condition[] = ['site_name', 'like', '%' . $search_text . '%'];
            }
            //商家分类
            if ($category_id != 0) {
                $condition[] = ['category_id', '=', $category_id];
            }
            //店铺等级
            if ($group_id != 0) {
                $condition[] = ['group_id', '=', $group_id];
            }
            //商家状态
            if ($shop_status != '') {
                $condition[] = ['shop_status', '=', $shop_status];
            }
            if($cert_id){
                switch($cert_id){
                    case 1: //未认证
                        $condition[] = ['cert_id', '=', 0];
                        break;
                    case 2: //已认证
                        $condition[] = ['cert_id', '>', 0];
                        break;
                }
            }
            if($is_own != '')
            {
                $condition[] = ['is_own', '=', $is_own];
            }
			if(!empty($start_time) && empty($end_time)){
				$condition[] = ['expire_time', '>=', strtotime($start_time)];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition[] = ["expire_time", "<=", strtotime($end_time)];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition[] = ["expire_time", ">=", strtotime($start_time)];
				$condition[] = ["expire_time", "<=", strtotime($end_time)];
			}
			
            $order = 'site_id desc';
            $field = '*';

            $shop_model = new ShopModel();
            // 商家名称site_name 商家分类category_name 店铺等级group_name 是否自营is_own 1：是 0：否 商家状态shop_status 1：正常 0：锁定 有效期
            return $shop_model->getShopPageList($condition, $page, $page_size, $order, $field);

        } else {
            //商家主营行业
            $shop_category_model = new ShopCategoryModel();
            $shop_category_list = $shop_category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
            $this->assign('shop_category_list', $shop_category_list['data']);

            //商家主营分组
            $shop_group_model = new ShopGroupModel();
            $shop_group_list = $shop_group_model->getGroupList([['is_own','=',0]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_group_list', $shop_group_list['data']);

            $is_addon_city = addon_is_exit('city');
            $this->assign('is_addon_city',$is_addon_city);

            if($is_addon_city == 1){
                $website_model = new WebSite();
                $website_list = $website_model->getWebsiteList([],'site_id,site_area_name');
                $this->assign('website_list',$website_list['data']);
            }
            return $this->fetch('shop/lists');
        }
    }

    /**
     * 商家详情
     */
    public function shopDetail()
    {
        $site_id = input('site_id', 0);
        $shop_model = new ShopModel();

        //商家信息
        $shop_info = $shop_model->getShopInfo([['site_id', '=', $site_id]]);
        $this->assign('shop_info', $shop_info);

        //认证信息
        $cert_info = $shop_model->getShopCert([['site_id', '=', $site_id]]);
        $this->assign('cert_info', $cert_info);

        return $this->fetch('shop/shop_detail');
    }

    /**
     * 商家添加
     */
    public function addShop()
    {
        if (request()->isAjax()) {
            //店铺信息
            $shop_data = [
                'site_name' => input('site_name', ''),//店铺名称
                'category_id' => input('category_id', 0),//分类id
                'category_name' => input('category_name', ''),//分类名称
                'group_id' => input('group_id', 0),//组id
                'group_name' => input('group_name', ''),//组名称
                'is_own' => input('is_own', 0),//是否自营
                'member_id' => input('member_id', 0),//关联会员 用于前台登陆和相关商家操作
                'year' => input('year', 0),//入驻时长 1 2 3 4 5 下拉选择
            ];
            //认证信息
            $cert_data = [
                /* 申请类型 */
                'cert_type' => input('cert_type', 1),//申请类型1.个人店铺 2.企业店铺
                /* 公司信息 只有公司类型有 */
                'company_name' => input('company_name', ''),//公司名称
                'company_province_id' => input('company_province_id', ''),//公司所在省
                'company_city_id' => input('company_city_id', ''),//公司所在市
                'company_district_id' => input('company_district_id', ''),//公司所在区/县
                'company_address' => input('company_address', ''),//公司地址
                'company_full_address' => input('company_full_address', ''),//公司完整地址
                /* 联系人手机号身份证 公司、个人类型都有 */
                'contacts_name' => input('contacts_name', ''),//联系人姓名
                'contacts_mobile' => input('contacts_mobile', ''),//联系人手机
                'contacts_card_no' => input('contacts_card_no', ''),//联系人身份证
                'contacts_card_electronic_1' => input('contacts_card_electronic_1', ''),//申请人手持身份证电子版
                'contacts_card_electronic_2' => input('contacts_card_electronic_2', ''),//申请人身份证正面
                'contacts_card_electronic_3' => input('contacts_card_electronic_3', ''),//申请人身份证反面
                /* 营业执照 税务 只有公司类型有 */
                'business_licence_number' => input('business_licence_number', ''),//统一社会信用码 input
                'business_licence_number_electronic' => input('business_licence_number_electronic', ''),//营业执照电子版
                'business_sphere' => input('business_sphere', ''),//法定经营范围 textarea
                'tax_registration_certificate' => input('tax_registration_certificate', ''),//税务登记证号
                'tax_registration_certificate_electronic' => input('tax_registration_certificate_electronic', ''),//税务登记证号电子版
                /* 对公账户信息 只有公司类型有 */
                'bank_account_name' => input('bank_account_name', ''),//银行开户名
                'bank_account_number' => input('bank_account_number', ''),//公司银行账号
                'bank_name' => input('bank_name', ''),//开户银行支行名称
                'bank_address' => input('bank_address', ''),//开户银行所在地 用三级地址选择省市区 传递拼在一起的名字 如山西省太原市小店区
                /* 结算信息 公司、个人类型都有 */
                'bank_type' => input('bank_type', 1),//结算账户类型  1银行卡 2 支付宝
                'settlement_bank_account_name' => input('settlement_bank_account_name', 0),//结算银行开户名
                'settlement_bank_account_number' => input('settlement_bank_account_number', 0),//结算公司银行账号
                'settlement_bank_name' => input('settlement_bank_name', 0),//结算开户银行支行名称
                'settlement_bank_address' => input('settlement_bank_address', 0),//结算开户银行所在地 用三级地址选择省市区 传递拼在一起的名字 如山西省太原市小店区
            ];
            //个人信息
            $user_info = [
                'username' => input('username', ''),
                'password' => data_md5(input('password', '')),
            ];

            $shop_model = new ShopModel();
            $this->addLog("添加商家:" . $shop_data['site_name']);
            return $shop_model->addShop($shop_data, $cert_data, $user_info);

        } else {
            //商家主营行业
            $shop_category_model = new ShopCategoryModel();
            $shop_category_list = $shop_category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
            $this->assign('shop_category_list', $shop_category_list['data']);

            //商家主营行业
            $shop_group_model = new ShopGroupModel();
            $shop_group_list = $shop_group_model->getGroupList([['is_own','=',0]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_group_list', $shop_group_list['data']);
            //商家自营等级
            $shop_own_group = $shop_group_model->getGroupList([['is_own','=',1]], 'group_id,is_own,group_name,fee,remark', 'fee asc');
            $this->assign('shop_own_group', $shop_own_group['data']);
            return $this->fetch('shop/add_shop');
        }
    }

    /**
     * 基本信息
     */
    public function basicInfo()
    {
	    $shop_model = new ShopModel();
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $data = [
                'site_name' => input('site_name', ''),//商家名称
                'expire_time' => input('expire_time') ? strtotime(input('expire_time')) : 0,//到期时间（0表示无限期）
                //待定
                'is_own' => input('is_own', 0),//是否自营
                'category_id' => input('category_id', 0),//店铺分类id
                'category_name' => input('category_name', ''),//店铺类别名称
                //待定
                'group_id' => input('group_id', 0),//分组id
                'group_name' => input('group_name', ''),//分组名称
                'member_id' => input('member_id', 0),//关联前台会员id
                'shop_status' => input('shop_status', ''),//店铺经营状态（0.关闭，1正常）
                'sort' => input('sort', 0),//排序号
                'logo' => input('logo', ''),//店铺logo
                'avatar' => input('avatar', ''),//店铺头像（大图）
                'banner' => input('banner', ''),//店铺条幅
                'seo_keywords' => input('seo_keywords', ''),//店铺关键字
                'seo_description' => input('seo_description', ''),//店铺简介
                'telephone' => input('telephone', ''),//联系电话
                'is_recommend' => input('is_recommend', 0),//是否推荐 1是 0否
                'shop_qtian' => input('shop_qtian', 0),//七天退货 1是 0否
                'shop_zhping' => input('shop_zhping', 0),//正品保障 1是 0否
                'shop_erxiaoshi' => input('shop_erxiaoshi', 0),//两小时发货 1是 0否
                'shop_tuihuo' => input('shop_tuihuo', 0),//退货承诺 1是 0否
                'shop_shiyong' => input('shop_shiyong', 0),//试用中心 1是 0否
                'shop_shiti' => input('shop_shiti', 0),//实体验证 1是 0否
                'shop_xiaoxie' => input('shop_xiaoxie', 0),//消协保证 1是 0否
            ];
            return $shop_model->editShop($data, [['site_id', '=', $site_id]]);
        } else {
            //商家信息
            $site_id = input('site_id', 22);
            $shop_info = $shop_model->getShopInfo([['site_id', '=', $site_id]]);
            $this->assign('shop_info', $shop_info['data']);

            //关联前台会员信息
            if (!empty($shop_info['data']['member_id'])) {
                $member_id = $shop_info['data']['member_id'];
                $member_model = new MemberModel();
                $member_info = $member_model->getMemberInfo([['member_id', '=', $member_id]]);
                $this->assign('member_info', $member_info['data']);
            }

            //商家主营行业
            $shop_category_model = new ShopCategoryModel();
            $shop_category_list = $shop_category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
            $this->assign('shop_category_list', $shop_category_list['data']);

            //商家开店套餐（非自营）
            $shop_group_model = new ShopGroupModel();
            $shop_group_list = $shop_group_model->getGroupList([['is_own','=',0]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_group_list', $shop_group_list['data']);

            //商家开店套餐（自营）
            $shop_group_model = new ShopGroupModel();
            $shop_own_group_list = $shop_group_model->getGroupList([['is_own','=',1]], 'group_id,is_own,group_name,fee,remark', 'is_own asc,fee asc');
            $this->assign('shop_own_group_list', $shop_own_group_list['data']);
            //四级菜单
            $this->forthMenu(['site_id' => $site_id]);

            return $this->fetch('shop/basic_info');
        }
    }

    /**
     * 认证信息
     */
    public function certInfo()
    {
	    $shop_model = new ShopModel();
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            //认证信息
            $data = [
                /* 公司信息 只有公司类型有 */
                'company_name' => input('company_name', ''),//公司名称
                'company_province_id' => input('company_province_id', 0),//公司所在省
                'company_city_id' => input('company_city_id', 0),//公司所在市
                'company_district_id' => input('company_district_id', 0),//公司所在区/县
                'company_address' => input('company_address', ''),//公司地址
                /* 联系人手机号身份证 公司、个人类型都有 */
                'contacts_name' => input('contacts_name', ''),//联系人姓名
                'contacts_mobile' => input('contacts_mobile', ''),//联系人手机
                'contacts_card_no' => input('contacts_card_no', ''),//联系人身份证
                'contacts_card_electronic_1' => input('contacts_card_electronic_1', ''),//申请人手持身份证电子版
                'contacts_card_electronic_2' => input('contacts_card_electronic_2', ''),//申请人身份证正面
                'contacts_card_electronic_3' => input('contacts_card_electronic_3', ''),//申请人身份证反面
                /* 营业执照 税务 只有公司类型有 */
                'business_licence_number' => input('business_licence_number', ''),//统一社会信用码 input
                'business_licence_number_electronic' => input('business_licence_number_electronic', ''),//营业执照电子版
                'business_sphere' => input('business_sphere', ''),//法定经营范围 textarea
                'tax_registration_certificate' => input('tax_registration_certificate', ''),//税务登记证号
                'tax_registration_certificate_electronic' => input('tax_registration_certificate_electronic', ''),//税务登记证号电子版
                /* 对公账户信息 只有公司类型有 */
                'bank_account_name' => input('bank_account_name', ''),//银行开户名
                'bank_account_number' => input('bank_account_number', ''),//公司银行账号
                'bank_name' => input('bank_name', ''),//开户银行支行名称
                'bank_address' => input('bank_address', ''),//开户银行所在地 用三级地址选择省市区 传递拼在一起的名字 如山西省太原市小店区
            ];
            return $shop_model->editShopCert($data, [['site_id', '=', $site_id]]);
        } else {
            $site_id = input('site_id', 0);
            $cert_info = $shop_model->getShopCert([['site_id', '=', $site_id]]);
            $this->assign('cert_info', $cert_info['data']);

            //四级菜单
            $this->forthMenu(['site_id' => $site_id]);

            return $this->fetch('shop/cert_info');
        }
    }

    /**
     * 结算信息
     */
    public function settlementInfo()
    {
	    $shop_model = new ShopModel();
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $bank_type = input('bank_type', 0);

            //结算账户信息
            $cert_data = [
                /* 结算信息 公司、个人类型都有 */
                'bank_type' => input('bank_type', 0),//结算账户类型  1银行卡 2 支付宝
                'settlement_bank_name' => input('settlement_bank_name', 0),//结算开户银行支行名称
                'settlement_bank_address' => input('settlement_bank_address', 0),//结算开户银行所在地 用三级地址选择省市区 传递拼在一起的名字 如山西省太原市小店区
            ];

            if($bank_type == 1){
                $cert_data['settlement_bank_account_name'] = input('settlement_bank_account_name', 0);//结算银行开户名
                $cert_data['settlement_bank_account_number'] = input('settlement_bank_account_number', 0);//结算公司银行账号
            }elseif ($bank_type == 2){
                $cert_data['settlement_bank_account_name'] = input('zfb_settlement_bank_account_name', 0);//结算银行开户名
                $cert_data['settlement_bank_account_number'] = input('zfb_settlement_bank_account_number', 0);//结算公司银行账号
            }else{
                $cert_data['settlement_bank_account_name'] = input('settlement_bank_account_name', 0);//结算银行开户名
                $cert_data['settlement_bank_account_number'] = input('settlement_bank_account_number', 0);//结算公司银行账号
            }

            return $shop_model->editShopCert($cert_data, [['site_id', '=', $site_id]]);
        } else {
            $site_id = input('site_id', 0);

            //获取商家结算账户信息
            $cert_info = $shop_model->getShopCert([['site_id', '=', $site_id]]);
            $this->assign('cert_info', $cert_info['data']);

            //四级菜单
            $this->forthMenu(['site_id' => $site_id]);

            return $this->fetch('shop/settlement_info');
        }
    }

    /**
     * 账户信息
     */
    public function accountInfo()
    {
        $site_id = input('site_id', 0);
        //四级菜单
        $this->forthMenu(['site_id' => $site_id]);
        $shop_model = new ShopModel();
        $condition = [
            ['site_id', '=', $site_id]
        ];
        $account_info = $shop_model->getShopInfo($condition);

        $account = $account_info['data']['account'] - $account_info['data']['account_withdraw_apply'];
        $this->assign('account',number_format($account,2, '.' , ''));

        $this->assign('account_info', $account_info['data']);
        $this->assign('order_calc', 0);//待结算

        return $this->fetch('shop/account_info');
    }


    /**
     * 获取待结算列表
     */
    public function getOrderCalc()
    {

        if (request()->isAjax()) {
            $order_common = new OrderCommon();
            $site_id = input('site_id', 0);
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "create_time desc");
            $is_refund = input("is_refund", '');
			$order_no = input("order_no", '');

            $condition = array(
                ['site_id', "=", $site_id],
                ['is_settlement', "=", 0],
                ['order_status', "not in", '0,-1'],
            );
			if($order_no){
				$condition[] = ['order_no', 'like', '%'. $order_no .'%'];
			}
            if ($is_refund !== '') {
                $condition[] = ['refund_status', '=', $is_refund];
            }

            $list = $order_common->getOrderPageList($condition, $page, $page_size, $order, $field = 'order_id,order_no,order_type_name,order_status_name,order_money,shop_money,platform_money,is_settlement,create_time,refund_status,order_type');
            return $list;
        }
    }


    /**
     * 获取账户流水
     */
    public function getShopAccount()
    {
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $account_model = new ShopAccount();

            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);

            $condition[] = ['site_id','=',$site_id];
            $type = input('type','');//收支类型（1收入  2支出）
            if(!empty($type)){
                switch($type){
                    case 1:
                        $condition[] = ['account_data','>',0];
                        break;
                    case 2:
                        $condition[] = ['account_data','<',0];
                        break;
                }
            }
            $start_time = input('start_time','');
            $end_time = input('end_time','');
            if(!empty($start_time) && empty($end_time)){
                $condition[] = ['create_time','>=',$start_time];
            }elseif(empty($start_time) && !empty($end_time)){
                $condition[] = ['create_time','<=',$end_time];
            }elseif(!empty($start_time) && !empty($end_time)){
                $condition[] = ['create_time','between',[$start_time,$end_time]];
            }

            return  $account_model->getAccountPageList($condition,$page,$page_size,'id desc');
        }
    }


    /**
     * 获取提现记录
     */
    public function getShopWithdraw()
    {
        if (request()->isAjax()) {
            $site_id = input('site_id', 0);
            $account_model = new ShopAccount();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "id desc");
            $search_text = input("search_text", "");

            $condition = array(
                ['site_id', "=", $site_id],
            );
            if (!empty($search_text)) {
                $condition[] = ['withdraw_no|settlement_bank_account_name|mobile', 'like', '%' . $search_text . '%'];
            }
            $list = $account_model->getShopWithdrawPageList($condition, $page, $page_size, $order);
            return $list;
        }
    }

    /**
     * 获取保证金记录
     */
    public function getShopDeposit()
    {
        if (request()->isAjax()) {
            $shop_deposit_model = new ShopDeposit();
            $site_id = input('site_id', 0);
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "id desc");
            $search_text = input("search_text", "");

            $condition = array(
                ['site_id', "=", $site_id],
            );
            if (!empty($search_text)) {
                $condition[] = ['pay_no|pay_account_name', 'like', '%' . $search_text . '%'];
            }
            $list = $shop_deposit_model->getShopDepositPageList($condition, $page, $page_size, $order);
            return $list;
        }
    }

    /**
     * 商家锁定
     */
    public function lockShop()
    {

    }

    /**
     * 商家解锁
     */
    public function unlockShop()
    {

    }

    /**
     *  商家导出
     */
    public function exportShop()
    {
        $search_text = input('search_text', '');
        $category_id = input('category_id', 0);
        $group_id = input('group_id', 0);
        $shop_status = input('shop_status', '');

        $condition = [];
        if ($search_text) {
            $condition[] = ['site_name', 'like', '%' . $search_text . '%'];
        }

        //商家分类
        if ($category_id != 0) {
            $condition[] = ['category_id', '=', $category_id];
        }
        //店铺等级
        if ($group_id != 0) {
            $condition[] = ['group_id', '=', $group_id];
        }
        //商家状态
        if ($shop_status != '') {
            $condition[] = ['shop_status', '=', $shop_status];
        }
        $order = 's.site_id desc';
        $shop_model = new ShopModel();

        $shop = $shop_model->getShopCertList($condition, $order);

        $header_arr = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
        );

        $shop_export_model = new ShopExport();
        //导出所有字段
        $field = array_merge($shop_export_model->shop_field, $shop_export_model->shop_cert_field);

        //接收需要展示的字段
        $input_field = input('field', implode(',',array_keys($field)));
        $input_field = explode(',', $input_field);

        //处理数据
        if (!empty($shop['data'])) {
            $shop_list = $shop_export_model->handleData($shop['data'], $input_field);
        }

        $count = count($input_field);
        // 实例化excel
        $phpExcel = new \PHPExcel();

        $phpExcel->getProperties()->setTitle("店铺信息");
        $phpExcel->getProperties()->setSubject("店铺信息");
        //单独添加列名称
        $phpExcel->setActiveSheetIndex(0);

        for ($i = 0; $i < $count; $i++) {
            $phpExcel->getActiveSheet()->setCellValue($header_arr[$i] . '1', $field[$input_field[$i]]);
        }

        if (!empty($shop_list)) {
            foreach ($shop_list as $k => $v) {
                $start = $k + 2;
                for ($i = 0; $i < $count; $i++) {

                    $phpExcel->getActiveSheet()->setCellValue($header_arr[$i] . $start, $v[$input_field[$i]]);
                }
            }
        }

        // 重命名工作sheet
        $phpExcel->getActiveSheet()->setTitle('店铺信息');
        // 设置第一个sheet为工作的sheet
        $phpExcel->setActiveSheetIndex(0);
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        $file = date('Y年m月d日-店铺信息表', time()) . '.xlsx';
        $objWriter->save($file);

        header("Content-type:application/octet-stream");

        $filename = basename($file);
        header("Content-Disposition:attachment;filename = " . $filename);
        header("Accept-ranges:bytes");
        header("Accept-length:" . filesize($file));
        readfile($file);
        unlink($file);
        exit;

    }

    /**
     * 导出字段
     * @return array
     */
    public function getPrintingField()
    {
        $model = new ShopExport();
        $field = array_merge($model->shop_field, $model->shop_cert_field);
        return success('1', '', $field);
    }

    /**
     * 店铺绑定openid 二维码
     */
    public function shopBindQrcode()
    {
        $key = 'bing_shop_openid_' . md5(uniqid(null, true));
        $url = addon_url("wechat://api/auth/shopBindOpenid", ["key" => $key]);
        Session::set("bing_shop_openid", $key);
        Cache::tag("bing_shop_openid")->set($key, [], 600);
        return \extend\QRcode::png($url, false, '', 4, 1);
    }

    /**
     * 验证店铺绑定情况(成功返回openid)
     */
    public function checkShopBind()
    {
        $key              = Session::get("bing_shop_openid");
        $data             = Cache::get($key);
        $shop_apply_model = new ShopApplyModel();
        if (!isset($data)) {
            return $shop_apply_model->error(["is_expire" => 1], "二维码已过期");
        }

        if (empty($data)) {
            return $shop_apply_model->error(["is_expire" => 0], "二维码还没有被扫描");
        }

        return $shop_apply_model->success($data);
    }
}