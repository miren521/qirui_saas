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

namespace addon\bundling\model;

use app\model\shop\Shop as ShopModel;
use app\model\BaseModel;

/**
 * 优惠套餐
 */
class Bundling extends BaseModel
{
    /**
     * TODO 添加优惠套餐
     * @param $data
     * @param $sku_ids
     * @return array|\multitype
     */
    public function addBundling($data, $sku_ids)
    {
        if ($data["bl_price"] <= 0) {
            return $this->error([], "优惠套餐价格不能小于或等与0");
        }
        model("promotion_bundling")->startTrans();
        try {
            $sku_id_array = explode(',', $sku_ids);
            $goods_money = 0;
            $sku_array = [];
            foreach ($sku_id_array as $k => $v) {
                $sku_info = model("goods_sku")->getInfo([['sku_id', '=', $v]], 'sku_id,sku_name,price,sku_image,is_virtual');
                if ($sku_info["is_virtual"] == 1) {
                    model("promotion_bundling")->rollback();
                    return $this->error([], "优惠套餐中不能包含虚拟商品");
                }
                unset($sku_info["is_virtual"]);
                $goods_money += $sku_info['price'];
                $sku_array[] = $sku_info;
            }

            $data["goods_money"] = $goods_money;
            $data["update_time"] = time();
            $shop_model = new ShopModel();
            $shop_info_result = $shop_model->getShopInfo([["site_id", "=", $data["site_id"]]], "site_name");
            $shop_info = $shop_info_result["data"];
            $data["site_name"] = $shop_info["site_name"];
            $bundling_id = model("promotion_bundling")->add($data);
            foreach ($sku_array as $k => $v) {
                $v['bl_id'] = $bundling_id;
                $v['site_id'] = $data["site_id"];
                $v['promotion_price'] = $v['price'] / $goods_money * $data['bl_price'];
                model("promotion_bundling_goods")->add($v);
            }
            model("promotion_bundling")->commit();
            return $this->success($bundling_id);
        } catch ( \Exception $e ) {
            model("promotion_bundling")->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 编辑优惠套餐
     * @param unknown $data
     * @param unknown $sku_ids
     */
    public function editBundling($data, $sku_ids, $condition)
    {
        if ($data["bl_price"] <= 0) {
            return $this->error([], "优惠套餐价格不能小于或等与0");
        }
        $check_condition = array_column($condition, 2, 0);
        model("promotion_bundling")->startTrans();
        try {
            model("promotion_bundling_goods")->delete($condition);
            $sku_id_array = explode(',', $sku_ids);
            $goods_money = 0;
            $sku_array = [];
            foreach ($sku_id_array as $k => $v) {
                $sku_info = model("goods_sku")->getInfo([['sku_id', '=', $v]], 'sku_id,sku_name,price,sku_image,is_virtual');
                if ($sku_info["is_virtual"] == 1) {
                    model("promotion_bundling")->rollback();
                    return $this->error([], "优惠套餐中不能包含虚拟商品");
                }
                unset($sku_info["is_virtual"]);
                $sku_info['bl_id'] = $check_condition['bl_id'];
                $goods_money += $sku_info['price'];
                $sku_array[] = $sku_info;
            }
            $data["goods_money"] = $goods_money;
            $data["update_time"] = time();
            $res = model("promotion_bundling")->update($data, $condition);
            foreach ($sku_array as $k => $v) {
                $v['promotion_price'] = $v['price'] / $goods_money * $data['bl_price'];
                $v["site_id"] = $check_condition["site_id"];
                model("promotion_bundling_goods")->add($v);
            }
            model("promotion_bundling")->commit();
            return $this->success($res);
        } catch ( \Exception $e ) {
            model("promotion_bundling")->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 删除优惠套餐
     * @param number $bl_id
     * @param number $site_id
     */
    public function deleteBundling($bl_id, $site_id)
    {
        $condition = array(
            ['bl_id', "=", $bl_id],
            ["site_id", "=", $site_id]
        );
        $res = model('promotion_bundling')->delete($condition);
        if ($res) {
            model('promotion_bundling_goods')->delete(['bl_id' => $bl_id]);
            return $this->success($res);
        } else {
            return $this->error();
        }
    }

    /**
     * 获取优惠套餐详情
     * @param unknown $bundling_id
     */
    public function getBundlingInfo($condition)
    {
        $data = model("promotion_bundling")->getInfo($condition, 'bl_id,bl_name, site_id, site_name, bl_price, goods_money, shipping_fee_type,status');
        return $this->success($data);
    }


    /**
     * 获取优惠套餐详情
     * @param unknown $bundling_id
     */
    public function getBundlingDetail($condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $bl_id = isset($check_condition['bl_id']) ? $check_condition['bl_id'] : '';
        $data = model("promotion_bundling")->getInfo($condition, 'bl_id,bl_name, site_id, site_name, bl_price, goods_money, shipping_fee_type,status');
        if (!empty($data)) {

            $field = 'pbg.sku_id, pbg.sku_name, pbg.price, pbg.sku_image, pbg.promotion_price,ngs.stock,ngs.unit,ngs.goods_state, ngs.verify_state';
            $order = '';
            $alias = 'pbg';
            $join = [
                [
                    'goods_sku ngs',
                    'pbg.sku_id = ngs.sku_id',
                    'inner'
                ],
            ];
            $data['bundling_goods'] = model("promotion_bundling_goods")->getList([['pbg.bl_id', '=', $bl_id]], $field, $order, $alias, $join);
        }
        return $this->success($data);
    }

    /**
     * 获取商品优惠套餐
     * @param $sku_id
     * @return array
     */
    public function getBundlingGoods($sku_id)
    {
        $bundling_ids = model("promotion_bundling_goods")->getList([['sku_id', '=', $sku_id]], 'bl_id');
        $bundling_array = [];
        foreach ($bundling_ids as $k => $v) {
            $temp_result = $this->getBundlingDetail([['bl_id', '=', $v["bl_id"]]]);
            $bundling_array[] = $temp_result["data"];
        }
        return $this->success($bundling_array);
    }

    /**
     * 获取优惠餐列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getBundlingPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('promotion_bundling')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

}