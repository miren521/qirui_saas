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

declare(strict_types=1);

namespace addon\servicer\shop\controller;

use addon\servicer\model\Dialogue;
use app\shop\controller\BaseShop;

/**
 * 聊天记录查询
 */
class Query extends BaseShop
{
    /**
     * 聊天记录查询页面
     *
     * @return mixed
     */
    public function index()
    {
        return $this->fetch('query/index');
    }

    /**
     * 获取聊天记录
     *
     * @return mixed
     */
    public function getChatlogs()
    {
        // 当前请求为Ajax获取数据请求，执行获取数据逻辑
        if (request()->isAjax()) {
            $memberId = input('member_id', 0);
            $page = input('member_id', 0);
            $limit = input('member_id', 0);

            $dialogueList = (new Dialogue())->getDialogueList($memberId, $page, $limit);

            return success(0, '请求成功', $dialogueList);
        }

        return error();
    }
}
