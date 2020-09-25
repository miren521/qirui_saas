<?php
/**
 * 客户管理
 * 刘兴豪
 */

namespace app\saas\controller;
use app\Controller;
use app\model\saas\SaasCustomer;
use app\saas\validate\CustomeCheck;
use think\facade\Db;
use think\facade\View;


class Customer extends Controller
{
    /**
     * 新增客户
     */
    public function addition()
    {
        if (request()->post()){
            $org_data = input();
            $customeCheck = new CustomeCheck();
            if (!$customeCheck->check($org_data)){
                $this->result([],428,$customeCheck->getError(),'json');
            }
            $org_data['passwd'] = md5($org_data['passwd']);
            $org_data['opening_time'] = date('Y-m-d H:i:s',time());
            $res = Db::table('saas_customer')->insert($org_data);
            if ($res){
                return json($org_data);
            }else{
                return '新增失败';
            }
        }else{
            return json('请求方式有误',404);
        }
    }

    /**
     * 直接客户
     */
    public function direct(){
        $res = Db::table('saas_customer')->field('id,account,passwd,mobile')->select();
        View::assign($res);
        View::fetch();
    }

    /**
     * 间接客户
     */
    public function indirect(){

    }

    /**
     * 我的客户
     */
    public function mine(){

    }

    /**
     * 停用客户
     */
    public function disable(){

    }

    /**
     * 修改客户信息
     */
    public function amend(){

    }

    /**
     * 修改客户信息
     */
    public function remove(){

    }

    /**
     * 停用启用客户
     */
    public function stopEnabled(){
        $id = input('id',0);
    }

    /**
     * 升级续费
     */
    public function upgradeRenewal(){

    }
}