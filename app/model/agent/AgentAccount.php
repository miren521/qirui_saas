<?php


namespace app\model\agent;


use app\model\BaseModel;

class AgentAccount extends BaseModel
{
    /**
     * 代理商充值添加记录
     */
    public function addAgentAccount($data)
    {
        model('agent_account')->startTrans();

        try {
//            $agent_meal = model('agent_meal')->getInfo([['agent_id', '=', $data['account_id']],['meal_id','=',$data['meal_id']]]);
//            if ($agent_meal) {
//             model('agent_meal')->setDec([['agent_id', '=', $data['account_id']],['meal_id','=',$data['meal_id']]],'num',$data['num']);
//            }else{
//                $da=[
//                    'agent_id'=>$data['account_id'],
//                    'meal_id'=>$data['meal_id'],
//                    'num'=>$data['num'],
//                    'use_num'=>$data['num']
//                ];
//                model('agent_meal')->add($da);
//            }
            model('agent')->setDec(['id','=',$data['account_id']],'balance',$data['discount_money']);
            model('agent_account')->add($data);
            model('agent_account')->commit();

            return $this->success();

        } catch (Exception $e) {
            model('agent_account')->rollback();

            return $this->error('', $e->getMessage());
        }


    }

    /**
     * 套餐列表查询
     */

    public  function  getCustomer($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*'){
        $list = model('customer_level')->pageList($condition, $field, $order, $page, $page_size);
        return $list;
    }

    /**
     * 代理商充值记录列表
     */

    public  function agentAccountList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*'){
        $list = model('agent_account')->pageList($condition, $field, $order, $page, $page_size);
        return $list;
    }
}