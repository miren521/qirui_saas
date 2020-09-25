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


namespace app\shop\controller;

use app\model\system\SiteAddress as SiteAddressModel;

/**
 * 站点地址
 * Class Express
 * @package app\shop\controller
 */
class Siteaddress extends BaseShop
{


    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();

    }
    /**
     * 商家地址列表
     */
    public function getSiteAddressList()
    {
        $site_address_model = new SiteAddressModel();
        if (request()->isAjax()) {
            $site_id = $this->site_id;
            $res = $site_address_model->getSiteAddressList([['site_id', '=', $site_id]]);
            return $res;
        }
    }
    /**
     * 商家地址列表
     */
    public function getSiteAddressInfo()
    {
        $id = input('id', 0);
        $site_address_model = new SiteAddressModel();
        if (request()->isAjax()) {
            $site_id = $this->site_id;
            $res = $site_address_model->getSiteAddressInfo([['site_id', '=', $site_id], ['id', '=', $id]]);
            return $res;
        }
    }

    /**
     * 商家地址添加
     */
    public function add()
    {

        $site_address_model = new SiteAddressModel();

        if (request()->isAjax()) {
            $name = input('name', '');//收货人
            $mobile = input('mobile', '');//手机号
            $area_code = input('area_code', '');//邮政编号
            $telephone = input('telephone', '');//固定电话
            $province = input('province_id', 0);//省
            $city = input('city_id', 0);//城市
            $district = input('district_id', '');//区县
            $community = input('community_id', '');//乡镇
            $province_name = input('province_name', '');//省
            $city_name = input('city_name', '');//城市
            $district_name = input('district_name', '');//区县
            $community_name = input('community_name', '');//乡镇
            $address = input('address', '');//详细地址
            $full_address = input('full_address', '');//完整地址

            $data = array(
                'name' => $name,
                'mobile' => $mobile,
                'area_code' => $area_code,
                'telephone' => $telephone,
                'province_id' => $province,
                'city_id' => $city,
                'district_id' => $district,
                'community_id' => $community,
                'province_name' => $province_name,
                'city_name' => $city_name,
                'district_name' => $district_name,
                'community_name' => $community_name,
                'address' => $address,
                'full_address' => $full_address,
                'site_id' => $this->site_id,
            );
            $res = $site_address_model->addSiteAddress($data);
            return $res;
        } else {
            return $this->fetch('order/address_add');
        }
    }

    /**
     * 商家地址修改
     */
    public function edit()
    {
        $id = input('id', 0);
        $site_address_model = new SiteAddressModel();
        $condition = array(
            ['site_id', '=', $this->site_id],
            ['id', '=', $id],
        );
        if (request()->isAjax()) {

            $name = input('name', '');//收货人
            $mobile = input('mobile', '');//手机号
            $area_code = input('area_code', '');//邮政编号
            $telephone = input('telephone', '');//固定电话
            $province = input('province_id', 0);//省
            $city = input('city_id', 0);//城市
            $district = input('district_id', '');//区县
            $community = input('community_id', '');//乡镇
            $province_name = input('province_name', '');//省
            $city_name = input('city_name', '');//城市
            $district_name = input('district_name', '');//区县
            $community_name = input('community_name', '');//乡镇
            $address = input('address', '');//详细地址
            $full_address = input('full_address', '');//完整地址

            $data = array(
                'name' => $name,
                'mobile' => $mobile,
                'area_code' => $area_code,
                'telephone' => $telephone,
                'province_id' => $province,
                'city_id' => $city,
                'district_id' => $district,
                'community_id' => $community,
                'province_name' => $province_name,
                'city_name' => $city_name,
                'district_name' => $district_name,
                'community_name' => $community_name,
                'address' => $address,
                'full_address' => $full_address,
            );
            $res = $site_address_model->editSiteAddress($data, $condition);
            return $res;
        }
    }

    /**
     * 删除商家地址
     */
    public function delete()
    {

        $id = input('id', 0);
        $site_address_model = new SiteAddressModel();
        $res = $site_address_model->deleteSiteAddress($id, $this->site_id);
        return $res;
    }

    /**
     * 设置默认地址
     */
    public function setDefault()
    {
        $id = input('id', 0);
        $site_address_model = new SiteAddressModel();
        $res = $site_address_model->setSiteAddressDefault($id, $this->site_id);
        return $res;
    }

}