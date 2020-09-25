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


namespace addon\city\city\controller;

use app\Controller;
use app\model\system\Address as AddressModel;

/**
 * 地址查询 控制器 不控权限 不入菜单
 */
class Address extends Controller
{
    /**
     * 获取省列表
     */
    public function getProvince()
    {
        $address_model = new AddressModel();
        $condition = [
            ['pid', '=', 0],
            ['level', '=', 1],
            ['status', '=', 1],
        ];
        $province_list = $address_model->getAreaList($condition, '*', 'sort asc');
        return $province_list['data'];
    }

    /**
     * 获取城市列表
     */
    public function getCity()
    {
        $address_model = new AddressModel();
        $province_id = request()->post('province_id', 0);
        $condition = [
            ['pid', '=', $province_id],
            ['level', '=', 2],
            ['status', '=', 1],
        ];
        $city_list = $address_model->getAreaList($condition, '*', 'sort asc');
        return $city_list['data'];
    }

    /**
     * 获取区域列表
     */
    public function getDistrict()
    {
        $address_model = new AddressModel();
        $city_id = request()->post('city_id', 0);
        $condition = [
            ['pid', '=', $city_id],
            ['level', '=', 3],
            ['status', '=', 1],
        ];
        $district_list = $address_model->getAreaList($condition, '*', 'sort asc');
        return $district_list['data'];
    }

    /**
     * 获取街道列表
     */
    public function getStreet()
    {
        $address_model = new AddressModel();
        $district_id = request()->post('district_id', 0);
        $condition = [
            ['pid', '=', $district_id],
            ['level', '=', 4],
            ['status', '=', 1],
        ];
        $street_list = $address_model->getAreaList($condition, '*', 'sort asc');
        return $street_list['data'];
    }

    /**
     * 通过ajax得到运费模板的地区数据
     */
    public function getAreaList()
    {
        $address_model = new AddressModel();
        $level = input('level', 1);
        $pid = input("pid", 0);
        $condition = array(
            "level" => $level,
            "pid" => $pid
        );
        $list = $address_model->getAreaList($condition, "id, pid, name, level", "id asc");
        return $list;
    }

    /**
     * 获取地理位置id
     */
    public function getGeographicId()
    {
        $address_model = new AddressModel();
        $address = request()->post("address", ",,");
        $address_array = explode(",", $address);
        $province = $address_array[0];
        $city = $address_array[1];
        $district = $address_array[2];
        $subdistrict = $address_array[3];
        $province_list = $address_model->getAreaList([ "name" => $province, "level" => 1 ], "id", '');
        $province_id = !empty($province_list["data"]) ? $province_list["data"][0]["id"] : 0;
        $city_list = ($province_id > 0) && !empty($city) ? $address_model->getAreaList([ "name" => $city, "level" => 2, "pid" => $province_id ], "id", '') : [];
        $city_id = !empty($city_list["data"]) ? $city_list["data"][0]["id"] : 0;
        $district_list = !empty($district) && $city_id > 0 && $province_id > 0 ? $address_model->getAreaList([ "name" => $district, "level" => 3, "pid" => $city_id ], "id", '') : [];
        $district_id = !empty($district_list["data"]) ? $district_list["data"][0]["id"] : 0;

        $subdistrict_list = !empty($subdistrict) && $city_id > 0 && $province_id > 0 && $district_id > 0 ? $address_model->getAreaList([ "name" => $subdistrict, "level" => 4, "pid" => $district_id ], "id", '') : [];
        $subdistrict_id = !empty($subdistrict_list["data"]) ? $subdistrict_list["data"][0]["id"] : 0;
        return [ "province_id" => $province_id, "city_id" => $city_id, "district_id" => $district_id, "subdistrict_id" => $subdistrict_id ];
    }

}