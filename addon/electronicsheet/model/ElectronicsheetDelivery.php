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


namespace addon\electronicsheet\model;

use app\model\BaseModel;
use app\model\express\ExpressCompany;
use app\model\shop\Shop;
use extend\Kdniao;
use app\model\system\Address;

/**
 * 电子面单物流配送
 */
class ElectronicsheetDelivery extends BaseModel
{

    /**
     * 电子面单发货
     * @param $param
     * @param $is_delivery
     * @return array
     */
    public function delivery($param)
    {
        //获取电子面单设置
        $electronicsheet_model = new ExpressElectronicsheet();
        $config = $electronicsheet_model->getElectronicsheetConfig($param['site_id']);
        $config_info = $config['data']['value'];
        if(empty($config_info)){
            return $this->error('','请先配置电子面单');
        }

        if(empty($param['template_id'])){
            return $this->error('','请先配置电子面单模板');
        }
        //获取电子模板信息
        $template = $electronicsheet_model->getExpressElectronicsheetInfo([['id','=',$param['template_id']]]);
        if(empty($template['data'])){
            return $this->error('','电子面单模板不存在');
        }

        //获取店铺信息
        $shop_model = new Shop();
        $shop = $shop_model->getShopInfo([['site_id','=',$param['site_id']]]);
        $shop_info = $shop['data'];

        if(empty($shop_info['name'])){
            return $this->error('','店铺联系地址中联系人姓名未设置');
        }
        if(empty($shop_info['province_name']) || empty($shop_info['city_name']) || empty($shop_info['district_name']) || empty($shop_info['address'])){
            return $this->error('','店铺联系地址中联系地址未设置');
        }
        if(empty($shop_info['telephone']) && empty($shop_info['mobile'])){
            return $this->error('','店铺联系地址中联系方式未设置');
        }

        $result = $this->electronicsheetDelivery($param,$config_info,$template['data'],$shop_info);

        if(isset($result['code']) && $result['code'] < 0){
            return $result;
        }else{
            
            return $this->success($result);
        }
    }


    /**
     * 电子面单发货
     * @param $delivery_id //包裹id
     * @param $config_info //快递鸟配置信息
     * @param $template  //电子面单模板
     * @param $shop_info //发货人信息
     * @param $goods_array //商品信息
     * @return array
     */
    public function electronicsheetDelivery($param,$config_info,$template,$shop_info)
    {
        //获取订单信息
        $order_info = model('order')->getInfo([ ['order_id','=',$param['order_id']] ]);
        //获取快递公司编码
        $express_company = model('express_company')->getInfo([ ['company_id','=',$template['company_id']] ],'express_no_kdniao,print_style');
        $print_style = json_decode($express_company['print_style'],true);

        //获取商品信息
        if(empty($param['order_goods_id_array'])){
            $goods_array = model('order_goods')->getInfo(
                [
                    ['order_id', '=', $param['order_id']]
                ],'sku_name as GoodsName,num as GoodsQuantity'
            );
        }else{
            $goods_array = model('order_goods')->getInfo(
                [
                    ["order_goods_id", "in", $param['order_goods_id_array']],
                    ['order_id', '=', $param['order_id']]
                ],'sku_name as GoodsName,num as GoodsQuantity'
            );
        }
        //替换商品名称中的特殊字符 '   "   #    &    +    <   >   %   \
        $search = array("'",'"','&','+','<','>','%',"\\",'#',"and");
        $goods_array['GoodsName']  = str_replace($search, '', $goods_array['GoodsName']);

        //实例化快递鸟
        $config = [
            'EBusinessID' => $config_info['kdniao_user_id'],
            'AppKey' => $config_info['kdniao_api_key']
        ];
        $kdniao = new Kdniao($config);
        //构造电子面单提交信息
        $eorder = [];
        $eorder['CustomerName'] = $template['customer_name'];
        $eorder['CustomerPwd'] = $template['customer_pwd'];
        $eorder['SendSite'] = $template['send_site'];
        $eorder['SendStaff'] = $template['send_staff'];
        $eorder['MonthCode'] = $template['month_code'];
        $eorder["ShipperCode"] = $express_company['express_no_kdniao'];//快递公司编码
        $eorder["OrderCode"] = $order_info['order_no'];//订单号
        $eorder["PayType"] = $template['postage_payment_method']; //邮费支付方式
        $eorder["ExpType"] = 1; //快递类型
        $eorder["TemplateSize"] = $print_style[$template['print_style']]['template_size']; //模板规格
        //发货人信息
        $sender = [];
        $sender["Name"] = $shop_info['name'];
        $sender["Mobile"] = $shop_info['mobile'];
        $sender["Tel"] = $shop_info['telephone'];
        $sender["ProvinceName"] = $shop_info['province_name'];
        $sender["CityName"] = $shop_info['city_name'];
        $sender["ExpAreaName"] = $shop_info['district_name'];
        $sender["Address"] = $shop_info['address'];
        $sender["PostCode"] = '000000';
        //获取收货人信息
        $area_model = new Address();
        $province_name = $area_model->getAreasInfo([['id','=',$order_info['province_id']]],'name');
        $city_name = $area_model->getAreasInfo([['id','=',$order_info['city_id']]],'name');
        $district_name = $area_model->getAreasInfo([['id','=',$order_info['district_id']]],'name');
        $receiver = [];
        $receiver["Name"] = $order_info['name'];
        $receiver["Mobile"] = $order_info['mobile'];
        $receiver["ProvinceName"] = $province_name['data']['name'];
        $receiver["CityName"] = $city_name['data']['name'];
        $receiver["ExpAreaName"] = $district_name['data']['name'];
        $receiver["Address"] = $order_info['address'];
        $receiver["PostCode"] = '000000';

        $commodity[] = $goods_array;
        $eorder["Sender"] = $sender;
        $eorder["Receiver"] = $receiver;
        $eorder["Commodity"] = $commodity;  //商品信息

        $eorder["IsReturnPrintTemplate"] = 1; //是否返回电子模板

        $jsonResult = $kdniao->submitEOrder($eorder);

        $result = json_decode($jsonResult, true);

        if($result["ResultCode"] == "100" || $result["ResultCode"] == "106") {
            return $result;
        }else {
            return $this->error('',$result['Reason']);
        }
    }


}