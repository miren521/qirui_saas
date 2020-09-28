<?php
declare (strict_types = 1);

namespace app\saas\controller;

use think\Request;
use app\model\saas\Combo as ComboModel;

class Combo extends BaseSaas
{
    /**
     * 基础方法
     * User: mi
     * Date: 2020-09-25
     */
    public function index(){
        if (request()->isAjax()) {

        } else {
            return $this->fetch('combo/index');
        }
    }

    /**
     * 套餐设置，添加
     * User: mi
     * Date: 2020-09-27
     */
    public function add(){
        if (request()->isPost()) {
            $param = $_POST;
            $res = ComboModel::create($param);
            if (!$res) error(500, '操作失败');
            success(200, '操作成功');
        } else {
            error(500, '非法请求');
        }
    }

    /**
     * 套餐设置，编辑
     * User: mi
     * Date: 2020-09-27
     */
    public function edit(){
        if (request()->isPost()) {
            $param = $_POST;
            $res = ComboModel::update($param);
            if (!$res) error(500, '操作失败');
            success(200, '操作成功');
        } else {
            error(500, '非法请求');
        }
    }

    /**
     *
     * User: mi
     * Date: 2020-09-27
     */
    public function del($id){
        if (request()->isGet()) {
            $res = ComboModel::find($id);
            if (!$res) error(500, '操作失败');

            if (!$res->delete()) error(500, '操作失败');
            success(200, '操作成功');
        } else {
            error(500, '非法请求');
        }
    }

}
