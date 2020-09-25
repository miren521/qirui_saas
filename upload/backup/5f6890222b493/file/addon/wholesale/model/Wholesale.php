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

namespace addon\wholesale\model;

use app\model\BaseModel;
use app\model\goods\Goods;
use app\model\system\Cron;
use think\facade\Db;

/**
 * 批发活动
 */
class Wholesale extends BaseModel
{


    /**
     * 添加批发商品
     * @param $data
     */
    public function addGoodsWholesale($data){
        $condition = [['goods_id', '=', $data['goods_id']], ['site_id', '=', $data['site_id']]];
        $wholesale_sku_info = model('wholesale_goods')->getInfo($condition, 'wholesale_goods_id, max_price, min_price, min_num');
        if(!empty($wholesale_sku_info)){
            return $this->error([], '当前商品已存在批发!');
        }
        return $this->setGoodsWholesale($data);
    }

    /**
     * 编辑批发商品
     * @param $data
     * @return array|\multitype
     */
    public function editGoodsWholesale($data){
        return $this->setGoodsWholesale($data);
    }
    /**
     * 设置批发商品
     * @param $data
     * @return array|\multitype
     */
    public function setGoodsWholesale($data){
        $goods_id = $data['goods_id'];
        $price_array = json_decode($data['price_json'], true);
        //循环同步各sku规格项
        $all_temp_array = [];

        model("wholesale_goods")->startTrans();
        //循环生成多个订单
        try{
            foreach($price_array as $sku_k => $sku_item){
                $temp_array = [];
                foreach($sku_item as $k => $v){
                    $temp_array[] = ['price' => $v['price'], 'num' => $v['num']];
                }
                array_multisort(array_column($temp_array,'num'),SORT_ASC,$temp_array);
                $price_json = json_encode($temp_array);
                $num_column = array_column($sku_item, 'num');
                $price_column = array_column($sku_item, 'price');
                if(count($num_column) != count(array_unique($num_column))){
                    model("order")->rollback();
                    return $this->error([], '规格阶梯价格的数量不能重复!');
                }
                $min_price = min($price_column);
                $max_price = max($price_column);
                $min_num = min($num_column);
                $item_data = array(
                    'sku_id' => $sku_k,
                    'min_price' => $min_price,
                    'max_price' => $max_price,
                    'min_num' => $min_num,
                    'site_id' => $data['site_id'],
                    'site_name' => $data['site_name'],
                    'price_json' => $price_json
                );
                $item_result = $this->setSkuWholesale($item_data);
                if($item_result['code'])
                    return $item_result;

                $all_temp_array = array_merge($all_temp_array, $temp_array);
            }
            //比对
            $all_min_price = min(array_column($all_temp_array, 'price'));
            $all_max_price = max(array_column($all_temp_array, 'price'));
            $all_min_num = min(array_column($all_temp_array, 'num'));
            //同步批发商品主表(通过比对全部订单项)
            $sync_result = $this->syncWholesaleGoods($data['site_id'], $data['site_name'], $goods_id, $all_max_price, $all_min_price, $all_min_num);
            if($sync_result['code'] < 0){
                model("order")->rollback();
                return $sync_result;
            }
            model("wholesale_goods")->commit();
            return $this->success();
        }catch(\Exception $e)
        {
            model("wholesale_goods")->rollback();
            return $this->error('', $e->getMessage());
        }
    }


	/**
	 * 添加批发
	 * @param $wholesale_data
	 * @return array|\multitype
	 */
	public function setSkuWholesale($data)
	{
        $wholesale_condition = array(
            ['sku_id', '=', $data['sku_id']],
            ['site_id', '=', $data['site_id']]
        );
        $wholesale_sku_info = model('wholesale_goods_sku')->getInfo($wholesale_condition, 'wholesale_sku_id, goods_id');
        if(!empty($wholesale_sku_info)){
            $data['update_time'] = time();

            model('wholesale_goods_sku')->update($data, $wholesale_condition);
            $data['goods_id'] = $wholesale_sku_info['goods_id'];
        }else{
            $goods_model = new Goods();
            $sku_condition = array(
                ['sku_id', '=', $data['sku_id']],
                ['site_id', '=', $data['site_id']]
            );
            $sku_info_result = $goods_model->getGoodsSkuInfo($sku_condition, 'goods_id, sku_name,sku_image');
            $sku_info = $sku_info_result['data'] ?? [];
            if(empty($sku_info))
                return $this->error([], '');

            $data['goods_id'] = $sku_info['goods_id'];
            $data['sku_name'] = $sku_info['sku_name'];
            $data['sku_image'] = $sku_info['sku_image'];
            $data['create_time'] = time();
            model('wholesale_goods_sku')->add($data);
        }

		return $this->success();
	}

    /**
     * 同步批发商品主表
     * @param $goods_id
     * @param $max_price
     * @param $min_price
     * @param $min_num
     */
	public function syncWholesaleGoods($site_id, $site_name, $goods_id, $max_price, $min_price, $min_num){
	    $condition = [['goods_id', '=', $goods_id], ['site_id', '=', $site_id]];
        $wholesale_sku_info = model('wholesale_goods')->getInfo($condition, 'wholesale_goods_id, max_price, min_price, min_num');
        if(empty($wholesale_sku_info)){
            $goods_model = new Goods();
            $goods_condition = array(
                ['goods_id', '=', $goods_id]
            );
            $goods_info_result = $goods_model->getGoodsInfo($goods_condition, 'goods_name, goods_image');
            $goods_info = $goods_info_result['data'] ?? [];
            if(empty($goods_info))
                return $this->error([], '商品不存在!');


            $data = array(
                'max_price' => $max_price,
                'goods_id' => $goods_id,
                'min_price' => $min_price,
                'min_num' => $min_num,
                'goods_name' => $goods_info['goods_name'],
                'goods_image' => $goods_info['goods_image'],
                'create_time' => time(),
                'site_id' => $site_id,
                'site_name' => $site_name
            );
            $wholesale_goods_result = model('wholesale_goods')->add($data);
            if($wholesale_goods_result === false)
                return $this->error();

        }else{
//            $new_max_price = max($wholesale_sku_info['max_price'], $max_price);
//            $new_min_price = min($wholesale_sku_info['min_price'], $min_price);
//            $new_min_num = min($wholesale_sku_info['min_num'], $min_num);
            $data = array(
                'max_price' => $max_price,
                'min_price' => $min_price,
                'min_num' => $min_num,
            );
            $wholesale_goods_result = model('wholesale_goods')->update($data, $condition);
            if($wholesale_goods_result === false)
                return $this->error();
        }
        return $this->success();
    }

    /**
     * 商品参与批发
     * @param $goods_id
     */
    public function delete($condition){
        $result = model('wholesale_goods')->delete($condition);
        if($result === false)
            return $this->error();

        $sku_result = model('wholesale_goods_sku')->delete($condition);
        if($sku_result === false)
            return $this->error();

        return $this->success();
    }



    /**
     * 获取批发信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getWholesaleGoodsDetail($condition = [])
    {
        $wholesale_sku_info = model('wholesale_goods')->getInfo($condition, '*');
        if(!empty($wholesale_sku_info)){
            $goods_model = new Goods();
            $goods_condition = array(
                ['goods_id', '=', $wholesale_sku_info['goods_id']]
            );
            $goods_info_result = $goods_model->getGoodsInfo($goods_condition, 'price');
            $goods_info = $goods_info_result['data'] ?? [];
            $wholesale_sku_info['goods_info'] = $goods_info;
            $sku_list = model('wholesale_goods_sku')->getList([['goods_id', '=', $wholesale_sku_info['goods_id']]]);
            if(!empty($sku_list)){
                foreach($sku_list as $k => $v){
                    $sku_list[$k]['price_array'] = json_decode($v['price_json'], true);

                    $sku_condition = array(
                        ['sku_id', '=', $v['sku_id']],
                    );
                    $sku_info_result = $goods_model->getGoodsSkuInfo($sku_condition, 'price,stock');
                    $sku_info = $sku_info_result['data'] ?? [];
                    $sku_list[$k]['sku_info'] = $sku_info;
                }
            }
            $wholesale_sku_info['sku_list'] = empty($sku_list) ? [] : $sku_list;
        }
        return $this->success($wholesale_sku_info);
    }

	/**
	 * 获取批发信息
	 * @param array $condition
	 * @param string $field
	 * @return array
	 */
	public function getWholesaleSkuDetail($condition = [], $field = 'gs.*,wgs.max_price, wgs.min_price, wgs.min_num,wgs.wholesale_sku_id,wgs.price_json')
	{
        $alias = 'gs';
        $join = [
            [ 'wholesale_goods_sku wgs', 'gs.sku_id = wgs.sku_id', 'left' ]
        ];
        $info  = model('goods_sku')->getInfo($condition, $field, $alias, $join);
        if(!empty($info)){
            $info['price_array'] = empty($info['price_json']) ? [] : json_decode($info['price_json'], true);
        }
        return $this->success($info);
	}
	

	/**
	 * 获取批发分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getWholesalePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
        $alias = 'wgk';
        $join = [
            [ 'goods_sku sku', 'wgk.sku_id = sku.sku_id', 'inner' ]
        ];
        $list = model('wholesale_goods_sku')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
	}
	
	/**
	 * 获取批发商品分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getWholesaleGoodsViewPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'g.create_time desc', $field = 'g.*,wg.wholesale_goods_id, wg.max_price, wg.min_price, wg.min_num, wg.status,sku.sku_id,sku.price,sku.sku_name,sku.sku_image', $alias = '', $join = '')
	{
        $list = model('goods')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}


    /**
     * 获取批发商品规格分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getWholesaleSkuViewList($condition = [], $order = 'gs.create_time desc', $field = 'gs.*,wgs.max_price, wgs.min_price, wgs.min_num,wgs.wholesale_sku_id')
    {
        $alias = 'gs';
        $join = [
            [ 'wholesale_goods_sku wgs', 'gs.sku_id = wgs.sku_id', 'left' ]
        ];
        $list = model('goods_sku')->getList($condition, $field, $order, $alias, $join);
        return $this->success($list);
    }

    /**
     * 获取批发商品规格分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getWholesaleSkuViewPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'g.create_time desc', $field = '*')
    {
        $alias = 'wgs';
        $join = [
            [ 'goods_sku gs', 'gs.sku_id = wgs.sku_id', 'inner' ]
        ];
        $list = model('wholesale_goods_sku')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
        return $this->success($list);
    }
}