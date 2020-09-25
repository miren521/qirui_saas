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


namespace app\model\goods;


use app\model\shop\Shop;
use think\facade\Db;
use think\facade\Cache;
use app\model\BaseModel;

/**
 * 商品评价
 */
class GoodsEvaluate extends BaseModel
{
	private $evaluate_status = [
		0 => '未评价',
		1 => '已评价',
		2 => '已追评'
	];
	
	/**
	 * 添加评价
	 * @param array $data
	 */
	public function addEvaluate($data)
	{
		$res = model('goods_evaluate')->getInfo([ [ 'order_id', '=', $data['order_id'] ] ], 'evaluate_id');
		if (empty($res)) {
			$data_arr = [];
			foreach ($data['goods_evaluate'] as $k => $v) {
				$item = [
					'order_id' => $data['order_id'],
					'order_no' => $data['order_no'],
					'member_id' => $data['member_id'],
					'member_name' => $data['member_name'],
					'member_headimg' => $data['member_headimg'],
					'is_anonymous' => $data['is_anonymous'],
					'order_goods_id' => $v['order_goods_id'],
					'goods_id' => $v['goods_id'],
					'sku_id' => $v['sku_id'],
					'site_id' => $v['site_id'],
					'sku_name' => $v['sku_name'],
					'sku_price' => $v['sku_price'],
					'sku_image' => $v['sku_image'],
					'content' => $v['content'],
					'images' => $v['images'],
					'scores' => $v['scores'],
					'explain_type' => $v['explain_type'],
					'create_time' => time(),

                    'shop_desccredit' => $data["shop_desccredit"],
                    'shop_servicecredit' => $data["shop_servicecredit"],
                    'shop_deliverycredit' => $data["shop_deliverycredit"],
				];
				$data_arr[] = $item;
				
				$evaluate = 0; //评价
				$evaluate_shaitu = 0; //晒图
				$evaluate_shipin = 0; //视频
				$evaluate_haoping = 0; //好评
				$evaluate_zhongping = 0; //中评
				$evaluate_chaping = 0; //差评
				if ($v['explain_type'] == 1) {
					//好评
					$evaluate = 1; //评价
					$evaluate_haoping = 1; //好评
					
				} elseif ($v['explain_type'] == 2) {
					//中评
					$evaluate = 1; //评价
					$evaluate_zhongping = 1; //中评
					
				} elseif ($v['explain_type'] == 3) {
					//差评
					$evaluate = 1; //评价
					$evaluate_chaping = 1; //差评
				}
				if (!empty($v['images'])) {
					$evaluate_shaitu = 1; //晒图
				}
				Db::name('goods')->where([ [ 'goods_id', '=', $v['goods_id'] ] ])
					->update(
						[
							"evaluate" => Db::raw('evaluate+' . $evaluate),
							"evaluate_shaitu" => Db::raw('evaluate_shaitu+' . $evaluate_shaitu),
							"evaluate_haoping" => Db::raw('evaluate_haoping+' . $evaluate_haoping),
							"evaluate_zhongping" => Db::raw('evaluate_zhongping+' . $evaluate_zhongping),
							"evaluate_chaping" => Db::raw('evaluate_chaping+' . $evaluate_chaping),
						]);
				Db::name('goods_sku')->where([ [ 'sku_id', '=', $v['sku_id'] ] ])
					->update(
						[
							"evaluate" => Db::raw('evaluate+' . $evaluate),
							"evaluate_shaitu" => Db::raw('evaluate_shaitu+' . $evaluate_shaitu),
							"evaluate_haoping" => Db::raw('evaluate_haoping+' . $evaluate_haoping),
							"evaluate_zhongping" => Db::raw('evaluate_zhongping+' . $evaluate_zhongping),
							"evaluate_chaping" => Db::raw('evaluate_chaping+' . $evaluate_chaping),
						]);
			}

//			修改订单表中的评价标识
			model("order")->update([ 'is_evaluate' => 1, 'evaluate_status' => 1, 'evaluate_status_name' => $this->evaluate_status[1] ], [ [ 'order_id', '=', $data['order_id'] ] ]);
			$evaluate_id = model('goods_evaluate')->addList($data_arr);
			Cache::tag("goods_evaluate")->clear();
			return $this->success($evaluate_id);
		} else {
			return $this->error([], '当前订单已评价');
		}
		
	}
	
	/**
	 * 评价回复
	 * @param unknown $data
	 */
	public function evaluateApply($data)
	{
		$res = model("goods_evaluate")->update($data, [ [ 'evaluate_id', '=', $data['evaluate_id'] ] ]);
		Cache::tag("goods_evaluate")->clear();
		return $this->success($res);
	}

    /**
     * 追评
     * @param $data
     * @return array
     */
	public function evaluateAgain($data)
	{
		foreach ($data['goods_evaluate'] as $k => $v) {
			$item = [
				'order_id' => $data['order_id'],
				'order_goods_id' => $v['order_goods_id'],
				'goods_id' => $v['goods_id'],
				'sku_id' => $v['sku_id'],
				'again_content' => $v['again_content'],
				'again_images' => $v['again_images'],
				'again_time' => time()
			];
			$res = model("goods_evaluate")->update($item, [ [ 'order_goods_id', '=', $v['order_goods_id'] ] ]);
			if ($res) {
				model("goods")->setInc([ [ 'goods_id', '=', $v['goods_id'] ] ], 'evaluate_zhuiping', 1);
				model("goods_sku")->setInc([ [ 'sku_id', '=', $v['sku_id'] ] ], 'evaluate_zhuiping', 1);
			}
		}
		model("order")->update([ 'is_evaluate' => 0, 'evaluate_status' => 2, 'evaluate_status_name' => $this->evaluate_status[2] ], [ [ 'order_id', '=', $data['order_id'] ] ]);
		Cache::tag("goods_evaluate")->clear();
		return $this->success($res);
	}
	
	/**
	 * 删除评价
	 * @param unknown $condition
	 */
	public function deleteEvaluate($evaluate_id)
	{
		$res = model('goods_evaluate')->delete([ 'evaluate_id' => $evaluate_id ]);
		Cache::tag("goods_evaluate")->clear();
		return $this->success($res);
	}
	
	/**
	 * 获取评价信息
	 * @param $condition
	 * @param $field
	 * @param $order
	 * @return \multitype
	 */
	public function getFirstEvaluateInfo($condition, $field = 'evaluate_id,order_goods_id,goods_id,sku_id,sku_name,sku_price,content,images,explain_first,member_name,member_headimg,member_id,is_anonymous,again_content,again_images,again_explain,create_time,again_time', $order = "create_time desc")
	{
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::get("goods_evaluate_getFirstEvaluateInfo_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$info = model('goods_evaluate')->getFirstData($condition, $field, $order);
        if(!empty($info)){
            //匿名评价的话不显示用户昵称
            if($info['is_anonymous'] == 1){
                $item_member_name = $info['member_name'] ?? '';
                $info['member_name'] = mb_substr($item_member_name, 0, 1, 'utf-8').'***'. mb_substr($item_member_name, -1, 1, 'utf-8').'(匿名)';
            }
        }
		Cache::tag("goods_evaluate")->set("goods_evaluate_getFirstEvaluateInfo_" . $data, $info);
		return $this->success($info);
	}
	
	/**
	 * 获取评价列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getEvaluateList($condition = [], $field = 'evaluate_id, order_id, order_no, order_goods_id, goods_id, sku_id, sku_name, sku_price, sku_image, content, images, explain_first, member_name, member_id, is_anonymous, scores, again_content, again_images, again_explain, explain_type, is_show, create_time, again_time,shop_desccredit,shop_servicecredit,shop_deliverycredit', $order = '', $limit = null)
	{
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::get("goods_evaluate_getEvaluateList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('goods_evaluate')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("goods_evaluate")->set("goods_evaluate_getEvaluateList_" . $data, $list);
		return $this->success($list);
	}
	
	/**
	 * 获取评价分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getEvaluatePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = 'evaluate_id, order_id, order_no, order_goods_id, goods_id, sku_id, sku_name, sku_price, sku_image, content, images, explain_first, member_name,member_headimg, member_id, is_anonymous, scores, again_content, again_images, again_explain, explain_type, is_show, create_time, again_time,shop_desccredit,shop_servicecredit,shop_deliverycredit')
	{
		$data = json_encode([ $condition, $field, $order, $page, $page_size ]);
		$cache = Cache::get("goods_evaluate_getEvaluatePageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('goods_evaluate')->pageList($condition, $field, $order, $page, $page_size);
        foreach($list['list'] as $k => $v){
            //匿名评价的话不显示用户昵称
            if($v['is_anonymous'] == 1){
                $item_member_name = $list['list'][$k]['member_name'] ?? '';
                $list['list'][$k]['member_name'] = mb_substr($item_member_name, 0, 1, 'utf-8').'***'. mb_substr($item_member_name, -1, 1, 'utf-8').'(匿名)';
            }
        }
		Cache::tag("goods_evaluate")->set("goods_evaluate_getEvaluatePageList_" . $data, $list);
		return $this->success($list);
	}

	/******************************************************************** 计算店铺分数 **********************************************************************/

    /**
     * 商品评价后 计算店铺评价
     * @param $site_id
     * @param $evaluate
     * @param $num
     */
    public function shopEvaluate($order_id, $evaluate){
        $order_info = model("order")->getInfo([["order_id", "=", $order_id]], "site_id");
        $site_id = $order_info["site_id"];
        $num = model("order_goods")->getCount([["order_id", "=", $order_id]], "order_goods_id");
        $count = model("goods_evaluate")->getCount([["site_id", "=", $site_id]], "evaluate_id");
        $shop_model = new Shop();
        $shop_info_result = $shop_model->getShopInfo([["site_id", "=", $site_id]], "shop_desccredit,shop_servicecredit,shop_deliverycredit,shop_status");
        $shop_info = $shop_info_result["data"];
        $last_count = $count + $num;
        //控制非法分值
        foreach($evaluate as $k => $v){
            if($v < 0){
                $v = 0;
            }
            if($v > 5){
                $v = 5;
            }
            $evaluate[$k] = $v;
        }
        $data = [
        	'shop_status' => $shop_info['shop_status']
        ];
        if($evaluate["shop_desccredit"] > 0){
            $avg_desccredit = ($shop_info["shop_desccredit"]*$count + $evaluate["shop_desccredit"]*$num)/$last_count;
            $data["shop_desccredit"] = $avg_desccredit;
        }
        if($evaluate["shop_servicecredit"] > 0){
            $avg_servicecredit = ($shop_info["shop_servicecredit"]*$count + $evaluate["shop_servicecredit"]*$num)/$last_count;
            $data["shop_servicecredit"] = $avg_servicecredit;
        }
        if($evaluate["shop_deliverycredit"] > 0){
            $avg_deliverycredit = ($shop_info["shop_deliverycredit"]*$count + $evaluate["shop_deliverycredit"]*$num)/$last_count;
            $data["shop_deliverycredit"] = $avg_deliverycredit;
        }
        if(!empty($data)){
            $result = $shop_model->editShop($data, [["site_id", "=", $site_id]]);
        }else{
            $result = $this->success();
        }

        return $result;
    }
    /******************************************************************** 计算店铺分数 **********************************************************************/
	
}