<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\electronicsheet\model;

use app\model\express\ExpressCompany;
use app\model\system\Config;
use app\model\BaseModel;

class ExpressElectronicsheet extends BaseModel
{

    /************************************ 电子面单设置 start **********************************************************/
    /**
     * 电子面单设置
     * @param $data
     * @return array
     */
    public function setElectronicsheetConfig($data)
    {
        $config = new Config();
        $res = $config->setConfig($data, '电子面单设置', 1, [ [ 'site_id', '=', $data['site_id'] ], [ 'app_module', '=', 'shop' ], [ 'config_key', '=', 'ELECTRONICSHEET_CONFIG' ] ]);
        return $this->success($res);
    }

    /**
     * 获取电子面单设置
     * @return array
     */
    public function getElectronicsheetConfig($site_id)
    {
        $config = new Config();
        $res = $config->getConfig([ [ 'site_id', '=', $site_id ], [ 'app_module', '=', 'shop' ], [ 'config_key', '=', 'ELECTRONICSHEET_CONFIG' ] ]);
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'type' => 'kdniao',

                'kdniao_user_id' => '',
                'kdniao_api_key' => '',
                'kdniao_port' => '',

                'cainiao_token' => '',
                'cainiao_ip' => ''
            ];
        }
        return $res;
    }

    /************************************ 电子面单设置 end **********************************************************/

    /**
     * 添加电子面单
     * @param $data
     * @return array
     */
    public function addExpressElectronicsheet($data)
    {
        //判断模板名称是否重复
        $count = model('express_electronicsheet')->getCount(
            [
                ['site_id','=',$data['site_id']],
                ['template_name','=',$data['template_name']]
            ]);

        if($count > 0){
            return $this->error('','该电子面单名称已存在');
        }
        $express_company_model = new ExpressCompany();
        $express_company_info = $express_company_model->getExpressCompanyInfo([ ['company_id','=',$data['company_id']] ],'company_id,company_name,express_no_kdniao');
        if(empty($express_company_info)){
            return $this->error('','快递公司不存在');
        }
        if(empty($express_company_info['data']['express_no_kdniao']) || empty($express_company_info['data']['company_name'])){
            return $this->error('','快递公司名称或者快递鸟编码为空');
        }

        $data['express_no'] = $express_company_info['data']['express_no_kdniao'];
        $data['company_name'] = $express_company_info['data']['company_name'];
        $data['create_time'] = time();

        model('express_electronicsheet')->startTrans();
        try{

            if($data['is_default'] == 1){
                $this->setExpressElectronicsheetDefault( [['site_id','=',$data['site_id']],['is_default','=',1]],0);
            }
            model('express_electronicsheet')->add($data);

            model('express_electronicsheet')->commit();
            return $this->success();
        }catch (\Exception $e){

            model('express_electronicsheet')->rollback();
            return $this->error('',$e->getMessage());
        }
    }


    /**
     * 编辑电子面单
     * @param $data
     * @return array
     */
    public function editExpressElectronicsheet($data)
    {
        //判断模板名称是否重复
        $count = model('express_electronicsheet')->getCount(
            [
                ['site_id','=',$data['site_id']],
                ['id','<>',$data['id']],
                ['template_name','=',$data['template_name']]
            ]);
        if($count > 0){
            return $this->error('','该电子面单名称已存在');
        }

        $express_company_model = new ExpressCompany();
        $express_company_info = $express_company_model->getExpressCompanyInfo([ ['company_id','=',$data['company_id']] ],'company_id,company_name,express_no_kdniao');
        if(empty($express_company_info)){
            return $this->error('','快递公司不存在');
        }
        if(empty($express_company_info['data']['express_no_kdniao']) || empty($express_company_info['data']['company_name'])){
            return $this->error('','快递公司名称或者快递鸟编码为空');
        }
        $data['express_no'] = $express_company_info['data']['express_no_kdniao'];
        $data['company_name'] = $express_company_info['data']['company_name'];
        $data['update_time'] = time();

        model('express_electronicsheet')->startTrans();
        try{

            if($data['is_default'] == 1){
                $this->setExpressElectronicsheetDefault( [['site_id','=',$data['site_id']],['id','<>',$data['id']],['is_default','=',1]],0);
            }
            $res = model('express_electronicsheet')->update($data,[['id','=',$data['id']],['site_id','=',$data['site_id']]]);

            model('express_electronicsheet')->commit();
            return $this->success($res);
        }catch (\Exception $e){

            model('express_electronicsheet')->rollback();
            return $this->error('',$e->getMessage());
        }
    }

    /**
     * 设置默认状态
     * @param array $condition
     * @param $is_default
     * @return int
     */
    public function setExpressElectronicsheetDefault($condition = [],$is_default)
    {
        $res = model('express_electronicsheet')->update( ['is_default'=>$is_default], $condition);
        return $this->success($res);
    }

    /**
     * 删除
     * @param $condition
     * @return array
     */
    public function deleteExpressElectronicsheet($condition)
    {
        $res = model('express_electronicsheet')->delete($condition);
        return $this->success($res);
    }

    /**
     * 获取电子面单信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getExpressElectronicsheetInfo($condition = [], $field = '*')
    {
        $res = model('express_electronicsheet')->getInfo($condition,$field);
        return $this->success($res);
    }

    /**
     * 获取电子面单列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getExpressElectronicsheetList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('express_electronicsheet')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取电子面单分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getExpressElectronicsheetPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('express_electronicsheet')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

}