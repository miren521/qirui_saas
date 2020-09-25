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


namespace addon\shopwithdraw\admin\controller;

use app\admin\controller\BaseAdmin;
use addon\shopwithdraw\model\Withdraw as WithdrawModel;

/**
 * 店铺提现
 */
class Withdraw extends BaseAdmin
{

    /**
     * 转账
     */
    public function transfer(){
        if(request()->isAjax()){
            $id = input('id', 0);

            $withdraw_model = new WithdrawModel();
            $condition = array(
                ["id", "=", $id]
            );
            $result = $withdraw_model->transfer($id);
            return $result;
        }
    }


}