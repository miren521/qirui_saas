<?php


namespace app\saas\controller;

use app\model\agent\Agent as AgentModel;
use app\model\BaseModel;
use think\facade\Db;
use think\View;

class Agent extends  BaseModel
{

    public  function  index(){
        return view('agent');
    }

    public function agent_list(){
        $agent=new AgentModel();

        return $agent->getAgentList();
    }
}