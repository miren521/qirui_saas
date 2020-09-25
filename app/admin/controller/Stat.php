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


namespace app\admin\controller;


use app\model\system\Stat as StatModel;
/**
 * 统计管理 控制器
 */
class Stat extends BaseAdmin
{
	/**
	 * 统计概况
	 */
	public function index()
	{
        if (request()->isAjax()) {
            $date_type = input('date_type', 0);

            if($date_type == 0){
                $start_time = strtotime("today");
                $time_range = date('Y-m-d',$start_time);
            }else if($date_type == 1){
                $start_time = strtotime(date('Y-m-d', strtotime("-6 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
            }else if($date_type == 2){
                $start_time = strtotime(date('Y-m-d', strtotime("-29 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
            }

            $stat_model = new StatModel();

            $shop_stat_sum = $stat_model->getShopStatSum(0, $start_time);

            $shop_stat_sum['data']['time_range'] = $time_range;

            return $shop_stat_sum;
        }else{
            return $this->fetch("stat/index");
        }
	}

    /**
     * 店铺统计报表
     * */
    public function getStatList()
    {
        if (request()->isAjax()) {
            $date_type = input('date_type', 1);

            if($date_type == 1){
                $start_time = strtotime(date('Y-m-d', strtotime("-6 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
                $day = 6;
            }else if($date_type == 2){
                $start_time = strtotime(date('Y-m-d', strtotime("-29 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
                $day = 29;
            }

            $stat_model = new StatModel();

            $stat_list = $stat_model->getShopStatList(0, $start_time);

            //将时间戳作为列表的主键
            $shop_stat_list = array_column($stat_list['data'], null, 'day_time');

            $data = array();

            for ($i = 0;$i <= $day;$i++){
                $time = strtotime(date('Y-m-d',strtotime("-".($day-$i)." day")));
                $data['time'][$i] = date('Y-m-d',$time);
                if(array_key_exists($time, $shop_stat_list)){
                    $data['order_total'][$i] = $shop_stat_list[$time]['order_total'];
                    $data['shipping_total'][$i] = $shop_stat_list[$time]['shipping_total'];
                    $data['refund_total'][$i] = $shop_stat_list[$time]['refund_total'];
                    $data['order_pay_count'][$i] = $shop_stat_list[$time]['order_pay_count'];
                    $data['goods_pay_count'][$i] = $shop_stat_list[$time]['goods_pay_count'];
                    $data['shop_money'][$i] = $shop_stat_list[$time]['shop_money'];
                    $data['platform_money'][$i] = $shop_stat_list[$time]['platform_money'];
                    $data['collect_shop'][$i] = $shop_stat_list[$time]['collect_shop'];
                    $data['collect_goods'][$i] = $shop_stat_list[$time]['collect_goods'];
                    $data['visit_count'][$i] = $shop_stat_list[$time]['visit_count'];
                    $data['order_count'][$i] = $shop_stat_list[$time]['order_count'];
                    $data['goods_count'][$i] = $shop_stat_list[$time]['goods_count'];
                    $data['add_goods_count'][$i] = $shop_stat_list[$time]['add_goods_count'];
                    $data['member_count'][$i] = $shop_stat_list[$time]['member_count'];
                }else{
                    $data['order_total'][$i] = 0.00;
                    $data['shipping_total'][$i] = 0.00;
                    $data['refund_total'][$i] = 0.00;
                    $data['order_pay_count'][$i] = 0;
                    $data['goods_pay_count'][$i] = 0;
                    $data['shop_money'][$i] = 0.00;
                    $data['platform_money'][$i] = 0.00;
                    $data['collect_shop'][$i] = 0;
                    $data['collect_goods'][$i] = 0;
                    $data['visit_count'][$i] = 0;
                    $data['order_count'][$i] = 0;
                    $data['goods_count'][$i] = 0;
                    $data['add_goods_count'][$i] = 0;
                    $data['member_count'][$i] = 0;
                }
            }

            $data['time_range'] = $time_range;

            return $data;
        }
    }

    /**
     * 交易分析
     */
    public function order()
    {
        if (request()->isAjax()) {
            $date_type = input('date_type', 0);

            if($date_type == 0){
                $start_time = strtotime("today");
                $time_range = date('Y-m-d',$start_time);
            }else if($date_type == 1){
                $start_time = strtotime(date('Y-m-d', strtotime("-6 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
            }else if($date_type == 2){
                $start_time = strtotime(date('Y-m-d', strtotime("-29 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
            }

            $stat_model = new StatModel();

            $shop_stat_sum = $stat_model->getShopStatSum(0, $start_time);

            $shop_stat_sum['data']['time_range'] = $time_range;

            return $shop_stat_sum;
        }else{
            return $this->fetch("stat/order");
        }
    }

    /**
     * 交易统计报表
     * */
    public function getOrderStatList()
    {
        if (request()->isAjax()) {
            $date_type = input('date_type', 1);

            if($date_type == 1){
                $start_time = strtotime(date('Y-m-d', strtotime("-6 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
                $day = 6;
            }else if($date_type == 2){
                $start_time = strtotime(date('Y-m-d', strtotime("-29 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
                $day = 29;
            }

            $stat_model = new StatModel();

            $stat_list = $stat_model->getShopStatList(0, $start_time);

            //将时间戳作为列表的主键
            $shop_stat_list = array_column($stat_list['data'], null, 'day_time');

            $data = array();

            for ($i = 0;$i <= $day;$i++){
                $time = strtotime(date('Y-m-d',strtotime("-".($day-$i)." day")));
                $data['time'][$i] = date('Y-m-d',$time);
                if(array_key_exists($time, $shop_stat_list)){
                    $data['order_total'][$i] = $shop_stat_list[$time]['order_total'];
                    $data['shipping_total'][$i] = $shop_stat_list[$time]['shipping_total'];
                    $data['refund_total'][$i] = $shop_stat_list[$time]['refund_total'];
                    $data['order_pay_count'][$i] = $shop_stat_list[$time]['order_pay_count'];
                    $data['goods_pay_count'][$i] = $shop_stat_list[$time]['goods_pay_count'];
                    $data['shop_money'][$i] = $shop_stat_list[$time]['shop_money'];
                    $data['platform_money'][$i] = $shop_stat_list[$time]['platform_money'];
                    $data['collect_shop'][$i] = $shop_stat_list[$time]['collect_shop'];
                    $data['collect_goods'][$i] = $shop_stat_list[$time]['collect_goods'];
                    $data['visit_count'][$i] = $shop_stat_list[$time]['visit_count'];
                    $data['order_count'][$i] = $shop_stat_list[$time]['order_count'];
                    $data['goods_count'][$i] = $shop_stat_list[$time]['goods_count'];
                    $data['add_goods_count'][$i] = $shop_stat_list[$time]['add_goods_count'];
                    $data['member_count'][$i] = $shop_stat_list[$time]['member_count'];
                }else{
                    $data['order_total'][$i] = 0.00;
                    $data['shipping_total'][$i] = 0.00;
                    $data['refund_total'][$i] = 0.00;
                    $data['order_pay_count'][$i] = 0;
                    $data['goods_pay_count'][$i] = 0;
                    $data['shop_money'][$i] = 0.00;
                    $data['platform_money'][$i] = 0.00;
                    $data['collect_shop'][$i] = 0;
                    $data['collect_goods'][$i] = 0;
                    $data['visit_count'][$i] = 0;
                    $data['order_count'][$i] = 0;
                    $data['goods_count'][$i] = 0;
                    $data['add_goods_count'][$i] = 0;
                    $data['member_count'][$i] = 0;
                }
            }

            $data['time_range'] = $time_range;

            return $data;
        }
    }

    /**
     * 商品统计
     * @return mixed
     */
    public function goods()
    {
        if (request()->isAjax()) {
            $date_type = input('date_type', 0);

            if($date_type == 0){
                $start_time = strtotime("today");
                $time_range = date('Y-m-d',$start_time);
            }else if($date_type == 1){
                $start_time = strtotime(date('Y-m-d', strtotime("-6 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
            }else if($date_type == 2){
                $start_time = strtotime(date('Y-m-d', strtotime("-29 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
            }

            $stat_model = new StatModel();

            $shop_stat_sum = $stat_model->getShopStatSum(0, $start_time);

            $shop_stat_sum['data']['time_range'] = $time_range;

            return $shop_stat_sum;
        }else{
            return $this->fetch("stat/goods");
        }
    }

    /**
     * 商品统计报表
     * */
    public function getGoodsStatList()
    {
        if (request()->isAjax()) {
            $date_type = input('date_type', 1);

            if($date_type == 1){
                $start_time = strtotime(date('Y-m-d', strtotime("-6 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
                $day = 6;
            }else if($date_type == 2){
                $start_time = strtotime(date('Y-m-d', strtotime("-29 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
                $day = 29;
            }

            $stat_model = new StatModel();

            $stat_list = $stat_model->getShopStatList(0, $start_time);

            //将时间戳作为列表的主键
            $shop_stat_list = array_column($stat_list['data'], null, 'day_time');

            $data = array();

            for ($i = 0;$i <= $day;$i++){
                $time = strtotime(date('Y-m-d',strtotime("-".($day-$i)." day")));
                $data['time'][$i] = date('Y-m-d',$time);
                if(array_key_exists($time, $shop_stat_list)){
                    $data['order_total'][$i] = $shop_stat_list[$time]['order_total'];
                    $data['shipping_total'][$i] = $shop_stat_list[$time]['shipping_total'];
                    $data['refund_total'][$i] = $shop_stat_list[$time]['refund_total'];
                    $data['order_pay_count'][$i] = $shop_stat_list[$time]['order_pay_count'];
                    $data['goods_pay_count'][$i] = $shop_stat_list[$time]['goods_pay_count'];
                    $data['shop_money'][$i] = $shop_stat_list[$time]['shop_money'];
                    $data['platform_money'][$i] = $shop_stat_list[$time]['platform_money'];
                    $data['collect_shop'][$i] = $shop_stat_list[$time]['collect_shop'];
                    $data['collect_goods'][$i] = $shop_stat_list[$time]['collect_goods'];
                    $data['visit_count'][$i] = $shop_stat_list[$time]['visit_count'];
                    $data['order_count'][$i] = $shop_stat_list[$time]['order_count'];
                    $data['goods_count'][$i] = $shop_stat_list[$time]['goods_count'];
                    $data['add_goods_count'][$i] = $shop_stat_list[$time]['add_goods_count'];
                    $data['member_count'][$i] = $shop_stat_list[$time]['member_count'];
                }else{
                    $data['order_total'][$i] = 0.00;
                    $data['shipping_total'][$i] = 0.00;
                    $data['refund_total'][$i] = 0.00;
                    $data['order_pay_count'][$i] = 0;
                    $data['goods_pay_count'][$i] = 0;
                    $data['shop_money'][$i] = 0.00;
                    $data['platform_money'][$i] = 0.00;
                    $data['collect_shop'][$i] = 0;
                    $data['collect_goods'][$i] = 0;
                    $data['visit_count'][$i] = 0;
                    $data['order_count'][$i] = 0;
                    $data['goods_count'][$i] = 0;
                    $data['add_goods_count'][$i] = 0;
                    $data['member_count'][$i] = 0;
                }
            }

            $data['time_range'] = $time_range;

            return $data;
        }
    }

    /**
     * 会员统计
     * @return mixed
     */
    public function member()
    {
        if (request()->isAjax()) {
            $date_type = input('date_type', 0);

            if($date_type == 0){
                $start_time = strtotime("today");
                $time_range = date('Y-m-d',$start_time);
            }else if($date_type == 1){
                $start_time = strtotime(date('Y-m-d', strtotime("-6 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
            }else if($date_type == 2){
                $start_time = strtotime(date('Y-m-d', strtotime("-29 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
            }

            $stat_model = new StatModel();

            $shop_stat_sum = $stat_model->getShopStatSum(0, $start_time);

            $shop_stat_sum['data']['time_range'] = $time_range;

            return $shop_stat_sum;
        }else{
            return $this->fetch("stat/member");
        }
    }

    /**
     * 会员统计报表
     * */
    public function getMemberStatList()
    {
        if (request()->isAjax()) {
            $date_type = input('date_type', 1);

            if($date_type == 1){
                $start_time = strtotime(date('Y-m-d', strtotime("-6 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
                $day = 6;
            }else if($date_type == 2){
                $start_time = strtotime(date('Y-m-d', strtotime("-29 day")));
                $time_range = date('Y-m-d',$start_time).' 至 '.date('Y-m-d',strtotime("today"));
                $day = 29;
            }

            $stat_model = new StatModel();

            $stat_list = $stat_model->getShopStatList(0, $start_time);

            //将时间戳作为列表的主键
            $shop_stat_list = array_column($stat_list['data'], null, 'day_time');

            $data = array();

            for ($i = 0;$i <= $day;$i++){
                $time = strtotime(date('Y-m-d',strtotime("-".($day-$i)." day")));
                $data['time'][$i] = date('Y-m-d',$time);
                if(array_key_exists($time, $shop_stat_list)){
                    $data['order_total'][$i] = $shop_stat_list[$time]['order_total'];
                    $data['shipping_total'][$i] = $shop_stat_list[$time]['shipping_total'];
                    $data['refund_total'][$i] = $shop_stat_list[$time]['refund_total'];
                    $data['order_pay_count'][$i] = $shop_stat_list[$time]['order_pay_count'];
                    $data['goods_pay_count'][$i] = $shop_stat_list[$time]['goods_pay_count'];
                    $data['shop_money'][$i] = $shop_stat_list[$time]['shop_money'];
                    $data['platform_money'][$i] = $shop_stat_list[$time]['platform_money'];
                    $data['collect_shop'][$i] = $shop_stat_list[$time]['collect_shop'];
                    $data['collect_goods'][$i] = $shop_stat_list[$time]['collect_goods'];
                    $data['visit_count'][$i] = $shop_stat_list[$time]['visit_count'];
                    $data['order_count'][$i] = $shop_stat_list[$time]['order_count'];
                    $data['goods_count'][$i] = $shop_stat_list[$time]['goods_count'];
                    $data['add_goods_count'][$i] = $shop_stat_list[$time]['add_goods_count'];
                    $data['member_count'][$i] = $shop_stat_list[$time]['member_count'];
                }else{
                    $data['order_total'][$i] = 0.00;
                    $data['shipping_total'][$i] = 0.00;
                    $data['refund_total'][$i] = 0.00;
                    $data['order_pay_count'][$i] = 0;
                    $data['goods_pay_count'][$i] = 0;
                    $data['shop_money'][$i] = 0.00;
                    $data['platform_money'][$i] = 0.00;
                    $data['collect_shop'][$i] = 0;
                    $data['collect_goods'][$i] = 0;
                    $data['visit_count'][$i] = 0;
                    $data['order_count'][$i] = 0;
                    $data['goods_count'][$i] = 0;
                    $data['add_goods_count'][$i] = 0;
                    $data['member_count'][$i] = 0;
                }
            }

            $data['time_range'] = $time_range;

            return $data;
        }
    }
    
}