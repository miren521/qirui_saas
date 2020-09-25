<?php

/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2019-2029 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */
namespace addon\servicer\admin\controller;

use app\admin\controller\BaseAdmin;
use app\model\system\User;
use app\model\system\Group;

/**
 * 聊天记录查询
 */
class Servicer extends BaseAdmin
{
    /**
     * 客服列表
     */
    public function index()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $status = input('status', '');
            $search_keys = input('search_keys', "");
        
            $condition = [];
            $condition[] = ["site_id", "=", 0];
            $condition[] = ["app_module", "=", 'servicer'];
            if (!empty($search_keys)) {
                $condition[] = ['username', 'like', '%' . $search_keys . '%' ];
            }
            if ($status != "") {
                $condition["status"] = ["status", "=", $status];
            }
        
            $user_model = new User();
            $list = $user_model->getUserPageList($condition, $page, $page_size, "create_time desc");
            return $list;
        }
        return $this->fetch('servicer/list');
    }
    
    /**
     * 添加客服
     */
    public function add()
    {
        if (request()->isAjax()) {
            $username = trim(input("username", ""));
            $password = trim(input("password", ""));
            $group_id = trim(input("group_id", ""));
            $user_model = new User();
            $data = [
                "username" => $username,
                "password" => $password,
                "group_id" => $group_id,
                "app_module" => 'servicer',
                "site_id" => 0
            ];
            $result = $user_model->addUser($data);
            return $result;
        }
        $group_model = new Group();
        $group_list_result = $group_model->getGroupList([ [ "site_id", "=", 0 ], [ "app_module", "=", 'servicer' ] ]);
        $group_list = $group_list_result["data"];
        $this->assign("group_list", $group_list);
        return $this->fetch('servicer/add');
    }
    
    /**
     * 编辑客服
     */
    public function edit()
    {
        $uid = input("uid", 0);
        $user_model = new User();
        if (request()->isAjax()) {
            $group_id = input("group_id", "");
            $status = input("status", "");
            $condition = [
                [ "uid", "=", $uid ],
                [ "site_id", "=", 0 ],
                [ "app_module", "=", 'servicer' ],
            ];
            $data = [
                "group_id" => $group_id,
                "status" => $status
            ];
            $result = $user_model->editUser($data, $condition);
            return $result;
        }
        $condition = [
            [ "uid", "=", $uid ],
            [ "site_id", "=", 0 ],
            [ "app_module", "=", 'servicer' ],
        ];
        $user_info_result = $user_model->getUserInfo($condition);
        $user_info = $user_info_result["data"];
        $this->assign("user_info", $user_info);
        $this->assign("uid", $uid);
        $group_model = new Group();
        $group_list_result = $group_model->getGroupList([["site_id", "=", 0], ["app_module", "=", 'servicer']]);
        $group_list = $group_list_result["data"];
        $this->assign("group_list", $group_list);
        return $this->fetch('servicer/edit');
    }
    
    /**
     * 修改用户状态
     * @return mixed
     */
    public function modifyUserStatus()
    {
        if (request()->isAjax()) {
            $uid = input('uid', 0);
            $status = input('status', 0);
            $user_model = new User();
            $condition = [
                [ "uid", "=", $uid ],
                [ "site_id", "=", 0 ],
                [ "app_module", "=", 'servicer' ],
            ];
            $result = $user_model->modifyUserStatus($status, $condition);
            return $result;
        }
    }
    
    /**
     * 重置密码
     */
    public function modifyPassword()
    {
        if (request()->isAjax()) {
            $password = trim(input('password', '123456'));
            $uid = input('uid', 0);
            $site_id = 0;
            $user_model = new User();
            return $user_model->modifyUserPassword($password, [ [ 'uid', '=', $uid ], [ 'site_id', '=', $site_id ] ]);
        }
    }
    
    /**
     * 删除客服
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $uid = input("uid", 0);
            $user_model = new User();
            $condition = [
                [ "uid", "=", $uid ],
                [ "app_module", "=", 'servicer' ],
                [ "site_id", "=", 0 ],
            ];
            $result = $user_model->deleteUser($condition);
            return $result;
        }
    }
}