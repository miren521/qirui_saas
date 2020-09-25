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

use addon\shopwithdraw\model\Config as ShopWithdrawConfig;
use app\model\shop\ShopApply;
use app\model\shop\ShopGroup as ShopGroupModel;
use app\model\system\Promotion as PrmotionModel;
use app\model\web\WebSite as WebsiteModel;
use app\model\shop\Config as ShopConfigModel;
use app\model\shop\Shop as ShopModel;
use app\model\shop\ShopReopen as ShopReopenModel;
use app\model\system\Address as AddressModel;

/**
 * 快捷开店认证
 * Class Cert
 * @package app\shop\controller
 */
class Cert extends BaseShop
{

    /**
     * 认证首页
     */
    public function index()
    {

        $shop_group_model = new ShopGroupModel();
        $promotion_model = new PrmotionModel();
        //插件
        $promotions = $promotion_model->getPromotions();
        $promotions = $promotions['shop'];
        //店铺等级
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
        $this->assign('group_info', $shop_group);

        //查询省级数据列表
        $address_model = new AddressModel();
        $list = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
        $this->assign("province_list", $list["data"]);

        //认证信息
        $userInfo = session($this->app_module);
        $this->assign('userInfo',$userInfo);
        $shop_apply_model = new ShopApply();
        $shop_apply_info = $shop_apply_model->getApplyDetail([['uid', '=', $userInfo['uid'] ]]);
        $this->assign('shop_apply_info',$shop_apply_info['data']);

        //商家信息
        $shop_apply_model = new ShopModel();
        $shop = $shop_apply_model->getShopInfo([['site_id', '=', $this->site_id ]],'site_id,site_name,category_id,category_name');
        $this->assign('shop',$shop['data']);

        //平台配置信息
        $website_model = new WebsiteModel();
        $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
        $this->assign('website_info',$website_info['data']);

        //收款信息
        $shop_config_model = new ShopConfigModel();
        $receivable_config = $shop_config_model->getSystemBankAccount();
        $this->assign('receivable_config',$receivable_config['data']);
        $this->assign("support_transfer_type", $this->getTransferType());
        return $this->fetch('cert/index');

    }

    public function getTransferType(){

        $support_type = [];
        if(addon_is_exit("shopwithdraw")){
            $config_model = new ShopWithdrawConfig();
            $config_result = $config_model->getConfig();
            $config = $config_result["data"];
            if($config["is_use"]){
                $support_type = explode(",", $config["value"]["transfer_type"]);
            }else{
                $support_type = ["alipay", "bank"];
            }
        }else{
            $support_type = ["alipay", "bank"];
        }
        return $support_type;
    }
    /**
     * 申请续签
     */
    public function reopen()
    {
        if(request()->isAjax()){

            $site_id = $this->site_id;
            $reopen_data = [
                'site_id' => $site_id,//店铺ID
                'apply_year' => input('apply_year',''),//入驻年长
                'shop_group_name' => input('shop_group_name',''),//开店套餐名称
                'shop_group_id' => input('shop_group_id',''),//开店套餐id
                'paying_money_certificate' => input('paying_money_certificate',''),//支付凭证
                'paying_money_certificate_explain' => input('paying_money_certificate_explain','')//付款凭证说明
            ];

            $shop_model = new ShopModel();
            $condition[] = ['site_id','=',$reopen_data['site_id']];
            //获取该店分类ID
            $shop_info = $shop_model->getShopInfo($condition,'category_id');

            $apply_model = new Shopapply();
            //计算入驻金额
            $apply_money = $apply_model->getApplyMoney($reopen_data['apply_year'], $reopen_data['shop_group_id'], $shop_info['data']['category_id']);
            $reopen_data['paying_amount'] = $apply_money['code']['paying_amount'];

            $model = new ShopReopenModel();
            $result = $model->addReopen($reopen_data);

            return $result;
        }else{
            //获取店铺信息
            $condition[] = ['site_id','=',$this->site_id];
            $apply_model = new ShopModel();
            $field = 'site_id,site_name,category_id,category_name,group_id,group_name';
            $shop_info = $apply_model->getShopInfo($condition,$field);
            $this->assign('shop', $shop_info['data']);

            $shop_group_model = new ShopGroupModel();
            $promotion_model = new PrmotionModel();
            //插件
            $promotions = $promotion_model->getPromotions();
            $promotions = $promotions['shop'];
            //店铺等级
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
            $this->assign('group_info', $shop_group);

            //平台配置信息
            $website_model = new WebsiteModel();
            $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
            $this->assign('website_info',$website_info['data']);

            //收款信息
            $shop_config_model = new ShopConfigModel();
            $receivable_config = $shop_config_model->getSystemBankAccount();
            $this->assign('receivable_config',$receivable_config['data']);

            return $this->fetch('cert/reopen');
        }

    }

    /**
     * 编辑续签首页
     */
    public function editReopenInfo()
    {
        if(request()->isPost()){
            $site_id = $this->site_id;
            $reopen_data = [
                'id' => input('id',''),
                'site_id' => $site_id,//店铺ID
                'paying_money_certificate' => input('paying_money_certificate',''),//支付凭证
                'paying_money_certificate_explain' => input('paying_money_certificate_explain','')//付款凭证说明
            ];

            $reopen_model = new ShopReopenModel();
            $result = $reopen_model->editReopen($reopen_data);

            return $result;
        }else{
            //获取店铺信息
            $condition[] = ['site_id','=',$this->site_id];
            $apply_model = new ShopModel();
            $field = 'site_id,site_name,category_id,category_name,group_id,group_name';
            $shop_info = $apply_model->getShopInfo($condition,$field);
            $this->assign('shop', $shop_info['data']);
            $shop_group_model = new ShopGroupModel();
            $promotion_model = new PrmotionModel();
            //插件
            $promotions = $promotion_model->getPromotions();
            $promotions = $promotions['shop'];
            //店铺等级
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
            $this->assign('group_info', $shop_group);

            //平台配置信息
            $website_model = new WebsiteModel();
            $website_info = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
            $this->assign('website_info',$website_info['data']);


            //收款信息
            $shop_config_model = new ShopConfigModel();
            $receivable_config = $shop_config_model->getSystemBankAccount();
            $this->assign('receivable_config',$receivable_config['data']);

            //获取续签信息
            $reopen_model = new ShopReopenModel();
            $reopen_info = $reopen_model->getReopenInfo([['sr.apply_state','in','-1,1'],['sr.site_id','=',$this->site_id]],'*');
            $this->assign('reopen_info',$reopen_info['data']);

            return $this->fetch('cert/edit_reopen');
        }

    }


}