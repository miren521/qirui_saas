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

namespace addon\bargain\api\controller;

use addon\bargain\model\Poster;
use app\api\controller\BaseApi;
use addon\bargain\model\Bargain as BargainModel;
use app\model\goods\GoodsService;
use app\model\shop\Shop as ShopModel;
use app\model\goods\Goods as GoodsModel;
/**
 * 砍价商品
 */
class Goods extends BaseApi
{
    /**
     * 商品详情
     */
    public function detail(){
        $id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;
        if (empty($id)) {
            return $this->response($this->error('', 'REQUEST_ID'));
        }

        $bargain = new BargainModel();
        $condition = [
            ['pbg.id', '=', $id],
            ['pbg.status', '=', 1]
        ];
        $goods_sku_detail = $bargain->getBargainGoodsDetail($condition);
        $goods_sku_detail = $goods_sku_detail[ 'data' ];
        $res[ 'goods_sku_detail' ] = $goods_sku_detail;

        if (empty($goods_sku_detail)) return $this->response($this->error($res));

        $goods_model = new GoodsModel();
        $goods_info = $goods_model->getGoodsInfo([['goods_id', '=', $goods_sku_detail['goods_id']]])['data'] ?? [];
        if (empty($goods_info)) return $this->response($this->error([], '找不到商品'));
        $res[ 'goods_info' ] = $goods_info;

        $token = $this->checkToken();
        if ($token['code'] == 0) {
            $launch_info = $bargain->getBargainLaunchDetail([
                ['bargain_id', '=', $goods_sku_detail['bargain_id'] ],
                ['sku_id', '=', $goods_sku_detail['sku_id'] ],
                ['member_id', '=', $this->member_id ],
                ['status', '=', 0 ]
            ], 'launch_id');
            if (!empty($launch_info['data'])) $res[ 'goods_sku_detail' ][ 'launch_info' ] = $launch_info['data'];
        }

        //店铺信息
        $shop_model = new ShopModel();
        $shop_info = $shop_model->getShopInfo([ [ 'site_id', '=', $goods_sku_detail['site_id'] ] ], 'site_id,site_name,is_own,logo,avatar,banner,seo_description,qq,ww,telephone,shop_desccredit,shop_servicecredit,shop_deliverycredit,shop_baozh,shop_baozhopen,shop_baozhrmb,shop_qtian,shop_zhping,shop_erxiaoshi,shop_tuihuo,shop_shiyong,shop_shiti,shop_xiaoxie,shop_sales,sub_num');
        $shop_info = $shop_info['data'];
        $res['shop_info'] = $shop_info;

        return $this->response($this->success($res));
    }

    /**
     * 商品海报
     * @return false|string
     */
    public function poster(){
        if (!empty($qrcode_param)) return $this->response($this->error('', '缺少必须参数qrcode_param'));

        $promotion_type = 'bargain';
        $qrcode_param = json_decode($this->params[ 'qrcode_param' ], true);
        $qrcode_param[ 'source_member' ] = $qrcode_param[ 'source_member' ] ?? 0;
        $poster = new Poster();
        $res = $poster->goods($this->params[ 'app_type' ], $this->params[ 'page' ], $qrcode_param, $promotion_type);
        return $this->response($res);
    }
}