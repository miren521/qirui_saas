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
