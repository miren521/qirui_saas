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

use app\model\express\ExpressCompany;
use app\model\express\ExpressTemplate;
use app\model\system\Address as AddressModel;

/**
 * 配送
 * Class Express
 * @package app\shop\controller
 */
class Express extends BaseShop
{


    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();

    }
    /**
     * 物流公司
     * @return mixed
     */
    public function expressCompany()
    {
        $express_company_model = new ExpressCompany();
        //平台公用的物流公司
        $company_list_result = $express_company_model->getExpressCompanyList([]);
        $company_list = $company_list_result["data"];
        $company_list = array_column($company_list, null, "company_id");
        //店铺物流公司
        $company_shop_list_result = $express_company_model->getExpressCompanyShopList([["site_id", "=", $this->site_id]]);
        $temp_company_shop_list = $company_shop_list_result["data"];
        $company_shop_list = [];
        //删除已有的公用物流公司
        if(!empty($temp_company_shop_list)){
            foreach($temp_company_shop_list as $k => $v){
                //店铺物流公司存在的话,删除掉公共物流公司中对应的数据
                if(!empty($company_list[$v["company_id"]])){
                    $temp_item = $company_list[$v["company_id"]];
                    $temp_item["id"] = $v["id"];
                    $company_shop_list[] = $temp_item;
                    unset($company_list[$v["company_id"]]);
                }
            }
        }
        $this->assign("company_list", $company_list);//平台公用的物流公司
        $this->assign("company_shop_list", $company_shop_list);//店铺物流公司
        //店铺配置的物流公司
        return $this->fetch("express/express_company");
    }


    public function addExpressCompanyShop() {
        if (request()->isAjax()) {
            $site_id = $this->site_id;
            $company_id = input('company_id', 0);
            $express_company_model = new ExpressCompany();
            if (empty($company_id)) {
                return $express_company_model->error('', '参数错误！');
            }
            $add_data = [
                'site_id' => $site_id,
                'company_id' => $company_id
            ];
            $re = $express_company_model->addExpressCompanyShop($add_data);
            return $re;
        }
    }

    /**
     * 取消物流公司
     * @return \multitype
     */
    public function closeCompany(){
        $company_id = input("company_id", 0);
        if(request()->isAjax()){
            $express_company_model = new ExpressCompany();
            $condition = array(
                ["site_id", "=", $this->site_id],
                ["company_id", "=", $company_id],
            );
            $result = $express_company_model->deleteExpressCompanyShop($condition);
            return $result;
        }
    }
    /**
     * 编辑打印模板
     */
    public function editPrintTemplate(){
        $company_id = input("company_id", 0);
        $id = input("id", 0);
        $condition = array(
            ["site_id", "=", $this->site_id],
            ["company_id", "=", $company_id],
            ["id", "=", $id]
        );
        $express_company_model = new ExpressCompany();
        if(request()->isAjax()){
            $data = array(
                'content_json' => input('content_json', '[]'),//打印内容
                'background_image' => input('background_image', ''),//打印背景图
                'font_size' => input('font_size', 14),//打印字体大小 单位px
                'width' => input('width', 0),//显示尺寸宽度 px
                'height' => input('height', 0),//显示尺寸高度 px
            );
            if($id == 0){
                $data["site_id"] = $this->site_id;
                $data["company_id"] = $company_id;
                $result = $express_company_model->addExpressCompanyShop($data);
            }else{
                $result = $express_company_model->editExpressCompanyShop($data, $condition);
            }
            return $result;
        }else{
            $company_shop_info_result = $express_company_model->getExpressCompanyShopInfo($condition);
            $company_shop_info = $company_shop_info_result["data"];
            $this->assign("info", $company_shop_info);
            //打印项
            $express_company_model = new ExpressCompany();
            $print_item_list = $express_company_model->getPrintItemList();
            $this->assign('print_item_list',$print_item_list);
            $this->assign("company_id", $company_id);
            $this->assign("id", $id);
            return $this->fetch("express/edit_print_template");
        }
    }

    /**
     * 运费模板
     * @return mixed
     */
    public function template(){
        if(request()->isAjax()){
            $express_template_model = new ExpressTemplate();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $order = input("order", "create_time desc");
            $keyword = input("keyword", '');
            $condition = array(
                ['site_id', "=",$this->site_id],
            );
            //关键字查询
            if(!empty($keyword)){
                $condition[] = ["template_name", "like", "%".$keyword."%"];
            }
            $result = $express_template_model->getExpressTemplatePageList($condition, $page, $page_size, $order);
            return $result;
        }else{
            return $this->fetch("express/template");
        }
    }


    /**
     * 添加运费模板
     * @return mixed
     */
    public function addTemplate(){
        if(request()->isAjax()){
            $express_template_model = new ExpressTemplate();
            $fee_type = input("fee_type", '');//运费计算方式1.重量2体积3按件
            $template_name = input("template_name", '');
            $json = input("json", "");
            $is_default = input('is_default', 0);
            $surplus_area_ids = input('surplus_area_ids', '');
            if(empty($json))
                return error(-1, "模板配置不能为空!");

            $data = array(
                "fee_type" => $fee_type,
                "template_name" => $template_name,
                "site_id" => $this->site_id,
                'is_default' => $is_default,
                'surplus_area_ids' => $surplus_area_ids,
            );
            $json_data = json_decode($json, true);
            $result = $express_template_model->addExpressTemplate($data, $json_data);
            return $result;
        }else{
            // 地区等级设置 将来从配置中查询数据
            $area_level = 4;
            // 计费方式
            $fee_type_obj = [
                '1'=>['name'=>'按重量计费','snum'=>'首重（Kg）','xnum'=>'续重（Kg）'],
                '2'=>['name'=>'按体积计费','snum'=>'首体积(m³)','xnum'=>'续体积(m³)'],
                '3'=>['name'=>'按件计费','snum'=>'首件（个）','xnum'=>'续件（个）'],
            ];
            $this->assign('fee_type_obj', $fee_type_obj);
            $this->assign('fee_type_json', json_encode($fee_type_obj));
            $this->assign('area_level', $area_level);//地址级别
            return $this->fetch("express/add_template");
        }
    }

    /**
     * 编辑运费模板
     * @return mixed
     */
    public function editTemplate(){
        $template_id = input("template_id", 0);
        $express_template_model = new ExpressTemplate();
        if(request()->isAjax()){
            $fee_type = input("fee_type", '');//运费计算方式1.重量2体积3按件
            $template_name = input("template_name", '');
            $json = input("json", "");
            $is_default = input('is_default', 0);
            $surplus_area_ids = input('surplus_area_ids', '');
            if(empty($json))
                return error(-1, "模板配置不能为空!");

            $data = array(
                "fee_type" => $fee_type,
                "template_name" => $template_name,
                "site_id" => $this->site_id,
                "template_id" => $template_id,
                "is_default" => $is_default,
                'surplus_area_ids' => $surplus_area_ids,
            );
            $json_data = json_decode($json, true);
            $result = $express_template_model->editExpressTemplate($data, $json_data);
            return $result;
        }else{
            // 地区等级设置 将来从配置中查询数据
            $area_level = 4;
            // 计费方式
            $fee_type_obj = [
                '1'=>['name'=>'按重量计费','snum'=>'首重（Kg）','xnum'=>'续重（Kg）'],
                '2'=>['name'=>'按体积计费','snum'=>'首体积(m³)','xnum'=>'续体积(m³)'],
                '3'=>['name'=>'按件计费','snum'=>'首件（个）','xnum'=>'续件（个）'],
            ];
            $this->assign('fee_type_obj', $fee_type_obj);
            $this->assign('fee_type_json', json_encode($fee_type_obj));
            $this->assign('area_level', $area_level);//地址级别
            $info_result = $express_template_model->getExpressTemplateInfo($template_id, $this->site_id);
            $info = $info_result["data"];
            $this->assign("info", $info);
            return $this->fetch("express/edit_template");
        }
    }
    /**
     * 删除运费模板
     * @return mixed
     */
    public function deleteTemplate(){
        if(request()->isAjax()) {
            $template_id = input("template_id", 0);
            $express_template_model = new ExpressTemplate();
            $result = $express_template_model->deleteExpressTemplate($template_id, $this->site_id);
            return $result;
        }
    }

    /**
     * 设置默认运费模板
     * @return mixed
     */
    public function defaultTemplate(){
        if(request()->isAjax()) {
            $template_id = input("template_id", 0);
            $express_template_model = new ExpressTemplate();
            $result = $express_template_model->updateDefaultExpressTemplate($template_id, 1, $this->site_id);
            return $result;
        }
    }

    /**
     * 通过ajax得到运费模板的地区数据
     */
    public function getAreaList(){
        if (request()->isAjax()) {
            $address_model = new AddressModel();
            $area_level = input('level', 4);
            $area_list = $address_model->getAddressTree($area_level)['data'];
            return $area_list;
        }
    }

    /**
     * 查询可用物流公司
     */
    public function getShopExpressCompanyList(){
        if (request()->isAjax()) {
            $express_company_model = new ExpressCompany();
            //店铺物流公司
            $result = $express_company_model->getExpressCompanyShopList([["site_id", "=", $this->site_id]]);
            return $result;
        }
    }
}