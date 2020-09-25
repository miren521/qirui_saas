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


namespace addon\fenxiao\api\controller;

use app\api\controller\BaseApi;
use addon\fenxiao\model\Fenxiao as FenxiaoModel;
use addon\fenxiao\model\FenxiaoGoodsCollect as FenxiaoGoodsCollectModel;
use addon\fenxiao\model\Config as ConfigModel;

/**
 * 分销商关注商品
 */
class Goodscollect extends BaseApi
{
	
	/**
	 * 添加分销商关注商品
	 * @return false|string
	 */
	public function add()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$goods_id = isset($this->params['goods_id']) ? $this->params['goods_id'] : 0;
		$sku_id = isset($this->params['sku_id']) ? $this->params['sku_id'] : 0;
		$site_id = isset($this->params['site_id']) ? $this->params['site_id'] : 0;
		
		if (empty($goods_id)) {
			return $this->response($this->error('', 'REQUEST_GOODS_ID'));
		}
		if (empty($sku_id)) {
			return $this->response($this->error('', 'REQUEST_SKU_ID'));
		}
		if (empty($site_id)) {
			return $this->response($this->error('', 'REQUEST_SITE_ID'));
		}
		$fenxiao_model = new FenxiaoModel();
		$fenxiao_info = $fenxiao_model->getFenxiaoInfo([ [ 'member_id', '=', $this->member_id ] ], "fenxiao_id");
		$fenxiao_info = $fenxiao_info['data'];
		
		$data = [
			'member_id' => $this->member_id,
			'fenxiao_id' => $fenxiao_info['fenxiao_id'],
			'goods_id' => $goods_id,
			'sku_id' => $sku_id,
			'site_id' => $site_id,
		];
		$fenxiao_goods_sku_model = new FenxiaoGoodsCollectModel();
		$res = $fenxiao_goods_sku_model->addCollect($data);
		return $this->response($res);
	}
	
	/**
	 * 删除分销商关注商品
	 * @return false|string
	 */
	public function delete()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$collect_id = isset($this->params['collect_id']) ? $this->params['collect_id'] : 0;
		
		if (empty($collect_id)) {
			return $this->response($this->error('', 'REQUEST_COLLECT_ID'));
		}
		
		$fenxiao_model = new FenxiaoModel();
		$fenxiao_info = $fenxiao_model->getFenxiaoInfo([ [ 'member_id', '=', $this->member_id ] ], "fenxiao_id");
		$fenxiao_info = $fenxiao_info['data'];
		$condition = [
			[ 'fenxiao_id', '=', $fenxiao_info['fenxiao_id'] ],
			[ 'collect_id', '=', $collect_id ]
		];
		$fenxiao_goods_sku_model = new FenxiaoGoodsCollectModel();
		$res = $fenxiao_goods_sku_model->deleteCollect($condition);
		return $this->response($res);
	}
	
	/**
	 * 分销商关注商品分页列表
	 */
	public function page()
	{
		$token = $this->checkToken();
		if ($token['code'] < 0) return $this->response($token);
		
		$page = isset($this->params['page']) ? $this->params['page'] : 1;
		$page_size = isset($this->params['page_size']) ? $this->params['page_size'] : PAGE_LIST_ROWS;
		
		// 获取当前用户的分销等级
		$fenxiao_model = new FenxiaoModel();
		$fenxiao_info = $fenxiao_model->getFenxiaoInfo([ [ 'member_id', '=', $this->member_id ] ], "fenxiao_id,level_id");
		$fenxiao_info = $fenxiao_info['data'];
		
		$condition = [
			[ 'g.is_fenxiao', '=', 1 ],
			[ 'gs.goods_state', '=', 1 ],
			[ 'gs.verify_state', '=', 1 ],
			[ 'gs.is_delete', '=', 0 ],
			[ 'fgs.level_id', '=', $fenxiao_info['level_id'] ],
			[ 'fgc.fenxiao_id', '=', $fenxiao_info['fenxiao_id'] ]
		];



//		$config = new ConfigModel();
//		$basics_config = $config->getFenxiaoBasicsConfig();
//		$basics_config = $basics_config['data']['value'];
		
		$fenxiao_goods_collect_model = new FenxiaoGoodsCollectModel();
		$list = $fenxiao_goods_collect_model->getCollectPageList($condition, $page, $page_size);
		
		// 计算佣金比率
		foreach ($list['data']['list'] as $k => $v) {
			
			$discount_price = $v['discount_price'];
			
			// 一级佣金比例/金额
            $money = 0;
            if ($v['one_rate'] > 0) {
                $money = number_format($discount_price * $v[ 'one_rate' ] / 100, 2);
            }elseif ($v['one_money'] > 0){
                $money = $v['one_money'];
            }
			
//			if ($basics_config['level'] == 1) {
//
//				// 一级佣金比例/金额
//				$money = $v['one_money'];
//				if ($v['one_rate']) {
//					$money = number_format($discount_price * $v['one_rate'] / 100, 2);
//				}
//			} elseif ($basics_config['level'] == 2) {
//				// 二级佣金比例/金额
//				$money = $v['two_money'];
//				if ($v['two_rate']) {
//					$money = number_format($discount_price * $v['two_rate'] / 100, 2);
//				}
//			} elseif ($basics_config['level'] == 3) {
//
//				// 三级佣金比例/金额
//				$money = $v['three_money'];
//				if ($v['three_rate']) {
//					$money = number_format($discount_price * $v['three_rate'] / 100, 2);
//				}
//			}
//			// 一级佣金比例/金额
//			$money = $v['one_money'];
//			if ($v['one_rate']) {
//				$money = number_format($discount_price * $v['one_rate'] / 100, 2);
//			}
			
			$list['data']['list'][ $k ]['commission_money'] = $money;
			
		}
		
		return $this->response($list);
		
	}
	
}