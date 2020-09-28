<?php
/**
 * 客户管理
 * 刘兴豪
 */

namespace app\saas\controller;
use app\Controller;
use app\model\BaseModel;
use app\model\saas\SaasCustomer;
use app\saas\validate\CustomeCheck;
use think\facade\Db;
use think\facade\Session;
use think\facade\View;


class Customer extends BaseModel
{
    protected $app_module = 'saas';

    private $uid;
    private $user_info;

    function __construct()
    {
        $this->uid = Session::get($this->app_module.'.'.'uid');
        $this->user_info = Session::get($this->app_module.'.'.'user_info');
    }

    /**
     * 新增客户
     */
    public function addition()
    {
        if (request()->post()){
            $org_data = input();
            $customeCheck = new CustomeCheck();
            if (!$customeCheck->check($org_data)){
                $this->error($customeCheck->getError());
            }
            $org_data['passwd'] = md5($org_data['passwd']);
            $org_data['opening_time'] = date('Y-m-d H:i:s',time());
            $org_data['deputy'] = $this->uid;
            $res = Db::table('saas_customer')->insert($org_data);
            if ($res){
                return $this->success($org_data);
            }else{
                return $this->error('新增失败');
            }
        }else{
            return $this->error('请求方式有误');
        }
    }

    /**
     * 直接客户
     */
    public function direct(){
        $condition = [];
        $status = input('status','*');//获取状态（0：启用，1：停用，*：所有）
        $deputy = input('deputy','*');//获取我的客户（*：所有，0：我的客户）
        //拼接条件
        $condition .= $status==='*' ? ['status','>=',0] : ['status','=',$status];
        $condition .= $deputy==='0' ? ['deputy','>=',0] : ['deputy','=',$this->uid];
        $res = Db::table('saas_customer')
            ->field('id,account,opening_time,level,expiration_time,mobile,deputy,network_sale,status')
            ->where($condition)
            ->select();
        View::assign('direct',$res);
        View::fetch('/customer/direct');
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
        if (request()->post()){
            $org_data = input();
            $customeCheck = new CustomeCheck();
            if (!$customeCheck->check($org_data)){
                $this->result([],428,$customeCheck->getError(),'json');
            }
            $org_data['passwd'] = md5($org_data['passwd']);
            $org_data['opening_time'] = date('Y-m-d H:i:s',time());
            $org_data['deputy'] = $this->uid;
            $res = Db::table('saas_customer')->insert($org_data);
            if ($res){
                return $this->success($org_data);
            }else{
                return '新增失败';
            }
        }else{
            return json('请求方式有误',404);
        }
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