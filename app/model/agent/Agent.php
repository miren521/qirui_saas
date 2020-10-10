<?php


namespace app\model\agent;


use app\model\BaseModel;
use app\model\saas\AuthGroup;
use think\facade\Session;

class Agent extends BaseModel
{

    /**
     * 获取代理列表
     */
    public function getAgentPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*', $alias = '', $join = [])

    {

        $list = model('agent')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);

        return $this->success($list);

    }


    /**
     * 代理商添加
     */

    public function addAgent($data, $agent_id)
    {
        $where['id'] = $agent_id;

        model('agent')->startTrans();
        $agent_info = model('agent')->getInfo($where, '*');

        $data['pid'] = $agent_info['id'];
        $data['gid'] = $agent_info['pid'];
        try {

            $agent_count = model('agent')->getCount([['number', '=', $data['number']]]);
            if ($agent_count > 0) {
                return $this->error('', '该账户已被使用,请重新输入!');
            }
            model('agent')->add($data);
            model('agent')->commit();

            return $this->success();

        } catch (Exception $e) {
            model('agent')->rollback();

            return $this->error('', $e->getMessage());
        }
    }

    /***
     * 代理商编辑数据查询
     */

    public function editAgent($agent_id)

    {
        $where['id'] = $agent_id;

        $res = model('agent')->getInfo($where, '*');

        return $this->success($res);

    }

    /**
     * 代理商充值
     */

    public function getAgent($where = [], $field = true, $alias = 'a', $join = null, $data = null)
    {

        $list = model('agent')->getInfo($where, $field, $alias, $join);
        return $list;
    }

    /**
     * 代理商修改
     */
    public function updAgent($data, $condition)
    {

        $res = model('agent')->update($data, $condition);
        return $this->success($res);
    }


    /**
     * 获取代理等级列表
     */
    public function getAgentLevelPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')

    {

        $list = model('auth_group')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);

    }

    /**
     * 代理商级别添加
     */
    public function addAgentLevel($data)
    {

        $res = model('auth_group')->add($data);

        return $res;
    }

    /**
     * 代理商级别单条数据查询
     */
    public function editAgentLevel($agent_level_id)
    {
        $where['id'] = $agent_level_id;

        $res = model('auth_group')->getInfo($where, '*');

        return $this->success($res);
    }

    /**
     * 代理商修改
     */
    public function updAgentLevel($data, $condition)
    {

        $res = model('auth_group')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 用户登录
     * @param unknown $mobile
     * @param unknown $password
     */
    public function login($username, $password, $app_module)
    {
        $time = time();
        // 验证参数 预留
        $user_info = model('agent')->getInfo([['number', "=", $username]]);

        if (empty($user_info)) {
            return $this->error('', 'USER_LOGIN_ERROR');
        } else if (data_md5($password) !== $user_info['password']) {
            return $this->error([], 'PASSWORD_ERROR');
        } else if ($user_info['status'] !== 1) {
            return $this->error([], 'USER_IS_LOCKED');
        }

        // 记录登录SESSION
        $auth = array(
            'is_admin' => $user_info['is_admin'],
            'id' => $user_info['id'],
            'username' => $user_info['number'],
            'power' => $user_info['power'],
            'create_time' => $user_info['create_time'],
            'status' => $user_info['status'],
            'level' => $user_info['level'],
            'pid' => $user_info['pid'],
            'gid' => $user_info['gid'],
            'login_time' => $time,
            'login_ip' => request()->ip()
        );

        //更新登录记录
        $data = [
            'login_time' => time(),
            'login_ip' => request()->ip(),
        ];
        model('agent')->update($data, [['id', "=", $user_info['id']]]);

        // 查询权限列表
        $rules = AuthGroup::where('id', $user_info['level'])
            ->value('rules');
        $auth['rules'] = $rules;

        //填写日志
        Session::set($app_module . "." . "uid", $user_info['id']);
        Session::set($app_module . "." . "user_info", $auth);
        $this->addUserLog($user_info['id'], $user_info['number'], $user_info['id'], "用户登录", []); //添加日志
        return $this->success();
    }

    /**
     * 添加用户日志
     * @param $data
     */
    public function addUserLog($uid, $username, $site_id, $action_name, $data = [])
    {

        $url = request()->parseUrl();
        $ip = request()->ip();
        $log = array(
            "uid" => $uid,
            "username" => $username,
            "site_id" => $site_id,
            "url" => $url,
            "ip" => $ip,
            "data" => json_encode($data),
            "action_name" => $action_name,
            "create_time" => time(),
        );
        $res = model("user_log")->add($log);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $this->success($res);
    }


}