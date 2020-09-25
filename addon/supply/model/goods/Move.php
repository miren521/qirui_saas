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


namespace addon\supply\model\goods;

use addon\supply\model\order\Order;
use app\model\BaseModel;
use app\model\goods\Config;
use app\model\system\Stat;
use app\model\goods\Goods as GoodsModel;
use app\model\goods\Config as GoodsConfig;

/**
 * 商品迁移
 */
class Move extends BaseModel
{

    /**
     * 保存迁移一部分商品(将供应商商品转化为特殊的结构用于普通商品的添加)
     * @param $condition
     */
    public function move($condition, $site_id)
    {

//        $goods_state  传入的商品状态  可以选择直接上架或是到仓库
        $order_model = new Order();
        $order_list = $order_model->getOrderList($condition)[ 'data' ] ?? [];
        if (empty($order_list))
            return $this->error([], 'ORDER_EMPTY');

        $temp_list = [];
        $goods_id_array = [];
        foreach ($order_list as $order_k => $order_v) {
            $order_goods_condition = array(
                ['order_id', '=', $order_v[ 'order_id' ]],
            );
            $order_goods_list = $order_model->getOrderGoodsList($order_goods_condition)[ 'data' ] ?? [];
            if (!empty($order_goods_list)) {
                foreach ($order_goods_list as $order_goods_k => $order_goods_v) {
                    if (!isset($temp_list[ $order_goods_v[ 'goods_id' ] ])) {
                        $temp_list[ $order_goods_v[ 'goods_id' ] ] = [];
                    }
                    $goods_id_array[] = $order_goods_v[ 'goods_id' ];
                    if (!isset($temp_list[ $order_goods_v[ 'goods_id' ] ][ $order_goods_v[ 'sku_id' ] ])) {
                        $temp_list[ $order_goods_v[ 'goods_id' ] ][ $order_goods_v[ 'sku_id' ] ] = 0;
                    }
                    $temp_list[ $order_goods_v[ 'goods_id' ] ][ $order_goods_v[ 'sku_id' ] ] += $order_goods_v[ 'num' ];
                }
            }
        }
        if (empty($temp_list))
            return $this->error([], 'ORDER_EMPTY');

        $temp_goods_condition = [
            ['goods_id', 'in', $goods_id_array]
        ];
        //查询到可迁移的商品
        $supply_goods_model = new Goods();

        $goods_list = $supply_goods_model->getGoodsList($temp_goods_condition, '*')[ 'data' ] ?? [];

        if (empty($goods_list)) {
            return $this->error([], '找不到可迁移的商品');
        }

        $goods_model = new GoodsModel();
        model('goods')->startTrans();

        try {


            //将供应商商品转化为普通的商品

            //店铺信息
            $shop_info = model('shop')->getInfo([['site_id', '=', $site_id]], 'site_id, site_name, website_id, is_own,cert_id, shop_status');

            $goods_config = new Config();
            $goods_verify_config = $goods_config->getVerifyConfig();
            $goods_verify_config = $goods_verify_config[ 'data' ][ 'value' ];
            $verify_state = 1;
            if (!empty($goods_verify_config[ 'is_open' ])) {
                $verify_state = 0;//开启商品审核后，审核状态为：待审核
            }

            // 店铺未认证、审核中的状态下，商品需要审核
            if (empty($shop_info[ 'cert_id' ]) || $shop_info[ 'shop_status' ] == 0 || $shop_info[ 'shop_status' ] == 2) {
                $verify_state = 0;//开启商品审核后，审核状态为：待审核
            }


            foreach ($goods_list as $goods_k => $goods_item) {
                //商品的规格sku项
                $sku_list = $supply_goods_model->getGoodsSkuList([['goods_id', '=', $goods_item[ 'goods_id' ]]], '*')[ 'data' ] ?? [];
                if (empty($sku_list)) {
                    return $this->error([], '商品规格项缺失');
                }

                $first_sku_data = $sku_list[ 0 ];


                //先创建新的商品数据(商品默认没有库存,模拟创建操作)
                $goods_data = $goods_item;

                $stock = array_sum($temp_list[ $goods_item[ 'goods_id' ] ] ?? []);
                $goods_data[ 'goods_stock' ] = $stock;
                $goods_data[ 'price' ] = $first_sku_data[ 'min_price' ];
                $goods_data[ 'market_price' ] = $first_sku_data[ 'market_price' ];
                $goods_data[ 'cost_price' ] = $first_sku_data[ 'cost_price' ];


                unset($goods_data[ 'goods_id' ]);//删除原有的自增长键
                unset($goods_data[ 'max_price' ]);//删除最低价
                unset($goods_data[ 'min_price' ]);//删除最高价
                unset($goods_data[ 'min_num' ]);//删除最低购买数量


                //重置部分字段, 库存都为空, 默认不上架
                $goods_data[ 'sku_id' ] = 0;
                $goods_data[ 'create_time' ] = time();
                $goods_data[ 'modify_time' ] = time();
                $goods_data[ 'discount_id' ] = 0;
                $goods_data[ 'seckill_id' ] = 0;
                $goods_data[ 'topic_id' ] = 0;
                $goods_data[ 'pintuan_id' ] = 0;
                $goods_data[ 'bargain_id' ] = 0;
                $goods_data[ 'sale_num' ] = 0;

                $goods_data[ 'evaluate' ] = 0;
                $goods_data[ 'evaluate_shaitu' ] = 0;
                $goods_data[ 'evaluate_shipin' ] = 0;
                $goods_data[ 'evaluate_zhuiping' ] = 0;
                $goods_data[ 'evaluate_haoping' ] = 0;
                $goods_data[ 'evaluate_zhongping' ] = 0;
                $goods_data[ 'evaluate_chaping' ] = 0;
                $goods_data[ 'is_fenxiao' ] = 0;
                $goods_data[ 'fenxiao_type' ] = 1;
                $goods_data[ 'site_id' ] = $shop_info[ 'site_id' ];
                $goods_data[ 'site_name' ] = $shop_info[ 'site_name' ];

//            $goods_data['goods_state'] = 0;
                $goods_data[ 'verify_state' ] = $verify_state;
                $goods_data[ 'verify_state_remark' ] = '';

                $goods_id = model('goods')->add($goods_data);
                $sku_arr = [];
                foreach ($sku_list as $k => $v) {
                    $sku_data = $v;

                    $sku_data[ 'price' ] = $sku_data[ 'min_price' ];
                    unset($sku_data[ 'sku_id' ]);
                    unset($sku_data[ 'price_json' ]);//删除价格规格
                    unset($sku_data[ 'min_price' ]);//删除最低价
                    unset($sku_data[ 'max_price' ]);//删除最高价
                    unset($sku_data[ 'min_num' ]);//删除最小数量

                    $sku_stock = $temp_list[ $goods_item[ 'goods_id' ] ][ $v[ 'sku_id' ] ] ?? 0;
                    $sku_data[ 'stock' ] = $sku_stock;

                    $sku_data[ 'goods_id' ] = $goods_id;
//                $sku_data['goods_state'] = 0;
                    $sku_data[ 'verify_state' ] = $verify_state;

                    $sku_data[ 'evaluate' ] = 0;
                    $sku_data[ 'evaluate_shaitu' ] = 0;
                    $sku_data[ 'evaluate_shipin' ] = 0;
                    $sku_data[ 'evaluate_zhuiping' ] = 0;
                    $sku_data[ 'evaluate_haoping' ] = 0;
                    $sku_data[ 'evaluate_zhongping' ] = 0;
                    $sku_data[ 'evaluate_chaping' ] = 0;

                    $sku_data[ 'click_num' ] = 0;
                    $sku_data[ 'sale_num' ] = 0;
                    $sku_data[ 'collect_num' ] = 0;

                    $sku_data[ 'discount_id' ] = 0;
                    $sku_data[ 'seckill_id' ] = 0;
                    $sku_data[ 'topic_id' ] = 0;
                    $sku_data[ 'pintuan_id' ] = 0;
                    $sku_data[ 'bargain_id' ] = 0;
                    $sku_data[ 'sale_num' ] = 0;

                    $sku_data[ 'goods_state' ] = 0;
                    $sku_data[ 'verify_state' ] = 0;
                    $sku_data[ 'verify_state_remark' ] = '';

                    $sku_data[ 'start_time' ] = 0;
                    $sku_data[ 'end_time' ] = 0;
                    $sku_data[ 'promotion_type' ] = 0;
                    $sku_data[ 'discount_price' ] = $sku_data[ 'price' ];
                    $sku_data[ 'start_time' ] = 0;

                    $sku_data[ 'site_id' ] = $shop_info[ 'site_id' ];
                    $sku_data[ 'site_name' ] = $shop_info[ 'site_name' ];
                    $sku_arr[] = $sku_data;
                }


                model('goods_sku')->addList($sku_arr);

                // 赋值第一个商品sku_id
                $first_info = model('goods_sku')->getFirstData(['goods_id' => $goods_id], 'sku_id', 'sku_id asc');
                model('goods')->update(['sku_id' => $first_info[ 'sku_id' ]], [['goods_id', '=', $goods_id]]);

                //添加商品属性关联关系
                $goods_model->refreshGoodsAttribute($goods_id, $goods_item[ 'goods_attr_format' ]);

                if (!empty($data[ 'goods_spec_format' ])) {
                    //刷新SKU商品规格项/规格值JSON字符串
                    $goods_model->dealGoodsSkuSpecFormat($goods_id, $data[ 'goods_spec_format' ]);
                }

                //添加店铺添加统计
                //添加统计
                $stat = new Stat();
                $stat->addShopStat(['add_goods_count' => 1, 'site_id' => $goods_item[ 'site_id' ]]);
            }
            model('goods')->commit();
            return $this->success($goods_id);
        } catch ( \Exception $e ) {
            model('goods')->rollback();
            return $this->error($e->getMessage().$e->getFile().$e->getLine());
        }


    }
}