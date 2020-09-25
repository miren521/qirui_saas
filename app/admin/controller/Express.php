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

use app\model\express\ExpressCompany as ExpressCompanyModel;
use app\model\express\Kd100;
use app\model\express\Kdbird;

/**
 * 物流公司管理 控制器
 */
class Express extends BaseAdmin
{
    /**
     * 物流公司列表
     */
    public function expressCompany()
    {
        if(request()->isAjax()){
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_text = input('search_text', '');
            $condition = [];
            $condition[] = ['company_name', 'like', '%'. $search_text .'%'];
            $order = '';
            $field = 'company_id,company_name,express_no_kdniao,express_no_kd100_free,express_no_kd100,express_no_cainiao,express_no_ext,logo,sort,url';

            $express_company_model = new ExpressCompanyModel();
            return $express_company_model->getExpressCompanyPageList($condition, $page, $page_size, $order, $field);
        }else{
            $this->forthMenu();
            return $this->fetch('express/express_company');
        }
    }

    /**
     * 物流公司添加
     */
    public function addCompany()
    {
        if(request()->isAjax()){
            $data = [
                'company_name' => input('company_name', ''),//物流公司名称
                'sort' => input('sort', 0),//排序
                'logo' => input('logo', ''),//logo
                'url' => input('url', ''),//网址
                'express_no_kdniao' => input('express_no_kdniao', ''),//快递鸟编码
                'express_no_kd100_free' => input('express_no_kd100_free', ''),//快递100免费版编码
                'express_no_kd100' => input('express_no_kd100', ''),//快递100付费版编码
                'express_no_cainiao' => input('express_no_cainiao', ''),//菜鸟物流接口编码 
                'express_no_ext' => input('express_no_ext', ''),//快递查询接口编码
                'content_json' => input('content_json', '[]'),//打印内容
                'background_image' => input('background_image', ''),//打印背景图
                'font_size' => input('font_size', 14),//打印字体大小 单位px
                'width' => input('width', 0),//显示尺寸宽度 px
                'height' => input('height', 0),//显示尺寸高度 px
                'scale' => input('scale', 1),//真实尺寸（mm）与显示尺寸（px）的比例
                'create_time' => time(),
                'is_electronicsheet' => input('is_electronicsheet', 0),//是否支持电子面单
                'print_style' => input('print_style', 0),//电子面单打印风格
            ];

            $express_company_model = new ExpressCompanyModel();
            $res = $express_company_model->addExpressCompany($data);
            if($res['code'] >= 0)
            {
                $this->addLog("添加物流公司:".$data['company_name'], $data);
            }
            return $res;
        }else{

            //打印项
            $express_company_model = new ExpressCompanyModel();
            $print_item_list = $express_company_model->getPrintItemList();
            $this->assign('print_item_list',$print_item_list);

            return $this->fetch('express/add_company');
        }
    }

    /**
     * 物流公司编辑
     */
    public function editCompany()
    {
        if(request()->isAjax()){
            $data = [
                'company_name' => input('company_name', ''),//物流公司名称
                'sort' => input('sort', 0),//排序
                'logo' => input('logo', ''),//logo
                'url' => input('url', ''),//网址
                'express_no_kdniao' => input('express_no_kdniao', ''),//快递鸟编码
                'express_no_kd100_free' => input('express_no_kd100_free', ''),//快递100免费版编码
                'express_no_kd100' => input('express_no_kd100', ''),//快递100付费版编码
                'express_no_cainiao' => input('express_no_cainiao', ''),//菜鸟物流接口编码
                'express_no_ext' => input('express_no_ext', ''),//快递查询接口编码
                'content_json' => input('content_json', '[]'),//打印内容
                'background_image' => input('background_image', ''),//打印背景图
                'font_size' => input('font_size', 14),//打印字体大小 单位mm
                'width' => input('width', 0),//显示尺寸宽度 px
                'height' => input('height', 0),//显示尺寸高度 px
                'scale' => input('scale', 1),//真实尺寸（mm）与显示尺寸（px）的比例
                'modify_time' => time(),
                'company_id' => input('company_id', 0),
                'is_electronicsheet' => input('is_electronicsheet', 0),//是否支持电子面单
                'print_style' => input('print_style', 0),//电子面单打印风格
            ];

            $express_company_model = new ExpressCompanyModel();
            $res = $express_company_model->editExpressCompany($data);
            $this->addLog("编辑物流公司:".$data['company_name'], $data);
            return $res;
        }else{
            //物流公司信息
            $company_id = input('company_id', 0);
            $express_company_model = new ExpressCompanyModel();
            $company_info = $express_company_model->getExpressCompanyInfo([['company_id', '=', $company_id]]);
            $this->assign('company_info', $company_info);

            //打印项
            $print_item_list = $express_company_model->getPrintItemList();
            $this->assign('print_item_list',$print_item_list);

            return $this->fetch('express/edit_company');
        }
    }

    /**
     * 物流公司删除
     */
    public function deleteCompany()
    {
        $company_ids = input('company_ids', '');
        $express_company_model = new ExpressCompanyModel();
        $this->addLog("删除物流公司:".$company_ids);
        return $express_company_model->deleteExpressCompany([['company_id', 'in', $company_ids]]);
    }

    /**
     * 修改物流公司排序
     */
    public function modifySort()
    {
        $sort = input('sort', 0);
        $company_id = input('company_id', 0);
        $express_company_model = new ExpressCompanyModel();
        return $express_company_model->modifyExpressCompanySort($sort, $company_id);
    }


    /**
     * 物流跟踪
     */
    public function trace(){
        if (request()->isAjax()) {

            $trace = input('traces_type', 'kd100');
            if ($trace == 'kd100') {
                $data = array(
                    "appkey" => input("appkey", ""),
                    "customer" => input("customer", ""),
                    "is_pay" => input("is_kd100_pay", 0),//是否是付费版
                );
                $kd100_config_model = new Kd100();
                $result = $kd100_config_model->setKd100Config($data, 1);
            }
            if ($trace == 'kdbird') {
                $data = array(
                    "EBusinessID" => input("EBusinessID", ""),
                    "AppKey" => input("AppKey", ""),
                    "is_pay" => input("is_bird_pay", 0),//是否是付费版
                );
                $kdbird_config_model = new Kdbird();
                $result = $kdbird_config_model->setKdbirdConfig($data, 1);
            }
            return $result;
        }else{
            $this->forthMenu();
            $kd100_model = new Kd100();
            $kdbird_model = new Kdbird();
            $kd100_config = $kd100_model->getKd100Config();
            $kdbird_config = $kdbird_model->getKdbirdConfig();
            $traces = [
                [
                    'name' => 'kd100',
                    'title' => '快递100',
                    'is_use' => $kd100_config['data']['is_use']
                ],
                [
                    'name' => 'kdbird',
                    'title' => '快递鸟',
                    'is_use' => $kdbird_config['data']['is_use']
                ]
            ];
            $this->assign('traces_type', $traces);
            $this->assign('kd100_config', $kd100_config["data"]);
            $this->assign('kdbird_config', $kdbird_config["data"]);
            return $this->fetch('express/trace');
        }

    }
}