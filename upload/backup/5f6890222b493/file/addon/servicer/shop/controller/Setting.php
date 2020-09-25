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
 
namespace addon\servicer\shop\controller;

use addon\servicer\model\Servicer;
use app\shop\controller\BaseShop;

/**
 * 客服设置控制器
 */
class Setting extends BaseShop
{
    /**
     * 客服设置页面
     *
     * @return mixed
     */
    public function index()
    {
        $servicerModel = new Servicer();
        $list = $servicerModel->getServicerList();

        return $this->fetch('setting/index');
    }

    /**
     * 添加客服
     *
     * @return string
     */
    public function addServicer()
    {
        if (request()->isAjax()) {
            $nickName = input('nick_name', '');
            $userId = input('user_id', 0);
            $type = input('type', -1);
            $headImg = input('head_img', '');

            if (empty($nickName)) {
                return error(-1, '客服昵称不能空');
            }
            if (empty($userId)) {
                return error(-1, '归属用户不能空');
            }
            if ($type == -1) {
                return error(-1, '客服类型不能空');
            }

            $servicerModel = new Servicer();
            $servicer = $servicerModel->createServicer($nickName, 1, $type, $userId, $headImg);

            if (empty($servicer)) {
                $servicerModel->error();
            }
            return $servicerModel->success($servicer);
        }

        return error();
    }

    /**
     * 删除客服
     *
     * @return string
     */
    public function delServicer()
    {
        $servicer_id = input('servicer_id', 0);

        if (empty($servicer_id)) {
            return error(-1, '参数不合法');
        }

        return (new Servicer())->delServicer($servicer_id);
    }

    /**
     * 修改用户头像
     *
     * @return string
     */
    public function setHead()
    {
        // TODO: 明确是否有现成的上传方案
    }
}
