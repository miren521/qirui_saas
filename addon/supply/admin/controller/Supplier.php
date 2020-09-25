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


namespace addon\supply\admin\controller;

use addon\supply\model\Config as ConfigModel;
use addon\supply\model\order\OrderCommon;
use app\admin\controller\BaseAdmin;
use addon\supply\model\Supplier as SupplierModel;
use addon\supply\model\SupplyCategory;

/**
 * 供应商管理
 */
class Supplier extends BaseAdmin
{
    /**
     * 供应商列表
     */
    public function index()
    {

        if (request()->isAjax()) {
            $supplier_model = new SupplierModel();
            $page_index     = input('page', 1);
            $page_size      = input('page_size', PAGE_LIST_ROWS);
            $search_text    = input('search_text', '');
            $condition      = [];
            if (!empty($search_text)) {
                $condition[] = ['title|desc|keywords|supplier_phone', 'LIKE', "%{$search_text}%"];
            }
            $res = $supplier_model->getSupplierPageList($condition, $page_index, $page_size, 'supplier_id DESC');
            return $res;
        }
        return $this->fetch('supplier/index');
    }

    /**
     * 添加供应商
     */
    public function add()
    {
        if (request()->isAjax()) {
            $supplier_model = new SupplierModel();
            $data           = [
                'title'            => input('title', ''),
                'category_id'      => input('category_id', ''),
                'category_name'    => input('category_name', ''),
                'logo'             => input('logo', ''),
                'desc'             => input('desc', ''),
                'keywords'         => input('keywords', ''),
                'supplier_address' => input('supplier_address', ''),
                'supplier_email'   => input('supplier_email', ''),
                'supplier_phone'   => input('supplier_phone', ''),
                'supplier_qq'      => input('supplier_qq', ''),
                'supplier_weixin'  => input('supplier_weixin', '')
            ];

            //个人信息
            $user_info = [
                'username' => input('username', ''),
                'password' => data_md5(input('password', '')),
            ];
            $res       = $supplier_model->addSupplier($data, $user_info);
            return $res;
        }
        // 主营行业
        $category_model = new SupplyCategory();
        $category_list  = $category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
        $this->assign('category_list', $category_list['data']);
        return $this->fetch('supplier/add');
    }

    /**
     * 修改供应商
     */
    public function edit()
    {
        $supplier_id    = input('supplier_id', 0);
        $condition      = [['supplier_id', '=', $supplier_id]];
        $supplier_model = new SupplierModel();
        if (request()->isAjax()) {
            $data = [
                'title'            => input('title', ''),
                'category_id'      => input('category_id', ''),
                'category_name'    => input('category_name', ''),
                'logo'             => input('logo', ''),
                'desc'             => input('desc', ''),
                'status'             => input('status', ''),
                'keywords'         => input('keywords', ''),
                'supplier_address' => input('supplier_address', ''),
                'supplier_email'   => input('supplier_email', ''),
                'supplier_phone'   => input('supplier_phone', ''),
                'supplier_qq'      => input('supplier_qq', ''),
                'supplier_weixin'  => input('supplier_weixin', '')
            ];
            $res  = $supplier_model->editSupplier($condition, $data);
            return $res;
        }
        //四级菜单
        $site_id = input('site_id', 0);
        $this->forthMenu(['site_id' => $site_id]);

        // 主营行业
        $category_model = new SupplyCategory();
        $category_list  = $category_model->getCategoryList([], 'category_id, category_name', 'sort asc');
        $this->assign('category_list', $category_list['data']);
        $supplier_info = $supplier_model->getSupplierInfo($condition);
        $this->assign('info', $supplier_info['data']);

        return $this->fetch('supplier/edit');
    }

    /**
     * 删除供应商
     * @return array
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $supplier_id = input('supplier_id', 0);
            if (empty($supplier_id)) {
                return error(-1, '参数错误！');
            }
            $supplier_model = new SupplierModel();
            $res            = $supplier_model->deleteSupplier($supplier_id);
            return $res;
        }
    }

    /**
     * 供应商配置
     */
    public function config()
    {
        $config_model = new ConfigModel();
        if (request()->isAjax()) {
            $data = [
                'fee'         => input('fee', 0),
                'remark'      => input('remark', ''),
                'period_type' => input('period_type', 1),
            ];
            $this->addLog("修改供应商配置");
            $res = $config_model->setSupplyConfig($data);
            return $res;
        } else {
            $copyright = $config_model->getSupplyConfig();
            $this->assign('config', $copyright['data']['value']);
            return $this->fetch('supplier/config');
        }
    }
    /**
     * 认证信息
     */
    public function cert()
    {
        $apply_model = new SupplierModel();
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
            return $apply_model->editSupplierCert($data, [['site_id', '=', $site_id]]);
        }
        //四级菜单
        $site_id = input('site_id', 0);
        $this->forthMenu(['site_id' => $site_id]);

        $cert_info = $apply_model->getSupplierCert([['site_id', '=', $site_id]]);
        $this->assign('cert_info', $cert_info['data']);

        return $this->fetch('supplier/cert');
    }

    /**
     * 结算信息
     */
    public function settlement()
    {
        $supply_model = new SupplierModel();
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

            if ($bank_type == 1) {
                $cert_data['settlement_bank_account_name'] = input('settlement_bank_account_name', 0);//结算银行开户名
                $cert_data['settlement_bank_account_number'] = input('settlement_bank_account_number', 0);//结算公司银行账号
            } elseif ($bank_type == 2) {
                $cert_data['settlement_bank_account_name'] = input('zfb_settlement_bank_account_name', 0);//结算银行开户名
                $cert_data['settlement_bank_account_number'] = input('zfb_settlement_bank_account_number', 0);//结算公司银行账号
            } else {
                $cert_data['settlement_bank_account_name'] = input('settlement_bank_account_name', 0);//结算银行开户名
                $cert_data['settlement_bank_account_number'] = input('settlement_bank_account_number', 0);//结算公司银行账号
            }

            return $supply_model->editSupplierCert($cert_data, [['site_id', '=', $site_id]]);
        } else {
            //四级菜单
            $site_id = input('site_id', 0);
            $this->forthMenu(['site_id' => $site_id]);

            //获取商家结算账户信息
            $cert_info = $supply_model->getSupplierCert([['site_id', '=', $site_id]]);
            $this->assign('cert_info', $cert_info['data']);

            return $this->fetch('supplier/settlement');
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
        $supply_model = new SupplierModel();
        $condition = [
            ['supplier_site_id', '=', $site_id]
        ];
        $account_info = $supply_model->getSupplierInfo($condition);

        $account = $account_info['data']['account'] - $account_info['data']['account_withdraw_apply'];
        $this->assign('account',number_format($account,2, '.' , ''));

        $this->assign('account_info', $account_info['data']);
        $this->assign('order_calc', 0);//待结算

        return $this->fetch('supplier/account_info');
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

            $list = $order_common->getOrderPageList($condition, $page, $page_size, $order, $field = 'order_id,order_no,order_type_name,order_status_name,order_money,supply_money,platform_money,is_settlement,create_time,refund_status,order_type');
            return $list;
        }
    }
}
