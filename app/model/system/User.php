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


namespace app\model\system;

use think\facade\Session;
use app\model\BaseModel;

/**
 * 管理员模型
 */
class User extends BaseModel
{
    /*******************************************************************用户 编辑查询 start*****************************************************/

    /**
     * 添加用户
     * @param $data
     */
    public function addUser($data)
    {

        $site_id = isset($data[ 'site_id' ]) ? $data[ 'site_id' ] : '';
        $app_module = isset($data[ 'app_module' ]) ? $data[ 'app_module' ] : '';
        $member_id = isset($data[ 'member_id' ]) ? $data[ 'member_id' ] : 0;
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        if ($app_module === '') {
            return $this->error('', 'REQUEST_APP_MODULE');
        }

        //判断 用户名 是否存在
        $user_info = model('user')->getInfo([['username', "=", $data[ "username" ]], ["app_module", "=", $data[ "app_module" ]]]);
        if (!empty($user_info)) {
            if (data_md5($data[ "password" ]) == $user_info[ 'password' ]) {
                return $this->success($user_info);
            } else {
                return $this->error('', '账号已存在');
            }
        }
        if ($member_id > 0) {
            $temp_condition = array(
                "app_module" => $data[ "app_module" ],
                "member_id" => $member_id
            );
            $temp_count = model('user')->getCount($temp_condition, 'uid');
            if ($temp_count > 0) {
                return $this->error('', 'USERNAME_EXISTED');
            }
        }

        $group_id = isset($data[ 'group_id' ]) ? $data[ 'group_id' ] : 0;
        if ($group_id > 0) {
            $group_model = new Group();
            $group_info_result = $group_model->getGroupInfo([["group_id", "=", $group_id], ["site_id", "=", $site_id], ["app_module", "=", $app_module]], "group_name");
            $group_info = $group_info_result[ "data" ];
            $data[ "group_name" ] = $group_info[ "group_name" ];
        }

        $data[ "password" ] = data_md5($data[ "password" ]);
        $data[ "create_time" ] = time();
        $result = model("user")->add($data);
        if ($result === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }

        $user_info = model('user')->getInfo([['uid', "=", $result], ["app_module", "=", $app_module]]);
        return $this->success($user_info);
    }

    /**
     * 编辑用户
     * @param $data
     * @param $condition
     */
    public function editUser($data, $condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition[ 'site_id' ]) ? $check_condition[ 'site_id' ] : '';
        $app_module = isset($check_condition[ 'app_module' ]) ? $check_condition[ 'app_module' ] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        if ($app_module === '') {
            return $this->error('', 'REQUEST_APP_MODULE');
        }
        $group_id = isset($data[ 'group_id' ]) ? $data[ 'group_id' ] : 0;
        if ($group_id > 0) {
            $group_model = new Group();
            $group_info_result = $group_model->getGroupInfo([["group_id", "=", $group_id], ["site_id", "=", $site_id], ["app_module", "=", $app_module]], "group_name");
            $group_info = $group_info_result[ "data" ];
            $data[ "group_name" ] = $group_info[ "group_name" ];
        }
        $res = model("user")->update($data, $condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 编辑用户状态
     * @param $status
     * @param $condition
     */
    public function modifyUserStatus($status, $condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition[ 'site_id' ]) ? $check_condition[ 'site_id' ] : '';
        $app_module = isset($check_condition[ 'app_module' ]) ? $check_condition[ 'app_module' ] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }
        if ($app_module === '') {
            return $this->error('', 'REQUEST_APP_MODULE');
        }
        $data = array(
            "status" => $status,
            "update_time" => time()
        );
        $res = model('user')->update($data, $condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 重置密码
     * @param $password
     * @param $condition
     * @return array|\multitype
     */
    public function modifyUserPassword($password, $condition)
    {
        $res = model('user')->update(['password' => data_md5($password)], $condition);
        if ($res === false) {
            return $this->error('', 'RESULT_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 系统用户修改密码
     *
     * @param unknown $uid
     * @param unknown $old_password
     * @param unknown $new_password
     */
    public function modifyAdminUserPassword($uid, $old_password, $new_password)
    {
        $condition = array(
            'uid' => $uid,
            'password' => data_md5($old_password)
        );
        $res = model('user')->getInfo($condition, "uid");
        if (!empty($res[ 'uid' ])) {
            $data = array(
                'password' => data_md5($new_password)
            );
            $res = model('user')->update($data, [
                'uid' => $uid
            ]);
            return $this->success($res, 'SUCCESS');
        } else {
            return $this->error('', 'PASSWORD_ERROR');
        }
    }

    /**
     * 删除用户
     * @param array $condition
     * @return multitype:string mixed
     */
    public function deleteUser($condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $app_module = isset($check_condition[ 'app_module' ]) ? $check_condition[ 'app_module' ] : '';
        if ($app_module === '') {
            return $this->error('', 'REQUEST_APP_MODULE');
        }

        $res = model('user')->delete($condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $this->success($res);
    }


    /**
     * 获取用户信息
     * @param $condition
     * @param string $field
     * @return \multitype
     */
    public function getUserInfo($condition, $field = "uid, app_module, site_id, group_id, username, member_id, create_time, update_time, status, login_time, login_ip")
    {
        $info = model('user')->getInfo($condition, $field);
        return $this->success($info);
    }


    /**
     * 获取用户列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return multitype:string mixed
     */
    public function getUserList($condition = [], $field = 'uid, app_module, site_id, group_id, username, member_id, create_time, update_time, status, login_time, login_ip, is_admin, group_name', $order = '', $limit = null)
    {
        $list = model('user')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }


    /**
     * 获取会员分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     * @return multitype:string mixed
     */
    public function getUserPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = 'uid, app_module, site_id, group_id, username, member_id, create_time, update_time, status, login_time, login_ip, is_admin, group_name')
    {
        $list = model('user')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 获取站点用户分页列表
     * @param unknown $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     */
    public function getSiteUserPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
    {
        $field = ' nu.uid, nu.app_module, nu.app_group,
            nu.is_admin, nu.site_id, nu.group_id, nu.group_name, nu.username, nu.member_id, nu.create_time, 
            nu.update_time, nu.status, nu.login_time, nu.login_ip, ns.site_name, ns.is_own, ns.level_name, ns.category_name';
        $alias = 'nu';
        $join = [
            [
                'shop ns',
                'nu.site_id = ns.site_id',
                'left'
            ],
        ];
        $list = model("user")->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }

    /**
     * 检测权限
     * @param unknown $app_module
     * @param unknown $group_info
     * @param unknown $url
     */
    public function checkAuth($url, $app_module, $group_info)
    {
        if ($group_info[ 'is_system' ] == 1) {
            return true;
        }
        $menu_model = new Menu();
        $menu_info = $menu_model->getMenuInfoByUrl($url, $app_module);
        if (!empty($menu_info[ 'data' ])) {
            //权限组
            if (empty($group_info)) {
                return false;
            }
            if (strpos(',' . $group_info[ 'menu_array' ] . ',', ',' . $menu_info[ 'data' ][ 'name' ] . ',') !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * 回去会员总数
     * @return mixed
     */
    public function getMemberTotalCount()
    {
        $res = model('member')->getCount();
        return $this->success($res);
    }


    /*******************************************************************用户 编辑查询 end*****************************************************/

    /*******************************************************************用户注册登录 start*****************************************************/

    /**
     * 用户登录
     * @param unknown $mobile
     * @param unknown $password
     */
    public function login($username, $password, $app_module)
    {
        $time = time();
        // 验证参数 预留
        $user_info = model('user')->getInfo([['username', "=", $username], ["app_module", "=", $app_module]]);

        if (empty($user_info)) {
            return $this->error('', 'USER_LOGIN_ERROR');
        } else if (data_md5($password) !== $user_info[ 'password' ]) {
            return $this->error([], 'PASSWORD_ERROR');
        } else if ($user_info[ 'status' ] !== 1) {
            return $this->error([], 'USER_IS_LOCKED');
        }

        // 记录登录SESSION
        $auth = array(
            'uid' => $user_info[ 'uid' ],
            'username' => $user_info[ 'username' ],
            'member_id' => $user_info[ 'member_id' ],
            'create_time' => $user_info[ 'create_time' ],
            'status' => $user_info[ 'status' ],
            'group_id' => $user_info[ "group_id" ],
            'site_id' => $user_info[ "site_id" ],
            'app_group' => $user_info[ "app_group" ],
            'login_time' => $time,
            'login_ip' => request()->ip()
        );

        //更新登录记录
        $data = [
            'login_time' => time(),
            'login_ip' => request()->ip(),
        ];
        model('user')->update($data, [['uid', "=", $user_info[ 'uid' ]]]);
        //填写日志
        Session::set($app_module . "." . "uid", $user_info[ 'uid' ]);
        Session::set($app_module . "." . "user_info", $auth);
        $this->addUserLog($user_info[ 'uid' ], $user_info[ 'username' ], $user_info[ 'site_id' ], "用户登录", []); //添加日志
        return $this->success();
    }

    /**
     * 用户登录
     * @param unknown $mobile
     * @param unknown $password
     */
    public function appLogin($username, $password, $app_module)
    {
        $time = time();
        // 验证参数 预留
        $user_info = model('user')->getInfo([['username', "=", $username], ["app_module", "=", $app_module]]);
        if (empty($user_info)) {
            return $this->error('', 'USER_LOGIN_ERROR');
        } else if (data_md5($password) !== $user_info[ 'password' ]) {
            return $this->error([], 'PASSWORD_ERROR');
        } else if ($user_info[ 'status' ] !== 1) {
            return $this->error([], 'USER_IS_LOCKED');
        }

        //更新登录记录
        $data = [
            'login_time' => $time,
            'login_ip' => request()->ip(),
        ];
        model('user')->update($data, [['uid', "=", $user_info[ 'uid' ]]]);

        $this->addUserLog($user_info[ 'uid' ], $user_info[ 'username' ], $user_info[ 'site_id' ], "用户登录", []); //添加日志

        return $this->success($user_info);
    }

    /**
     * 模拟登录
     * @param $username
     * @param $app_module
     * @param int $type 登录类型
     * @return array
     */
    public function simulatedLogin($username, $app_module, $type = 'pc')
    {
        // 验证参数 预留
        $user_info = model('user')->getInfo([['username', "=", $username], ["app_module", "=", $app_module]]);
        if (empty($user_info)) {
            return $this->error('', 'USER_LOGIN_ERROR');
        } else if ($user_info[ 'status' ] !== 1) {
            return $this->error([], 'USER_IS_LOCKED');
        }

        if ($type == 'pc') {
            // 记录登录SESSION
            $auth = array(
                'uid' => $user_info[ 'uid' ],
                'username' => $user_info[ 'username' ],
                'member_id' => $user_info[ 'member_id' ],
                'create_time' => $user_info[ 'create_time' ],
                'status' => $user_info[ 'status' ],
                'group_id' => $user_info[ "group_id" ],
                'site_id' => $user_info[ "site_id" ],
                'app_group' => $user_info[ "app_group" ],
            );
            //填写日志
            Session::set($app_module . "." . "uid", $user_info[ 'uid' ]);
            Session::set($app_module . "." . "user_info", $auth);
            $this->addUserLog($user_info[ 'uid' ], $user_info[ 'username' ], $user_info[ 'site_id' ], "用户登录", []); //添加日志
            return $this->success();
        } else {
            return $this->success($user_info);
        }
    }

    /**
     * 获取当前登录uid
     * @param $app_module
     * @return mixed
     */
    public function uid($app_module)
    {
        return Session::get($app_module . "." . "uid");
    }

    /**
     * 获取当前登录管理员信息
     * @param $app_module
     * @return mixed
     */
    public function userInfo($app_module)
    {
        return Session::get($app_module . "." . "user_info");
    }

    /**
     * 清除登录信息
     */
    public function clearLogin($app_module)
    {
        Session::delete($app_module);
    }
    /*******************************************************************用户注册登录 end*****************************************************/

    /*******************************************************************用户日志 start*****************************************************/

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

    /**
     * 删除用户日志
     */
    public function deleteUserLog($condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $site_id = isset($check_condition[ 'site_id' ]) ? $check_condition[ 'site_id' ] : '';
        if ($site_id === '') {
            return $this->error('', 'REQUEST_SITE_ID');
        }

        $res = model("user_log")->delete($condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $this->success($res);
    }

    /**
     * 获用户员日志分页列表
     *
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     * @return multitype:string mixed
     */
    public function getUserLogPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = 'username, site_id, url, id, uid, data, ip, action_name, create_time')
    {
        $list = model('user_log')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    /*******************************************************************用户日志 end*****************************************************/
}
