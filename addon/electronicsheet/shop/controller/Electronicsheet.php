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


namespace addon\electronicsheet\shop\controller;

use app\model\express\ExpressCompany;
use app\shop\controller\BaseShop;
use addon\electronicsheet\model\ExpressElectronicsheet as ExpressElectronicsheetModel;


class Electronicsheet extends BaseShop
{
	/*
	 *  电子面单列表
	 */
	public function lists()
	{
		$model = new ExpressElectronicsheetModel();
		
		$condition[] = [ 'site_id', '=', $this->site_id ];
		//获取续签信息
		if (request()->isAjax()) {
			$status = input('status', '');//模板状态
			if ($status) {
				$condition[] = [ 'status', '=', $status ];
			}
            $template_name = input('template_name','');
			if($template_name){
                $condition[] = [ 'template_name', 'like', '%' . $template_name . '%' ];
            }

			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$list = $model->getExpressElectronicsheetPageList($condition, $page, $page_size, 'is_default desc');
			return $list;
		}
		return $this->fetch("electronicsheet/lists");
	}
	
	/**
	 * 添加电子面单
	 */
	public function add()
	{
		if (request()->isAjax()) {
            $data = [
                'site_id' => $this->site_id,
                'site_name' => $this->shop_info['site_name'],
                'template_name' => input('template_name',''),
                'company_id' => input('company_id',''),
                'customer_name' => input('customer_name',''),
                'customer_pwd' => input('customer_pwd',''),
                'send_site' => input('send_site',''),
                'send_staff' => input('send_staff',''),
                'month_code' => input('month_code',''),
                'postage_payment_method' => input('postage_payment_method',''),
                'is_notice' => input('is_notice',''),
                'is_default' => input('is_default',0)
            ];
            $model = new ExpressElectronicsheetModel();
            return $model->addExpressElectronicsheet($data);

		} else {
            //快递公司
            $express_company_model = new ExpressCompany();
            $condition = [
                ['is_electronicsheet','=',1],
            ];
            $company_list = $express_company_model->getExpressCompanyList($condition,'company_id,company_name,print_style','sort asc');
            $this->assign('company_list',$company_list['data']);
			return $this->fetch("electronicsheet/add");
		}
	}

    /**
     * 编辑电子面单
     */
    public function edit()
    {
        $model = new ExpressElectronicsheetModel();
        $id = input('id',0);
        if (request()->isAjax()) {
            $data = [
                'id' => $id,
                'site_id' => $this->site_id,
                'site_name' => $this->shop_info['site_name'],
                'template_name' => input('template_name',''),
                'company_id' => input('company_id',''),
                'customer_name' => input('customer_name',''),
                'customer_pwd' => input('customer_pwd',''),
                'send_site' => input('send_site',''),
                'send_staff' => input('send_staff',''),
                'month_code' => input('month_code',''),
                'postage_payment_method' => input('postage_payment_method',''),
                'is_notice' => input('is_notice',''),
                'is_default' => input('is_default',0)
            ];
            return $model->editExpressElectronicsheet($data);

        } else {

            $info = $model->getExpressElectronicsheetInfo([['id','=',$id],['site_id','=',$this->site_id]]);
            $this->assign('electronicsheet_info',$info['data']);

            //快递公司
            $express_company_model = new ExpressCompany();
            $condition = [
                ['is_electronicsheet','=',1],

            ];
            $company_list = $express_company_model->getExpressCompanyList($condition,'company_id,company_name,print_style','sort asc');
            $this->assign('company_list',$company_list['data']);
            return $this->fetch("electronicsheet/edit");
        }
    }

	/*
	 *  删除
	 */
	public function delete()
	{
		$id = input('id', '');

		$groupbuy_model = new ExpressElectronicsheetModel();
		return $groupbuy_model->deleteExpressElectronicsheet([['id','=',$id],['site_id','=',$this->site_id]]);
	}
	
	/*
	 *  修改默认状态
	 */
	public function setDefaultStatus()
	{
		$id = input('id', '');

		$groupbuy_model = new ExpressElectronicsheetModel();
		return $groupbuy_model->setExpressElectronicsheetDefault([['id','=',$id],['site_id','=',$this->site_id]], 1);
	}
	
}