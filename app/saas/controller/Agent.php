<?php


namespace app\saas\controller;

use addon\city\model\Menu;
use app\admin\controller\BaseAdmin;
use app\model\agent\Agent as AgentModel;
use app\model\BaseModel;
use app\model\system\Address as AddressModel;
use think\facade\Db;
use think\View;

class Agent extends BaseAdmin
{

    public function agent()
    {
        return view('agent');
    }

    /**
     * 代理商列表
     */
    public function agent_list()
    {

        $page = isset($this->params['page']) ? $this->params['page'] : 1;

        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;

        $condition = [];

        $order = 'agent_id desc';

        $field = '*';

        $agent = new AgentModel();
        return $agent->getAgentPageList($condition, $page, $page_size, $order, $field);
    }

    /**
     * 代理商添加页面
     */
    public function add_agent()
    {

        $agent = new AgentModel();
        if (request()->isAjax()) {
            $data = [
                'number' => input('number', ''),
                'password' => md5(input('password', '')),
                'name' => input('name', ''),
                'level' => input('level', ''),
                'region_id' => input('city_id', ''),
                'region' => input('site_area_name', ''),
                'address' => input('address', ''),
                'manager' => input('manager', ''),
                'contacts' => input('contacts', ''),
                'telephone' => input('telephone', ''),
                'phone' => input('phone', ''),
                'email' => input('email', ''),
                'qq' => input('qq', ''),
                'status' => input('status', '')
            ];
            $res = $agent->addAgent($data);
            return $res;
        } else {
            $address_model = new AddressModel();
            $list = $address_model->getAreaList([["pid", "=", 0], ["level", "=", 1]]);
            $this->assign("province_list", $list["data"]);

            return $this->fetch('agent/add_agent');

        }

    }

    /**
     * 代理商修改
     */
    public function edit_agent()
    {
        $agent_model = new AgentModel();
        if (request()->isAjax()) {
            $data = [
                'number' => input('number', ''),
                'name' => input('name', ''),
                'level' => input('level', ''),
                'address' => input('address', ''),
                'manager' => input('manager', ''),
                'contacts' => input('contacts', ''),
                'telephone' => input('telephone', ''),
                'phone' => input('phone', ''),
                'email' => input('email', ''),
                'qq' => input('qq', ''),
                'status' => (int)input('status', 0)
            ];

            $p = trim(input('password', ''));
            if (!$p == null || !$p == "") {
                $data['password'] = md5($p);
            }
            $agent_id = (int)input('agent_id');

            $condition[] = ['agent_id', '=', $agent_id];
            $res = $agent_model->updAgent($data, $condition);
            return $res;

        } else {
            $agent_id = 2;//input('agent_id');

            $agent = $agent_model->editAgent($agent_id);

            $this->assign('agent_info', $agent);


            return $this->fetch('agent/edit_agent');
        }

    }


    /**
     * 代理商级别页面
     */

    public function agent_level()
    {
        return view();
    }

    /**
     * 代理商级别列表数据渲染
     */

    public function agent_level_list()
    {

        $page = isset($this->params['page']) ? $this->params['page'] : 1;

        $page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;

        $condition = [];

        $order = 'id desc';

        $field = '*';

        $agent = new AgentModel();
        return $agent->getAgentLevelPageList($condition, $page, $page_size, $order, $field);
    }

    /**
     * 代理商级别添加
     */
    public function agent_level_add()
    {
        $agent_level = new AgentModel();
        if (request()->isAjax()) {
            $data = [
                'title' => input('title', ''),
                'ratio' => input('ratio', ''),
                'user_number' => input('user_number', '')
            ];
            $res = $agent_level->addAgentLevel($data);
            return $res;
        } else {
            return view('add_agent_level');
        }
    }

    /**
     * 代理商级别修改
     */
    public function agent_level_edit()
    {
        $agent_model = new AgentModel();
        if (request()->isAjax()) {
            $data = [
                'title' => input('title', ''),
                'ratio' => input('ratio', ''),
                'user_number' => input('user_number', '')
            ];
            $id = input('id', '');
            $condition[] = ['id', '=', $id];
            $res = $agent_model->updAgentLevel($data, $condition);
            return $res;
        } else {

            $agent_level_id = 1;//input('agent_id');

            $agent = $agent_model->editAgentLevel($agent_level_id);

            $this->assign('agent_level_info', $agent);


            return $this->fetch('agent/edit_agent_level');
        }
    }

    public  function  agent_power(){
        $menu=new \app\model\system\Menu();
        //$data=$menu->menuTree();
        $res = $menu->getMenuList([[ 'app_module', "=", 'saas' ], [ 'is_show', "=", 1 ], [ 'parent', '=', '' ] ], '*', 'sort asc');
        //halt($res);
        $tree = list_to_tree($res['data'], 'id', 'parent', 'child_list');
//
//        return view('agent/agent_power');
    }

}