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


namespace app\model\system;

use app\model\BaseModel;
use Carbon\Carbon;
use think\facade\Db;

/**
 * 统计
 * @author Administrator
 *
 */
class Stat extends BaseModel
{
    /**
     * 添加店铺统计(按照天统计)
     * @param array $data
     */
    public function addShopStat($data)
    {
        $carbon = Carbon::now();
        $condition = [
            'site_id' => $data[ 'site_id' ],
            'year' => $carbon->year,
            'month' => $carbon->month,
            'day' => $carbon->day
        ];
        $info = model("stat_shop")->getInfo($condition, 'id');
        if (empty($info)) {
            $stat_data = [
                'site_id' => $data[ 'site_id' ],
                'year' => $carbon->year,
                'month' => $carbon->month,
                'day' => $carbon->day,
                'day_time' => Carbon::today()->timestamp,
                'create_time' => time()
            ];
            if (isset($data['order_total'])) {
                $stat_data['order_total'] = $data['order_total'];
            }
            if (isset($data['shipping_total'])) {
                $stat_data['shipping_total'] = $data['shipping_total'];
            }
            if (isset($data['refund_total'])) {
                $stat_data['refund_total'] = $data['refund_total'];
            }
            if (isset($data['order_pay_count'])) {
                $stat_data['order_pay_count'] = $data['order_pay_count'];
            }
            if (isset($data['goods_pay_count'])) {
                $stat_data['goods_pay_count'] = $data['goods_pay_count'];
            }
            if (isset($data['shop_money'])) {
                $stat_data['shop_money'] = $data['shop_money'];
            }
            if (isset($data['platform_money'])) {
                $stat_data['platform_money'] = $data['platform_money'];
            }
            if (isset($data['collect_shop'])) {
                $stat_data['collect_shop'] = $data['collect_shop'];
            }
            if (isset($data['collect_goods'])) {
                $stat_data['collect_goods'] = $data['collect_goods'];
            }
            if (isset($data['visit_count'])) {
                $stat_data['visit_count'] = $data['visit_count'];
            }
            if (isset($data['order_count'])) {
                $stat_data['order_count'] = $data['order_count'];
            }
            if (isset($data['goods_count'])) {
                $stat_data['goods_count'] = $data['goods_count'];
            }
            if (isset($data['add_goods_count'])) {
                $stat_data['add_goods_count'] = $data['add_goods_count'];
            }
            if (isset($data['member_count'])) {
                $stat_data['member_count'] = $data['member_count'];
            }
            $res = model("stat_shop")->add($stat_data);

        } else {
            $stat_data = ['modify_time' => time()];

            if (isset($data['order_total'])) {
                $stat_data['order_total'] = Db::raw('order_total+' . $data['order_total']);
            }
            if (isset($data['shipping_total'])) {
                $stat_data['shipping_total'] = Db::raw('shipping_total+' . $data['shipping_total']);
            }
            if (isset($data['refund_total'])) {
                $stat_data['refund_total'] = Db::raw('refund_total+' . $data['refund_total']);
            }
            if (isset($data['order_pay_count'])) {
                $stat_data['order_pay_count'] = Db::raw('order_pay_count+' . $data['order_pay_count']);
            }
            if (isset($data['goods_pay_count'])) {
                $stat_data['goods_pay_count'] = Db::raw('goods_pay_count+' . $data['goods_pay_count']);
            }
            if (isset($data['shop_money'])) {
                $stat_data['shop_money'] = Db::raw('shop_money+' . $data['shop_money']);
            }
            if (isset($data['platform_money'])) {
                $stat_data['platform_money'] = Db::raw('platform_money+' . $data['platform_money']);
            }
            if (isset($data['collect_shop'])) {
                $stat_data['collect_shop'] = Db::raw('collect_shop+' . $data['collect_shop']);
            }
            if (isset($data['collect_goods'])) {
                $stat_data['collect_goods'] = Db::raw('collect_goods+' . $data['collect_goods']);
            }
            if (isset($data['visit_count'])) {
                $stat_data['visit_count'] = Db::raw('visit_count+' . $data['visit_count']);
            }
            if (isset($data['order_count'])) {
                $stat_data['order_count'] = Db::raw('order_count+' . $data['order_count']);
            }
            if (isset($data['goods_count'])) {
                $stat_data['goods_count'] = Db::raw('goods_count+' . $data['goods_count']);
            }
            if (isset($data['add_goods_count'])) {
                $stat_data['add_goods_count'] = Db::raw('add_goods_count+' . $data['add_goods_count']);
            }
            if (isset($data['member_count'])) {
                $stat_data['member_count'] = Db::raw('member_count+' . $data['member_count']);
            }
            $res = Db::name('stat_shop')->where($condition)->update($stat_data);
        }
        if ($data[ 'site_id' ] != 0) {
            $data[ 'site_id' ] = 0;
            $this->addShopStat($data);
        }
        return $this->success($res);

    }

    /**
     * 获取店铺统计（按照天查询）
     * @param unknown $site_id 0表示平台
     * @param unknown $year
     * @param unknown $month
     * @param unknown $day
     */
    public function getStatShop($site_id, $year, $month, $day)
    {
        $condition = [
            'site_id' => $site_id,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ];
        $info = model("stat_shop")->getInfo($condition,
            'id, site_id, year, month, day, order_total, shipping_total, refund_total, order_pay_count, goods_pay_count, shop_money, platform_money, create_time, modify_time, collect_shop, collect_goods, visit_count, order_count, goods_count, add_goods_count, day_time, member_count');

        if (empty($info)) {
            $condition[ 'day_time' ] = Carbon::today()->timestamp;
            model("stat_shop")->add($condition);
            $info = model("stat_shop")->getInfo($condition,
                'id, site_id, year, month, day, order_total, shipping_total, refund_total, order_pay_count, goods_pay_count, shop_money, platform_money, create_time, modify_time, collect_shop, collect_goods, visit_count, order_count, goods_count, add_goods_count, day_time, member_count');

        }
        return $this->success($info);
    }


    /**
     * 获取店铺统计信息
     * @param unknown $site_id
     * @param unknown $start_time
     */
    public function getShopStatSum($site_id, $start_time = 0)
    {
        $condition = [
            ['site_id', '=', $site_id]
        ];
        if (!empty($start_time)) {
            $condition[] = ['day_time', '>=', $start_time];
        }
        $info = model("stat_shop")->getInfo($condition,
            'SUM(order_total) as order_total,SUM(shipping_total) as shipping_total,SUM(refund_total) as refund_total,SUM(order_pay_count) as order_pay_count,SUM(goods_pay_count) as goods_pay_count,SUM(shop_money) as shop_money,SUM(platform_money) as platform_money,SUM(collect_shop) as collect_shop,SUM(collect_goods) as collect_goods,SUM(visit_count) as visit_count,SUM(order_count) as order_count,SUM(goods_count) as goods_count,SUM(add_goods_count) as add_goods_count,SUM(member_count) as member_count');
        if ($info[ 'order_total' ] == null) {
            $info = [
                "order_total" => 0,
                "shipping_total" => 0,
                "refund_total" => 0,
                "order_pay_count" => 0,
                "goods_pay_count" => 0,
                "shop_money" => 0,
                "platform_money" => 0,
                "collect_shop" => 0,
                "collect_goods" => 0,
                "visit_count" => 0,
                "order_count" => 0,
                "goods_count" => 0,
                "add_goods_count" => 0,
                "member_count" => 0
            ];
        }
        return $this->success($info);
    }

    /**
     * 获取店铺统计列表
     * @param unknown $site_id
     * @param unknown $start_time
     */
    public function getShopStatList($site_id, $start_time)
    {
        $condition = [
            ['site_id', '=', $site_id],
            ['day_time', '>=', $start_time],
        ];
        $info = model("stat_shop")->getList($condition,
            'id, site_id, year, month, day, order_total, shipping_total, refund_total, order_pay_count, goods_pay_count, shop_money, platform_money, create_time, modify_time, collect_shop, collect_goods, visit_count, order_count, goods_count, add_goods_count, day_time, member_count');
        return $this->success($info);
    }
}