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

use app\model\store\Store as StoreModel;
use app\model\system\Address as AddressModel;
use addon\supply\model\Supplier as SupplierModel;
use addon\supply\model\SupplyDeposit;

/**
 * 门店首页
 * @author Administrator
 *
 */
class Supply extends BaseSupply
{
    /**
     * 店铺设置
     * @return mixed
     */
    public function config()
    {
        $supply_model = new SupplierModel();
        $condition[] = ["supplier_site_id", "=", $this->supply_id];
        if (request()->isAjax()) {
            $logo            = input("logo", '');//店铺logo
//            $avatar          = input("avatar", '');//店铺头像（大图）
//            $banner          = input("banner", '');//店铺条幅
//            $seo_keywords    = input("seo_keywords", '');//店铺关键字
            $desc = input("desc", '');//店铺简介
            $qq              = input("qq", '');//qq
//            $ww              = input("ww", '');//ww
            $telephone       = input("telephone", '');//联系电话
            $data            = [
                "logo"            => $logo,
//                "avatar"          => $avatar,
//                "banner"          => $banner,
//                "seo_keywords"    => $seo_keywords,
                "supplier_qq"              => $qq,
//                "ww"              => $ww,
                "supplier_phone"       => $telephone,
                "desc" => $desc,
//                'status'     => 1
            ];
            $res             = $supply_model->editSupplier($condition, $data);
            return $res;
        }
        $supply_info_result = $supply_model->getSupplierInfo($condition);
        $supply_info        = $supply_info_result["data"];
        $this->assign("data", $supply_info);
        return $this->fetch("supply/config");
    }

    /**
     * 联系方式
     * @return mixed
     */
    public function contact()
    {
        $supply_model = new SupplierModel();
        $condition[]  = ["supplier_site_id", "=", $this->supply_id];
        if (request()->isAjax()) {
            $province         = input("province", 0);//省级地址
            $province_name    = input("province_name", '');//省级地址
            $city             = input("city");//市级地址
            $city_name        = input("city_name", '');//市级地址
            $district         = input("district", 0);//县级地址
            $district_name    = input("district_name", '');//县级地址
            $community        = input("community", 0);//乡镇地址
            $community_name   = input("community_name", '');//乡镇地址
            $address          = input("address", 0);//详细地址
            $full_address     = input("full_address", 0);//完整地址
            $qq               = input("qq", '');//qq号
            $supplier_contact = input("supplier_contact", '');//联系人姓名
            $mobile           = input("mobile", '');//联系人手机号

            $data = [
                "province"         => $province,
                "province_name"    => $province_name,
                "city"             => $city,
                "city_name"        => $city_name,
                "district"         => $district,
                "district_name"    => $district_name,
                "community"        => $community,
                "community_name"   => $community_name,
                "address"          => $address,
                "full_address"     => $full_address,
                "supplier_qq"               => $qq,
                "supplier_phone"   => $mobile,
                'status'      => 1,
                "supplier_contact" => $supplier_contact,
            ];
            $res = $supply_model->editSupplier($condition, $data);
            return $res;
        }
        $supply_info_result = $supply_model->getSupplierInfo($condition);
        $supply_info        = $supply_info_result["data"];
        $this->assign("data", $supply_info);

        //查询省级数据列表
        $address_model = new AddressModel();
        $list          = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
        $this->assign("province_list", $list["data"]);
        return $this->fetch("supply/contact");
    }

    /**
     * 认证信息
     */
    public function cert()
    {
        $supply_model       = new SupplierModel();
        $condition        = array(
            ["site_id", "=", $this->supply_id]
        );
        $cert_info_result = $supply_model->getSupplierCert($condition);
        $cert_info        = $cert_info_result["data"];
        $this->assign("cert_info", $cert_info);
        return $this->fetch("supply/cert");
    }


    /**
     * 账户信息
     * @return mixed
     */
    public function account()
    {
        $supply_model         = new SupplierModel();

        $config_model = new Config();
        //获取转账设置
        $withdraw_config = $config_model->getSupplyWithdrawConfig();


        $condition        = array(
            ["supplier_site_id", "=", $this->supply_id]
        );
        $supply_info_result = $supply_model->getSupplierInfo($condition, 'account, account_withdraw, bond');
        $supply_info        = $supply_info_result["data"];

        //获取店家结算账户信息
        $cert_condition = array(
            ["site_id", "=", $this->supply_id]
        );
        $shop_cert_result = $supply_model->getSupplierCert($cert_condition, 'bank_type, settlement_bank_account_name, settlement_bank_account_number, settlement_bank_name, settlement_bank_address');

        $this->assign("account", $supply_info['account']);//账户余额
        $this->assign("account_withdraw", $supply_info['account_withdraw']); //已提现
        $this->assign('order_calc', 0);//待结算
        $this->assign('bond', $supply_info['bond']);//保证金
        $this->assign('withdraw_config', $withdraw_config['data']['value']);//商家转账设置
        $this->assign('cert_info', $shop_cert_result['data']);//店家结算账户信息
        return $this->fetch("supply/account");
    }

}