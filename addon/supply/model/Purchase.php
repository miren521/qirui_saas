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


namespace addon\supply\model;


use app\model\system\Cron;
use app\model\BaseModel;

/**
 * 求购
 */

class Purchase extends BaseModel
{

    /**
     * 添加求购信息
     * @param array $data
     */
    public function addPurchase($param)
    {
        $data_item_list = $param['purchase_goods_item'];
        $now_time = time();
        if($param['end_time'] < $now_time){
            return $this->error('', '截止时间不能小于当前时间');
        }
        $data = [
            'title' => $param['title'],
            'type' => $param['type'],
            'nickname' => $param['nickname'],
            'sex' => $param['sex'],
            'mobile' => $param['mobile'],
            'email' => $param['email'],
            'end_time' => $param['end_time'],
            'is_invoice' => $param['is_invoice'],
            'province' => $param['province'],
            'province_name' => $param['province_name'],
            'city' => $param['city'],
            'city_name' => $param['city_name'],
            'district' => $param['district'],
            'district_name' => $param['district_name'],
            'community' => $param['community'],
            'community_name' => $param['community_name'],
            'address' => $param['address'],
            'full_address' => $param['full_address'],
            'remark' => $param['remark'],
            'shop_id' => $param['shop_id'],
            'shop_name' => $param['shop_name'],
            'uid' => $param['uid'],

            'goods_name' => $data_item_list[0]['goods_name'],
            'goods_num' => $data_item_list[0]['num'],
            'goods_price' => $data_item_list[0]['price'],
            'goods_image' => $data_item_list[0]['goods_images'][0] ?? '',
            'create_time' => $now_time,
            'update_time' => $now_time,

            'status' => 1,//默认进行中uid
            'audit_status' => 1,//审核状态
        ];
        $purchase_id = model('supply_purchase')->add($data);

        foreach($data_item_list as $k => $v){
            $data_item_list[$k]['purchase_id'] = $purchase_id;
            $data_item_list[$k]['shop_id'] = $param['shop_id'];
            $data_item_list[$k]['shop_name'] = $param['shop_name'];
            $data_item_list[$k]['uid'] = $param['uid'];
            $goods_image_array = $v['goods_images'];
            $data_item_list[$k]['goods_images'] = implode(',', $goods_image_array);
            $data_item_list[$k]['goods_image'] = $goods_image_array[0] ?? '';
            $data_item_list[$k]['category_id'] = $v['category_id_1'];
            $data_item_list[$k]['category_name'] = $v['category_name_1'];

        }
        model('supply_purchase_goods_item')->addList($data_item_list);
        //todo  设置关闭的自动事件

        $cron_model = new Cron();
        $cron_model->addCron(1, 0, "求购单自动截止", "CronSupplyPurchaseClose", $param['end_time'], $purchase_id);

        return $this->success();
    }

    /**
     * 修改求购
     * @param array $data
     */
    public function editPurchase($data, $condition)
    {
        model('supply_purchase')->update($data, $condition);
        return $this->success();
    }

    /**
     * 删除求购
     * @param $condition
     * @return array
     */
    public function deletePurchase($condition)
    {
        $res = model('supply_purchase')->delete($condition);
        return $this->success($res);

    }

    /**
     * 关闭求购
     * @param $condition
     * @return array
     */
    public function closePurchase($condition)
    {
        $res = model('supply_purchase')->update(['status' => 2],$condition);
        return $this->success($res);

    }

    /**
     * 获取求购信息
     * @param $condition
     * @param string $field
     */
    public function getPurchaseInfo($condition, $field = '*')
    {
        $purchase_info = model('supply_purchase')->getInfo($condition, $field);
        if(!empty($purchase_info)){
            //查询求购商品项
            $purchase_goods_item_list = model('supply_purchase_goods_item')->getList($condition, $field);
            $purchase_info['list'] = $purchase_goods_item_list;
        }
        return $this->success($purchase_info);
    }



    /**
     * 获取求购列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getPurchaseList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('supply_purchase')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取求购分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getPurchasePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('supply_purchase')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
}