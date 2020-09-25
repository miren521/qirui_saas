<?php

/**
 * Adv.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */

namespace app\api\controller;

use app\model\web\AdvPosition as AdvPositionModel;
use app\model\web\Adv as AdvModel;

class Adv extends BaseApi
{

    /**
     * 详情信息
     */
    public function detail()
    {
        $keyword = isset($this->params['keyword']) ? $this->params['keyword'] : '';
        if (empty($keyword)) {
            return $this->response($this->error('', 'REQUEST_KEYWORD'));
        }
        $adv_position_model = new AdvPositionModel();
        $adv_model = new AdvModel();
        $info = $adv_position_model->getAdvPositionInfo([['keyword', '=', $keyword]]);
        $info = $info['data'];

        $res = ['adv_position' => $info];

        $list = $adv_model->getAdvList(
            [['ap_id', '=', $info['ap_id']]],
            $field = 'adv_id, adv_title, ap_id, adv_url, adv_image, slide_sort, price, background'
        );
        $list = $list['data'];

        $res['adv_list'] = $list;
        return $this->response($this->success($res));
    }
}
