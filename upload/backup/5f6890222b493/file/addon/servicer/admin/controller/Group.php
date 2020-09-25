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
use app\model\system\Group as GroupModel;

/**
 * 客服分组
 */
class Group extends BaseAdmin
{
    /**
     * 客服分组
     */
    public function index()
    {
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $search_keys = input('search_keys', "");

            $condition = [
                ['site_id', "=", 0],
                ["app_module", "=", 'servicer']
            ];
            if (!empty($search_keys)) {
                $condition[] = ['desc', 'like', '%' . $search_keys . '%'];
            }

            $group_model = new GroupModel();
            $list = $group_model->getGroupPageList($condition, $page, $page_size);
            return $list;
        }
        return $this->fetch("group/list");
    }

    /**
     *添加分组
     */
    public function add()
    {
        if (request()->isAjax()) {
            $group_name = input('group_name', '');
            $desc = input('desc', '');
            $group_model = new GroupModel();
            $data = [
                "group_name" => $group_name,
                "site_id" => 0,
                "app_module" => 'servicer',
                "group_status" => 1,
                "menu_array" => '',
                "desc" => $desc,
                "is_system" => 0
            ];
            $result = $group_model->addGroup($data);
            return $result;
        }
        return $this->fetch('group/add');
    }

    /**
     * 编辑分组
     */
    public function edit()
    {
        if (request()->isAjax()) {
            $group_name = input('group_name', '');
            $group_id = input('group_id', 0);
            $desc = input('desc', '');

            $data = [
                "group_name" => $group_name,
                "desc" => $desc,
            ];
            $condition = [
                ["group_id", "=", $group_id],
                ["site_id", "=", 0],
                ["app_module", "=", 'servicer']
            ];
            $group_model = new GroupModel();
            $result = $group_model->editGroup($data, $condition);
            return $result;
        }
        $group_model = new GroupModel();
        $group_id = input('group_id', 0);
        $condition = [
            ["group_id", "=", $group_id],
            ["site_id", "=", 0],
            ["app_module", "=", 'servicer']
        ];
        $group_info_result = $group_model->getGroupInfo($condition);
        $group_info = $group_info_result["data"];
        $this->assign("group_info", $group_info);
        $this->assign("group_id", $group_id);

        return $this->fetch('group/edit');
    }

    /**
     * 删除分组
     */
    public function delete()
    {
        if (request()->isAjax()) {
            $group_id = input('group_id', '');
            $condition = [
                ["group_id", "=", $group_id],
                ["site_id", "=", 0],
                ["app_module", "=", 'servicer'],
            ];
            $group_model = new GroupModel();
            $result = $group_model->deleteGroup($condition);
            return $result;
        }
    }
}
