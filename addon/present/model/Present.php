<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\present\model;

use app\model\BaseModel;
use app\model\goods\GoodsStock;
use app\model\system\Cron;
use think\facade\Db;

/**
 * 赠品活动
 */
class Present extends BaseModel
{
    /**
     * 添加赠品
     * @param $present_data
     * @return array|\multitype
     */
    public function addPresent($present_data)
    {
        $present_data[ 'create_time' ] = time();

        $condition = [
            ['site_id', '=', $present_data[ 'site_id' ]],
            ['status', 'in', '1,2'],
            ['goods_id', '=', $present_data[ 'goods_id' ]],
            ['sku_id', '=', $present_data[ 'sku_id' ]],
            ['', 'exp', Db::raw('not ( (`start_time` > ' . $present_data[ 'end_time' ] . ' and `start_time` > ' . $present_data[ 'start_time' ] . ' )  or (`end_time` < ' . $present_data[ 'start_time' ] . ' and `end_time` < ' . $present_data[ 'end_time' ] . '))')]//todo  修正  所有的优惠都要一样
        ];
        //查询该商品是否存在赠品
        $present_info = model('promotion_present')->getInfo($condition, 'present_id,status');

        if (!empty($present_info)) {
            return $this->error('', "当前时间段内该商品存在赠品活动");
        }
        // 当前时间
        $time = time();

        if ($time > $present_data[ 'start_time' ] && $time < $present_data[ 'end_time' ]) {

            $present_data[ 'status' ] = 2;
        } else {
            $present_data[ 'status' ] = 1;
        }

        $present_id = model("promotion_present")->add($present_data);

        $cron = new Cron();
        if ($present_data[ 'status' ] == 2) {
            $cron->addCron(1, 0, "赠品活动关闭", "ClosePresent", $present_data[ 'end_time' ], $present_id);
        } else {
            $cron->addCron(1, 0, "赠品活动开启", "OpenPresent", $present_data[ 'start_time' ], $present_id);
            $cron->addCron(1, 0, "赠品活动关闭", "ClosePresent", $present_data[ 'end_time' ], $present_id);
        }
        return $this->success($present_id);
    }

    /**
     * 编辑赠品
     * @param $present_id
     * @param $site_id
     * @param $present_data
     * @return array|\multitype
     */
    public function editPresent($condition, $present_data)
    {
        //查询赠品活动
        $present_info = model('promotion_present')->getInfo($condition, '*');
        if (empty($present_info))
            return $this->error();

        $time_condition = [
            ['site_id', '=', $present_info[ 'site_id' ]],
//            [ 'status', 'in', '1,2' ],
            ['goods_id', '=', $present_info[ 'goods_id' ]],
            ['sku_id', '=', $present_info[ 'sku_id' ]],
            ['', 'exp', Db::raw('not ( (`start_time` > ' . $present_data[ 'end_time' ] . ' and `start_time` > ' . $present_data[ 'start_time' ] . ' )  or (`end_time` < ' . $present_data[ 'start_time' ] . ' and `end_time` < ' . $present_data[ 'end_time' ] . '))')],
            ['present_id', '<>', $present_info[ 'present_id' ]]
        ];

        //查询该商品是否存在赠品
        $temp_present_info = model('promotion_present')->getInfo($time_condition, 'present_id,status');
        if (!empty($temp_present_info)) {
            return $this->error('', "当前时间段内该商品存在赠品活动");
        }

//		$present_info = model("promotion_present")->getInfo([ [ 'present_id', '=', $present_id ], [ 'site_id', '=', $site_id ] ], 'status');
        if ($present_info[ 'status' ] == 2) {
            return $this->error('', "当前活动再进行中，不能修改");
        }
        // 当前时间
        $time = time();

        if ($time > $present_data[ 'start_time' ] && $time < $present_data[ 'end_time' ]) {
            $present_data[ 'status' ] = 2;
        } else {
            $present_data[ 'status' ] = 1;
        }

        $present_data[ 'modify_time' ] = time();

        $res = model("promotion_present")->update($present_data, $condition);

        $cron = new Cron();
        if ($present_data[ 'status' ] == 2) {
            //活动商品启动
            $this->cronOpenPresent($present_info[ 'present_id' ]);
            $cron->deleteCron([['event', '=', 'Openpresent'], ['relate_id', '=', $present_info[ 'present_id' ]]]);
            $cron->deleteCron([['event', '=', 'Closepresent'], ['relate_id', '=', $present_info[ 'present_id' ]]]);

            $cron->addCron(1, 0, "赠品活动关闭", "ClosePresent", $present_data[ 'end_time' ], $present_info[ 'present_id' ]);
        } else {
            $cron->deleteCron([['event', '=', 'OpenPresent'], ['relate_id', '=', $present_info[ 'present_id' ]]]);
            $cron->deleteCron([['event', '=', 'ClosePresent'], ['relate_id', '=', $present_info[ 'present_id' ]]]);

            $cron->addCron(1, 0, "赠品活动开启", "OpenPresent", $present_data[ 'start_time' ], $present_info[ 'present_id' ]);
            $cron->addCron(1, 0, "赠品活动关闭", "ClosePresent", $present_data[ 'end_time' ], $present_info[ 'present_id' ]);
        }

        return $this->success($res);
    }

    /**
     * 更新赠品库存
     */
    public function modifyPresentStock($condition, $stock){
        $result = model('promotion_present')->update(['stock' => $stock], $condition);
        if($result !== false){
            return $this->error();
        }
        return $this->success($result);
    }
    /**
     * 删除赠品活动
     * @param $present_id
     * @param $site_id
     * @return array|\multitype
     */
    public function deletePresent($condition)
    {
        //赠品信息
        $present_info = model('promotion_present')->getInfo($condition, 'present_id,status');
        if ($present_info) {
            $present_id = $present_info[ 'present_id' ];
            if (in_array($present_info[ 'status' ], [1, 3])) {
                $res = model('promotion_present')->delete([['present_id', '=', $present_id]]);
                if ($res) {
                    $cron = new Cron();
                    $cron->deleteCron([['event', '=', 'OpenPresent'], ['relate_id', '=', $present_id]]);
                    $cron->deleteCron([['event', '=', 'ClosePresent'], ['relate_id', '=', $present_id]]);
                }
                return $this->success($res);
            } else {
                return $this->error('', '赠品活动进行中或已结束');
            }

        } else {
            return $this->error('', '赠品活动不存在');
        }
    }

    /**
     * 结束赠品活动
     * @param $present_id
     * @param $site_id
     * @return array
     */
    public function finishPresent($condition)
    {
        //赠品信息
        $present_info = model('promotion_present')->getInfo($condition, 'present_id,status');
        if ($present_info) {
            $present_id = $present_info['present_id'];
            if ($present_info[ 'status' ] != 3) {
                $res = model('promotion_present')->update(['status' => 3], [['present_id', '=', $present_id]]);
                if ($res) {
                    $cron = new Cron();
                    $cron->deleteCron([['event', '=', 'OpenPresent'], ['relate_id', '=', $present_id]]);
                    $cron->deleteCron([['event', '=', 'ClosePresent'], ['relate_id', '=', $present_id]]);
                }
                return $this->success($res);
            } else {
                $this->error('', '该赠品活动已结束');
            }

        } else {
            $this->error('', '该赠品活动不存在');
        }
    }

    /**
     * 获取赠品信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getPresentInfo($condition = [], $field = '*')
    {
        //赠品信息
        $present_info = model("promotion_present")->getInfo($condition, $field);
        return $this->success($present_info);
    }

    /**
     * 赠品商品详情
     * @param array $condition
     * @return array
     */
    public function getPresentGoodsDetail($condition = [])
    {
        $field = 'pg.present_id,pg.present_price,pg.buy_num,pg.start_time,pg.end_time,pg.sale_num,pg.status,sku.sku_id,sku.site_id,sku.sku_name,sku.price,sku.sku_spec_format,sku.promotion_type,sku.stock,sku.click_num,sku.sale_num,sku.collect_num,sku.sku_image,sku.sku_images,sku.goods_id,sku.site_id,sku.goods_content,sku.goods_state,sku.verify_state,sku.is_virtual,sku.is_free_shipping,sku.goods_spec_format,sku.goods_attr_format,sku.introduction,sku.unit,sku.video_url,sku.evaluate,sku.category_id,sku.category_id_1,sku.category_id_2,sku.category_id_3,sku.category_name,sku.goods_id';
        $alias = 'pg';
        $join = [
            ['goods_sku sku', 'pg.goods_id = sku.goods_id', 'inner']
        ];

        $goods_info = model('promotion_present')->getInfo($condition, $field, $alias, $join);
        return $this->success($goods_info);
    }

    /**
     * 获取赠品列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getPresentList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('promotion_present')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取赠品分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getPresentPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('promotion_present')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 获取赠品商品分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getPresentGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'pg.start_time desc', $field = 'pg.present_id,pg.sku_price,pg.sale_num,pg.site_id,sku.sku_id,sku.price,sku.sku_name,sku.sku_image,g.goods_id,g.goods_name', $alias = '', $join = '')
    {
        $list = model('promotion_present')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }

    /**
     * 开启赠品活动
     * @param $present_id
     * @return array|\multitype
     */
    public function cronOpenPresent($present_id)
    {
        $present_info = model('promotion_present')->getInfo([
                ['present_id', '=', $present_id]]
            , 'start_time,status'
        );
        if (!empty($present_info)) {
            if ($present_info[ 'start_time' ] <= time() && $present_info[ 'status' ] == 1) {
                $res = model('promotion_present')->update(['status' => 2], [['present_id', '=', $present_id]]);

                return $this->success($res);
            } else {
                return $this->error("", "赠品活动已开启或者关闭");
            }

        } else {
            return $this->error("", "赠品活动不存在");
        }

    }

    /**
     * 关闭赠品活动
     * @param $present_id
     * @return array|\multitype
     */
    public function cronClosePresent($present_id)
    {
        $present_info = model('promotion_present')->getInfo([
                ['present_id', '=', $present_id]]
            , 'start_time,status'
        );
        if (!empty($present_info)) {
            if ($present_info[ 'status' ] != 3) {
                $res = model('promotion_present')->update(['status' => 3], [['present_id', '=', $present_id]]);
                return $this->success($res);
            } else {
                return $this->error("", "该活动已结束");
            }
        } else {
            return $this->error("", "赠品活动不存在");
        }
    }





    /****************************************************************************** 发放赠品 start ******************************************************************/

    /**
     * 发放赠品
     * @param $param
     */
    public function givingPresent($param){

        $sku_id = $param['sku_id'] ?? 0;
        $member_id = $param['member_id'] ?? 0;
        $present_id = $param['present_id'] ?? 0;
        $num = $param['num'] ?? 0;
        $goods_stock_model = new GoodsStock();
        if($present_id > 0 && $member_id > 0 && $num > 0){
            $present_info = model('promotion_present')->getInfo([['present_id', '=', $present_id]]);
            if(empty($present_info)){
                return $this->error([], '赠品不存在');
            }
            if($present_info['status'] != 2){
                return $this->error([], '当前赠品已过期或还未开始');
            }
            $stock_result = $goods_stock_model->decStock(["sku_id" => $sku_id, "num" => $num]);
            if($stock_result['code'] < 0)
                return $stock_result;

            //如果发放成功, 增加以发放数量
            $result = model('promotion_present')->setInc([['present_id', '=', $present_id]], 'sale_num', $num);
            return $stock_result;
        }else{
            return $this->error([], '缺少发放赠品必要参数');
        }

    }
    /****************************************************************************** 发放赠品 end ******************************************************************/
}