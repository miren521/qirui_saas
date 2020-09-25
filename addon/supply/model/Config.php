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


namespace addon\supply\model;

use app\model\system\Config as ConfigModel;
use app\model\BaseModel;
use app\model\system\Cron;
use app\model\system\Document as DocumentModel;
use app\model\system\multitype;
use Carbon\Carbon;

/**
 * 供应商配置
 */
class Config extends BaseModel
{

    /**
     * 设置供应商配置
     * array $data
     */
    public function setSupplyConfig($data)
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '供应商配置', 1, [['site_id', '=', 0], ['app_module', '=', 'supply'], ['config_key', '=', 'SUPPLY_CONFIG']]);

        $cron = new Cron();
        switch ($data['period_type']) {
            case 1://天
                $date         = strtotime(date('Y-m-d 00:00:00'));
                $execute_time = strtotime('+1day', $date);
                break;
            case 2://周
                $execute_time = Carbon::parse('next monday')->timestamp;
                break;
            case 3://月
                $execute_time = Carbon::now()->addMonth()->firstOfMonth()->timestamp;
                break;
        }
        $cron->deleteCron([['event', '=', 'SupplyPeriodCalc']]);
        $cron->addCron('2', '1', '供应商周期结算', 'SupplyPeriodCalc', $execute_time, '0', $data['period_type']);
        return $res;
    }

    /**
     * 获取供应商配置
     */
    public function getSupplyConfig()
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'supply'], ['config_key', '=', 'SUPPLY_CONFIG']]);
        //如果为空,则填充默认数据
        if(empty($res['data']['value'])){
            $res['data']['value'] = array(
                'fee' => 10,
                'remark' => '',
                'period_type' => 3,
            );
        }
        return $res;
    }


    /**
     * 获取供应商提现设置
     */
    public function getSupplyWithdrawConfig()
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'SUPPLY_WITHDRAW']]);
        if (empty($res['data']['value'])) {
            //默认数据管理
            $res['data']['value'] = [
                'min_withdraw' => 0,  //最低提现金额
                'max_withdraw' => 0,  //最高提现金额
            ];
        }
        return $res;
    }

    /**
     * 设置供应商提现设置
     */
    public function setSupplyWithdrawConfig($data)
    {
        $config = new ConfigModel();
        $res    = $config->setConfig($data, '供应商提现设置', 1, [['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'SUPPLY_WITHDRAW']]);
        return $res;
    }

    /**
     * 获取入驻协议
     * @return array
     */
    public function getApplyAgreement()
    {
        $document = new DocumentModel();
        $info     = $document->getDocument([['site_id', '=', 0], ['app_module', '=', 'admin'], ['document_key', '=', "SUPPLY_APPLY_AGREEMENT"]]);
        return $info;
    }

    /**
     * 设置入驻协议
     * @param $title
     * @param $content
     * @return multitype
     */
    public function setApplyAgreement($title, $content)
    {
        $document = new DocumentModel();
        $res      = $document->setDocument($title, $content, [['site_id', '=', 0], ['app_module', '=', 'admin'], ['document_key', '=', "SUPPLY_APPLY_AGREEMENT"]]);
        return $res;
    }

    /**
     * 获取系统银行账户
     */
    public function getSystemBankAccount()
    {
        $config = new ConfigModel();
        $res    = $config->getConfig([['site_id', '=', 0], ['app_module', '=', 'admin'], ['config_key', '=', 'SYSTEM_BANK_ACCOUNT']]);
        if (empty($res['data']['value'])) {
            $res['data']['value'] = [
                'bank_account_name' => '',
                'bank_account_no'   => '',
                'bank_name'         => '',
                'bank_address'      => ''
            ];
        }
        return $res;
    }
}
