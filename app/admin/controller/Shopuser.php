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


namespace app\admin\controller;

use app\model\system\User;
use app\model\shop\ShopApply;
/**
 * 店铺用户控制器
 * @author Administrator
 *
 */
class Shopuser extends BaseAdmin
{
    /**
     * 用户列表
     */
    public function lists(){
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $condition = [];
            $condition[] = ["nu.app_module", "=", 'shop'];
            $status = input('status', 'all');
            $search_keys = input('search_keys', "");
            $site_id = input("site_id", "");
            if (!empty($search_keys)) {
                $condition[] = ['nu.username', 'like', '%' . $search_keys . '%' ];
            }
            if($status != "all" && $status != ""){
                $condition[] = ["nu.status", "=", $status];
            }
            if($site_id != "")
            {
                $condition[] = ["nu.site_id", "=", $site_id];
            }
            $user_model = new User();
            $list = $user_model->getSiteUserPageList($condition, $page, $page_size, "nu.create_time desc");
            return $list;
        }else{
            return $this->fetch('shopuser/lists');
        }
    }
    /**
     * 重置密码
     */
    public function modifyPassword()
    {
        if(request()->isAjax()){
            $password = input('password', '123456');
            $uid = input('uid', 0);
            $user_model = new User();
            return $user_model->modifyUserPassword($password, [['uid', '=', $uid]]);
        }
    }
    
    /**
     * 删除用户
     */
    public function deleteUser(){
        if (request()->isAjax()) {
            $uid = input("uid", 0);
            //查询是否有正在申请中的店铺
            $shop_apply_model = new ShopApply();
            $shop_apply_info = $shop_apply_model->getApplyInfo(['uid' => $uid]);
            if (!empty($shop_apply_info['data'])) {
                return error(0, '该用户正在申请店铺中，不可删除');exit;
            }
            $user_model = new User();
            $condition = array(
                ["uid", "=",$uid],
                ["app_module", "=",'shop'],
            );
            $result = $user_model->deleteUser($condition);
            return $result;
        }
    }
    
}