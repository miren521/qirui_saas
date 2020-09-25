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


namespace addon\supply\shop\controller;


use app\model\goods\GoodsCategory;
use app\shop\controller\BaseShop;
use addon\supply\model\Purchase as PurchaseModel;
use app\model\system\Address;

/**
 * 求购信息
 * Class Order
 * @package app\shop\controller
 */
class Purchase extends BaseSupplyshop
{

    public function __construct()
    {
        parent::__construct();
        $check_login_result = $this->checkLogin();
        if($check_login_result['code'] < 0){
            echo json_encode($check_login_result);
            exit();
        }
    }


    /**
     *发布求购信息
     */
    public function release(){
        if (request()->isAjax()) {
            $purchase_model = new PurchaseModel();
            $purchase_goods_item_json = input('purchase_goods_item_json', '');
            if(empty($purchase_goods_item_json))
                return $purchase_model->error([], '求购商品不能为空!');

            $purchase_goods_item = json_decode($purchase_goods_item_json, true);
            $data = [
                'title' => input('title',''),//求购标题
                'type' => input('type',1),//求购类型  1 现货  2定制
                'nickname' => input('nickname',''),//求购人姓名
                'sex' => input('sex',''),//求购人性别
                'mobile' => input('mobile',''),//求购人联系电话
                'email' => input('email',''),//求购人联系邮箱
                'end_time' => date_to_time(input('end_time','')),//求购截止时间
                'is_invoice' => input('is_invoice',''),
                'province' => input('province',''),//收货地址 省id
                'province_name' => input('province_name',''),//收货地址 省
                'city' => input('city',''),//收货地址 城市id
                'city_name' => input('city_name',''),//收货地址 城市
                'district' => input('district',''),//收货地址 区县id
                'district_name' => input('district_name',''),//收货地址 区县
                'community' => input('community',''),//收货地址 乡镇id
                'community_name' => input('community_name',''),//收货地址 乡镇
                'address' => input('address',''),//详细地址
                'full_address' => input('full_address',''),//完整地址
                'remark' => input('remark', ''),//补充说明
                'shop_id' => $this->site_id,
                'shop_name' => $this->shop_info['site_name'],
                'uid' => $this->uid,
                'purchase_goods_item' => $purchase_goods_item
            ];
            $result = $purchase_model->addPurchase($data);
            return $result;
        } else {
            $goods_category_model = new GoodsCategory();
            $category_list_result = $goods_category_model->getCategoryList([['pid', '=', 0]]);
            $this->assign('category_list', $category_list_result['data'] ?? []);
            //查询省级数据列表
            $address_model = new Address();
            $list          = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $list["data"]);
            return $this->fetch("purchase/release", [], $this->replace);
        }
    }


    /**
     * 求购列表
     */
    public function lists()
    {
        if (request()->isAjax()) {
            $purchase_model = new PurchaseModel();
            $is_self = input('is_self', 0);
            $status = input('status', 'all');
            $condition = array();
            if($status != 'all'){
                $condition[] = ['status', '=', $status];
            }
            $page = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $res = $purchase_model->getPurchasePageList($condition, $page, $page_size, "create_time desc");
            return $res;
        } else {
            return $this->fetch("purchase/lists", [], $this->replace);
        }
    }

    /**
     * 我的求购列表
     */
    public function purchase()
    {

        if (request()->isAjax()) {
            $purchase_model = new PurchaseModel();
            $is_self = input('is_self', 0);
            $status = input('status', 'all');
            $condition          = array(
                ['shop_id', '=', $this->site_id]
            );

            if($status != 'all'){
                $condition[] = ['status', '=', $status];
            }

            $page = input('page', 1);
            $page_size  = input('page_size', PAGE_LIST_ROWS);
            $res = $purchase_model->getPurchasePageList($condition, $page, $page_size, "create_time desc");
            return $res;
        } else {
            return $this->fetch("purchase/purchase", [], $this->replace);
        }
    }

    /**
     * 采购详情
     */
    public function info(){
        $purchase_id = input('purchase_id', 0);
        $purchase_model = new PurchaseModel();
        $info_result = $purchase_model->getPurchaseInfo([['purchase_id', '=', $purchase_id]]);
        $this->assign('info', $info_result['data']);
        return $this->fetch("purchase/info", [], $this->replace);
    }

    /**
     * 我的采购详情
     */
    public function purchaseinfo(){
        $purchase_id = input('purchase_id', 0);
        $purchase_model = new PurchaseModel();
        $info_result = $purchase_model->getPurchaseInfo([['purchase_id', '=', $purchase_id], ['shop_id', '=', $this->site_id]]);
        $this->assign('info', $info_result['data']);
        return $this->fetch("purchase/purchaseinfo", [], $this->replace);
    }

    /**
     * 主动截止求购
     * @return array
     */
    public function close(){
        $purchase_id = input('purchase_id', 0);
        $purchase_model = new PurchaseModel();
        $result = $purchase_model->closePurchase([['purchase_id', '=', $purchase_id], ['shop_id', '=', $this->site_id]]);
        return $result;
    }

    /**
     * 删除求购
     * @return array
     */
    public function delete(){
        $purchase_id = input('purchase_id', 0);
        $purchase_model = new PurchaseModel();
        $result = $purchase_model->deletePurchase([['purchase_id', '=', $purchase_id], ['shop_id', '=', $this->site_id]]);
        return $result;
    }
}
