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

use addon\shopwithdraw\model\Config as ShopWithdrawConfig;
use addon\supply\model\SupplyApply;
use app\model\system\Promotion as PromotionModel;
use app\model\web\WebSite as WebsiteModel;
use app\model\shop\Config as ShopConfigModel;
use addon\supply\model\Supplier as SupplierModel;
use addon\supply\model\SupplyReopen as SupplyReopenModel;
use app\model\system\Address as AddressModel;

/**
 * 快捷开店认证
 * Class Cert
 * @package addon\supply\supply\controller
 */
class Cert extends BaseSupply
{

    /**
     * 认证首页
     */
    public function index()
    {
        //查询省级数据列表
        $address_model = new AddressModel();
        $list          = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
        $this->assign("province_list", $list["data"]);

        //认证信息
        $userInfo = session($this->app_module);
        $this->assign('userInfo', $userInfo);
        $apply_model = new SupplyApply();
        $apply_info  = $apply_model->getApplyDetail([['uid', '=', $userInfo['uid']]]);
        $this->assign('apply_info', $apply_info['data']);

        //商家信息
        $supply_model = new SupplierModel();
        $info             = $supply_model->getSupplierInfo(
            [['supplier_site_id', '=', $this->supply_id]],
            'supplier_site_id,title,category_id,category_name'
        );
        $this->assign('supply', $info['data']);

        //平台配置信息
        $website_model = new WebsiteModel();
        $website_info  = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
        $this->assign('website_info', $website_info['data']);

        //收款信息
        $config_model = new ShopConfigModel();
        $receivable_config = $config_model->getSystemBankAccount();
        $this->assign('receivable_config', $receivable_config['data']);
        $this->assign("support_transfer_type", $this->getTransferType());
        return $this->fetch('cert/index');
    }

    public function getTransferType()
    {

        $support_type = [];
        if (addon_is_exit("shopwithdraw")) {
            $config_model  = new ShopWithdrawConfig();
            $config_result = $config_model->getConfig();
            $config        = $config_result["data"];
            if ($config["is_use"]) {
                $support_type = explode(",", $config["value"]["transfer_type"]);
            } else {
                $support_type = ["alipay", "bank"];
            }
        } else {
            $support_type = ["alipay", "bank"];
        }
        return $support_type;
    }

    /**
     * 申请续签
     */
    public function reopen()
    {
        if (request()->isAjax()) {
            $site_id     = $this->supply_id;
            $reopen_data = [
                'site_id'                          => $site_id,//店铺ID
                'apply_year'                       => input('apply_year', ''),//入驻年长
                'paying_money_certificate'         => input('paying_money_certificate', ''),//支付凭证
                'paying_money_certificate_explain' => input('paying_money_certificate_explain', '')//付款凭证说明
            ];

            $supplier_model = new SupplierModel();
            $condition[] = ['supplier_site_id', '=', $reopen_data['site_id']];
            //获取该店分类ID
            $supply_info = $supplier_model->getSupplierInfo($condition, 'category_id, bond');

            $apply_model = new SupplyApply();
            //计算入驻金额
            $apply_money = $apply_model->getApplyMoney($reopen_data['apply_year'], $supply_info['data']['category_id']);
            if ($supply_info['data']['bond'] > 0) {
                $reopen_data['paying_amount'] = $apply_money['code']['paying_amount'];
            } else {
                $reopen_data['paying_amount'] = $apply_money['code']['paying_apply'];
            }

            $model  = new SupplyReopenModel();
            $result = $model->addReopen($reopen_data);

            return $result;
        }
        //获取店铺信息
        $condition[] = ['supplier_site_id', '=', $this->supply_id];
        $apply_model = new SupplierModel();
        $field       = 'supplier_site_id,title,category_id,category_name';
        $supply_info   = $apply_model->getSupplierInfo($condition, $field);
        $this->assign('supply', $supply_info['data']);

        //平台配置信息
        $website_model = new WebsiteModel();
        $website_info  = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
        $this->assign('website_info', $website_info['data']);

        //收款信息
        $config_model = new ShopConfigModel();
        $receivable_config = $config_model->getSystemBankAccount();
        $this->assign('receivable_config', $receivable_config['data']);

        return $this->fetch('cert/reopen');
    }

    /**
     * 编辑续签首页
     */
    public function editReopenInfo()
    {
        if (request()->isPost()) {
            $site_id     = $this->supply_id;
            $reopen_data = [
                'id'                               => input('id', ''),
                'site_id'                          => $site_id,//店铺ID
                'paying_money_certificate'         => input('paying_money_certificate', ''),//支付凭证
                'paying_money_certificate_explain' => input('paying_money_certificate_explain', '')//付款凭证说明
            ];

            $reopen_model = new SupplyReopenModel();
            $result       = $reopen_model->editReopen($reopen_data);

            return $result;
        }
        //获取店铺信息
        $condition[] = ['supplier_site_id', '=', $this->supply_id];
        $apply_model = new SupplierModel();
        $field       = 'supplier_site_id,title,category_id,category_name';
        $info   = $apply_model->getSupplierInfo($condition, $field);
        $this->assign('supply', $info['data']);

        //平台配置信息
        $website_model = new WebsiteModel();
        $website_info  = $website_model->getWebSite([['site_id', '=', 0]], 'web_qrcode,web_phone');
        $this->assign('website_info', $website_info['data']);

        //收款信息
        $config_model = new ShopConfigModel();
        $receivable_config = $config_model->getSystemBankAccount();
        $this->assign('receivable_config', $receivable_config['data']);

        //获取续签信息
        $reopen_model = new SupplyReopenModel();
        $reopen_info  = $reopen_model->getReopenInfo(
            [['sr.apply_state', 'in', '-1,1'], ['sr.site_id', '=', $this->supply_id]]
        );
        $this->assign('reopen_info', $reopen_info['data']);

        return $this->fetch('cert/edit_reopen');
    }
}
