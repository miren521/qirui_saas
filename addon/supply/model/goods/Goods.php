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

use app\model\BaseModel;
use app\model\system\Stat;
use app\model\goods\Config as GoodsConfig;

/**
 * 商品
 */
class Goods extends BaseModel
{

    private $goods_class = array('id' => 1, 'name' => '实物商品');

    private $goods_state = array(
        1 => '正常',
        0 => '下架'
    );

    private $verify_state = array(
        1 => '已审核',
        0 => '待审核',
        -2 => '审核失败',
        10 => '违规下架'
    );

    public function getGoodsState()
    {
        return $this->goods_state;
    }

    public function getVerifyState()
    {
        return $this->verify_state;
    }
    /**
     * 商品添加
     * @param $data
     */
    public function addGoods($data)
    {
        model('supply_goods')->startTrans();

        try {

            //供应商信息
//			$supply_info = model('shop')->getInfo([ [ 'site_id', '=', $data['site_id'] ] ], 'site_name, website_id, is_own,cert_id,shop_status');

            $supply_info = model('supplier')->getInfo([['supplier_site_id', '=', $data[ 'site_id' ]]], 'title, status');
            $website_id = $supply_info[ 'website_id' ] ?? 0;
            $is_own = $supply_info[ 'is_own' ] ?? 0;
            $goods_config = new GoodsConfig();
            $goods_verify_config = $goods_config->getVerifyConfig();
            $goods_verify_config = $goods_verify_config[ 'data' ][ 'value' ];
            $verify_state = 1;
            if (!empty($goods_verify_config[ 'is_open' ])) {
                $verify_state = 0;//开启商品审核后，审核状态为：待审核
            }

            // 供应商未认证、审核中的状态下，商品需要审核
			if (empty($supply_info['cert_id']) || $supply_info['status'] == 0 || $supply_info['shop_status'] == 2) {
				$verify_state = 0;//开启商品审核后，审核状态为：待审核
			}

            $goods_image = $data[ 'goods_image' ];

            //SKU商品数据
            if (!empty($data[ 'goods_sku_data' ])) {
                $data[ 'goods_sku_data' ] = json_decode($data[ 'goods_sku_data' ], true);
                if (empty($goods_image)) {
                    $goods_image = $data[ 'goods_sku_data' ][ 0 ][ 'sku_image' ];
                }
            }


            $sku_arr = array();
            //添加sku商品
            $all_temp_array = [];
            foreach ($data[ 'goods_sku_data' ] as $item) {

                //价格阶梯price_json
                $price_json = $item[ 'price_json' ] ?? '';

                if (empty($price_json)) {
                    model('supply_goods')->rollback();
                    return $this->error([], '阶梯价格不能为空!');
                }
                $price_array = json_decode($price_json, true);

                array_multisort(array_column($price_array, 'num'), SORT_ASC, $price_array);
                $price_json = json_encode($price_array);
                $num_column = array_column($price_array, 'num');
                $price_column = array_column($price_array, 'price');
                if (count($num_column) != count(array_unique($num_column))) {
                    model("supply_goods")->rollback();
                    return $this->error([], '规格阶梯价格的数量不能重复!');
                }
                $min_price = min($price_column);
                $max_price = max($price_column);
                $min_num = min($num_column);

                $sku_data = array(
                    'sku_name' => $data[ 'goods_name' ] . ' ' . $item[ 'spec_name' ],
                    'spec_name' => $item[ 'spec_name' ],
                    'sku_no' => $item[ 'sku_no' ],
                    'sku_spec_format' => !empty($item[ 'sku_spec_format' ]) ? json_encode($item[ 'sku_spec_format' ]) : "",
                    'price_json' => $item[ 'price_json' ],
                    'market_price' => $item[ 'market_price' ],
                    'cost_price' => $item[ 'cost_price' ],
                    'stock' => $item[ 'stock' ],
                    'weight' => $item[ 'weight' ],
                    'volume' => $item[ 'volume' ],
                    'sku_image' => $item[ 'sku_image' ],
                    'sku_images' => $item[ 'sku_images' ],

                    'price_json' => $price_json,
                    'min_price' => $min_price,
                    'price' => $min_price,
                    'max_price' => $max_price,
                    'min_num' => $min_num,
                    'site_id' => $data[ 'site_id' ],
                    'site_name' => $data[ 'site_name' ],
                );
                $sku_arr[] = $sku_data;
                $all_temp_array = array_merge($all_temp_array, $price_array);
            }
            //比对
            $all_min_price = min(array_column($all_temp_array, 'price'));
            $all_max_price = max(array_column($all_temp_array, 'price'));
            $all_min_num = min(array_column($all_temp_array, 'num'));

            $goods_data = array(
                'goods_image' => $goods_image,
                'goods_stock' => $data[ 'goods_stock' ],
                'market_price' => $data[ 'goods_sku_data' ][ 0 ][ 'market_price' ],
                'cost_price' => $data[ 'goods_sku_data' ][ 0 ][ 'cost_price' ],
                'goods_spec_format' => $data[ 'goods_spec_format' ],
                'min_price' => $all_min_price,
                'price' => $all_min_price,
                'max_price' => $all_max_price,
                'min_num' => $all_min_num,
            );

            $common_data = array(
                'goods_name' => $data[ 'goods_name' ],
                'goods_class' => $this->goods_class[ 'id' ],
                'goods_class_name' => $this->goods_class[ 'name' ],
                'goods_attr_class' => $data[ 'goods_attr_class' ],
                'goods_attr_name' => $data[ 'goods_attr_name' ],
                'website_id' => $website_id,
                'category_id' => $data[ 'category_id' ],
                'category_id_1' => $data[ 'category_id_1' ],
                'category_id_2' => $data[ 'category_id_2' ],
                'category_id_3' => $data[ 'category_id_3' ],
                'category_name' => $data[ 'category_name' ],
                'brand_id' => $data[ 'brand_id' ],
                'brand_name' => $data[ 'brand_name' ],
                'goods_content' => $data[ 'goods_content' ],
                'is_own' => $is_own,
                'goods_state' => $data[ 'goods_state' ],
                'goods_stock_alarm' => $data[ 'goods_stock_alarm' ],
                'is_free_shipping' => $data[ 'is_free_shipping' ],
                'shipping_template' => $data[ 'shipping_template' ],
                'goods_attr_format' => $data[ 'goods_attr_format' ],
                'introduction' => $data[ 'introduction' ],
                'keywords' => $data[ 'keywords' ],
                'unit' => $data[ 'unit' ],
                'commission_rate' => $data[ 'commission_rate' ],
                'video_url' => $data[ 'video_url' ],
                'sort' => $data[ 'sort' ],
                'verify_state' => $verify_state,
                'create_time' => time(),

                'site_id' => $data[ 'site_id' ],
                'site_name' => $data[ 'site_name' ],
            );

            $goods_id = model('supply_goods')->add(array_merge($goods_data, $common_data));

            //添加sku商品
            foreach ($sku_arr as $sku_k => $item) {
                $item[ 'goods_id' ] = $goods_id;
                $sku_arr[ $sku_k ] = array_merge($common_data, $item);
            }

            model('supply_goods_sku')->addList($sku_arr);

            // 赋值第一个商品sku_id
            $first_info = model('supply_goods_sku')->getFirstData(['goods_id' => $goods_id], 'sku_id', 'sku_id asc');
            model('supply_goods')->update(['sku_id' => $first_info[ 'sku_id' ]], [['goods_id', '=', $goods_id]]);

//			添加商品属性关联关系
            $this->refreshGoodsAttribute($goods_id, $data[ 'goods_attr_format' ]);

            if (!empty($data[ 'goods_spec_format' ])) {
//				刷新SKU商品规格项/规格值JSON字符串
                $this->dealGoodsSkuSpecFormat($goods_id, $data[ 'goods_spec_format' ]);
            }

//			添加供应商添加统计
//			添加统计
            //todo  供应商统计
            $stat = new Stat();
            $stat->addShopStat(['add_goods_count' => 1, 'site_id' => $data[ 'site_id' ]]);
            model('supply_goods')->commit();

            return $this->success($goods_id);
        } catch ( \Exception $e ) {
            model('supply_goods')->rollback();
            return $this->error($e->getMessage() . $e->getFile() . $e->getLine());
        }
    }

    /**
     * 商品编辑
     * @param $data
     */
    public function editGoods($data)
    {

        model('supply_goods')->startTrans();

        try {

            $goods_id = $data[ 'goods_id' ];

            //供应商信息
            $supply_info = model('supplier')->getInfo([['supplier_site_id', '=', $data[ 'site_id' ]]], 'title, status');
            $website_id = $supply_info[ 'website_id' ] ?? 0;
            $is_own = $supply_info[ 'is_own' ] ?? 0;

            $goods_config = new GoodsConfig();
            $goods_verify_config = $goods_config->getVerifyConfig();
            $goods_verify_config = $goods_verify_config[ 'data' ][ 'value' ];
            $verify_state = 1;
            $verify_state_remark = '';
            if (!empty($goods_verify_config[ 'is_open' ])) {
                $verify_state = 0;//开启商品审核后，审核状态为：待审核
            }

            // 供应商未认证、审核中的状态下，商品需要审核
//			if (empty($shop_info['cert_id']) || $shop_info['shop_status'] == 0 || $shop_info['shop_status'] == 2) {
//				$verify_state = 0;//开启商品审核后，审核状态为：待审核
//			}

            $goods_image = $data[ 'goods_image' ];

            //SKU商品数据
            if (!empty($data[ 'goods_sku_data' ])) {
                $data[ 'goods_sku_data' ] = json_decode($data[ 'goods_sku_data' ], true);
                if (empty($goods_image)) {
                    $goods_image = $data[ 'goods_sku_data' ][ 0 ][ 'sku_image' ];
                }
            }


            $sku_arr = array();
            //添加sku商品
            $all_temp_array = [];
            foreach ($data[ 'goods_sku_data' ] as $k => $item) {

                //价格阶梯price_json
                $price_json = $item[ 'price_json' ] ?? '';

                if (empty($price_json)) {
                    model('supply_goods')->rollback();
                    return $this->error([], '阶梯价格不能为空!');
                }
                $price_array = json_decode($price_json, true);

                array_multisort(array_column($price_array, 'num'), SORT_ASC, $price_array);
                $price_json = json_encode($price_array);
                $num_column = array_column($price_array, 'num');
                $price_column = array_column($price_array, 'price');
                if (count($num_column) != count(array_unique($num_column))) {
                    model("supply_goods")->rollback();
                    return $this->error([], '规格阶梯价格的数量不能重复!');
                }
                $min_price = min($price_column);
                $max_price = max($price_column);
                $min_num = min($num_column);

                $sku_data = array(
                    'sku_name' => $data[ 'goods_name' ] . ' ' . $item[ 'spec_name' ],
                    'spec_name' => $item[ 'spec_name' ],
                    'sku_no' => $item[ 'sku_no' ],
                    'sku_spec_format' => !empty($item[ 'sku_spec_format' ]) ? json_encode($item[ 'sku_spec_format' ]) : "",
                    'market_price' => $item[ 'market_price' ],
                    'cost_price' => $item[ 'cost_price' ],
                    'stock' => $item[ 'stock' ],
                    'weight' => $item[ 'weight' ],
                    'volume' => $item[ 'volume' ],
                    'sku_image' => $item[ 'sku_image' ],
                    'sku_images' => $item[ 'sku_images' ],

                    'price_json' => $price_json,
                    'min_price' => $min_price,
                    'price' => $min_price,
                    'max_price' => $max_price,
                    'min_num' => $min_num,
                    'site_id' => $data[ 'site_id' ],
                    'site_name' => $data[ 'site_name' ],
                );
                $sku_arr[ $item[ 'sku_id' ] ] = $sku_data;
                $all_temp_array = array_merge($all_temp_array, $price_array);
            }

            //比对
            $all_min_price = min(array_column($all_temp_array, 'price'));
            $all_max_price = max(array_column($all_temp_array, 'price'));
            $all_min_num = min(array_column($all_temp_array, 'num'));

            $goods_data = array(
                'goods_image' => $goods_image,
                'goods_stock' => $data[ 'goods_stock' ],
                'market_price' => $data[ 'goods_sku_data' ][ 0 ][ 'market_price' ],
                'cost_price' => $data[ 'goods_sku_data' ][ 0 ][ 'cost_price' ],
                'goods_spec_format' => $data[ 'goods_spec_format' ],
                'min_price' => $all_min_price,
                'price' => $all_min_price,
                'max_price' => $all_max_price,
                'min_num' => $all_min_num,
            );

            $common_data = array(
                'goods_name' => $data[ 'goods_name' ],
                'goods_class' => $this->goods_class[ 'id' ],
                'goods_class_name' => $this->goods_class[ 'name' ],
                'goods_attr_class' => $data[ 'goods_attr_class' ],
                'goods_attr_name' => $data[ 'goods_attr_name' ],
                'site_id' => $data[ 'site_id' ],
                'site_name' => $data[ 'site_name' ],
                'website_id' => $website_id,
                'category_id' => $data[ 'category_id' ],
                'category_id_1' => $data[ 'category_id_1' ],
                'category_id_2' => $data[ 'category_id_2' ],
                'category_id_3' => $data[ 'category_id_3' ],
                'category_name' => $data[ 'category_name' ],
                'brand_id' => $data[ 'brand_id' ],
                'brand_name' => $data[ 'brand_name' ],
                'goods_content' => $data[ 'goods_content' ],
                'is_own' => $is_own,
                'goods_state' => $data[ 'goods_state' ],
                'goods_stock_alarm' => $data[ 'goods_stock_alarm' ],
                'is_free_shipping' => $data[ 'is_free_shipping' ],
                'shipping_template' => $data[ 'shipping_template' ],
                'goods_attr_format' => $data[ 'goods_attr_format' ],
                'introduction' => $data[ 'introduction' ],
                'keywords' => $data[ 'keywords' ],
                'unit' => $data[ 'unit' ],
                'commission_rate' => $data[ 'commission_rate' ],
                'video_url' => $data[ 'video_url' ],
                'sort' => $data[ 'sort' ],
                'verify_state' => $verify_state,
                'verify_state_remark' => $verify_state_remark,
                'modify_time' => time(),

                'site_id' => $data[ 'site_id' ],
                'site_name' => $data[ 'site_name' ],
            );

            model('supply_goods')->update(array_merge($goods_data, $common_data), [['goods_id', '=', $goods_id], ['goods_class', '=', $this->goods_class[ 'id' ]]]);


            //修改sku商品
            foreach ($data[ 'goods_sku_data' ] as $item) {
                $sku_data = $sku_arr[ $item[ 'sku_id' ] ];

                model('supply_goods_sku')->update(array_merge($sku_data, $common_data), [['sku_id', '=', $item[ 'sku_id' ]], ['goods_class', '=', $this->goods_class[ 'id' ]]]);

            }

            // 赋值第一个商品sku_id
            $first_info = model('supply_goods_sku')->getFirstData(['goods_id' => $goods_id], 'sku_id', 'sku_id asc');
            model('supply_goods')->update(['sku_id' => $first_info[ 'sku_id' ]], [['goods_id', '=', $goods_id]]);

//			添加商品属性关联关系
            $this->refreshGoodsAttribute($goods_id, $data[ 'goods_attr_format' ]);

            if (!empty($data[ 'goods_spec_format' ])) {
//				刷新SKU商品规格项/规格值JSON字符串
                $this->dealGoodsSkuSpecFormat($goods_id, $data[ 'goods_spec_format' ]);
            }

            model('supply_goods')->commit();
            return $this->success($goods_id);
        } catch ( \Exception $e ) {
            model('supply_goods')->rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * 修改商品状态
     * @param $goods_ids
     * @param $goods_state
     * @param $site_id
     * @return \multitype
     */
    public function modifyGoodsState($goods_ids, $goods_state, $site_id)
    {
        model('supply_goods')->update(['goods_state' => $goods_state], [['goods_id', 'in', $goods_ids], ['site_id', '=', $site_id]]);
        model('supply_goods_sku')->update(['goods_state' => $goods_state], [['goods_id', 'in', $goods_ids], ['site_id', '=', $site_id]]);
        return $this->success(1);
    }

    /**
     * 修改审核状态
     * @param $goods_ids
     * @param $verify_state
     * @param $verify_state_remark
     * @return \multitype
     */
    public function modifyVerifyState($goods_ids, $verify_state, $verify_state_remark)
    {
        model('supply_goods')->update(['verify_state' => $verify_state, 'verify_state_remark' => $verify_state_remark], [['goods_id', 'in', $goods_ids]]);
        model('supply_goods_sku')->update(['verify_state' => $verify_state, 'verify_state_remark' => $verify_state_remark], [['goods_id', 'in', $goods_ids]]);
        return $this->success(1);
    }

    /**
     * 修改删除状态
     * @param $goods_ids
     * @param $is_delete
     * @param $site_id
     */
    public function modifyIsDelete($goods_ids, $is_delete, $site_id)
    {
        model('supply_goods')->update(['is_delete' => $is_delete], [['goods_id', 'in', $goods_ids], ['site_id', '=', $site_id]]);
        model('supply_goods_sku')->update(['is_delete' => $is_delete], [['goods_id', 'in', $goods_ids], ['site_id', '=', $site_id]]);
        return $this->success(1);
    }

    /**
     * 违规下架商品
     * @param $condition
     * @param $verify_state_remark
     * @return array
     */
    public function lockup($condition, $verify_state_remark)
    {
        model('supply_goods')->update(['verify_state_remark' => $verify_state_remark, 'verify_state' => 10, 'goods_state' => 0], $condition);
        model('supply_goods_sku')->update(['verify_state_remark' => $verify_state_remark, 'verify_state' => 10, 'goods_state' => 0], $condition);
        return $this->success(1);
    }

    /**
     * 修改商品点击量
     * @param $sku_id
     * @param $site_id
     */
    public function modifyClick($sku_id, $site_id)
    {
        model("supply_goods_sku")->setInc([['sku_id', '=', $sku_id], ['site_id', '=', $site_id]], 'click_num', 1);
        return $this->success(1);
    }

    /**
     * 删除回收站商品
     * @param $goods_ids
     * @param $site_id
     */
    public function deleteRecycleGoods($goods_ids, $site_id)
    {
        model('supply_goods')->delete([['goods_id', 'in', $goods_ids], ['site_id', '=', $site_id]]);
        model('supply_goods_sku')->delete([['goods_id', 'in', $goods_ids], ['site_id', '=', $site_id]]);
        return $this->success(1);
    }

    /**
     * 获取商品信息
     * @param array $condition
     * @param string $field
     */
    public function getGoodsInfo($condition, $field = 'goods_id,goods_name,goods_class,goods_class_name,goods_attr_class,goods_attr_name,category_id,category_id_1,category_id_2,category_id_3,category_name,brand_id,brand_name,goods_image,goods_content,goods_state,verify_state,market_price,cost_price,goods_stock,goods_stock_alarm,is_free_shipping,shipping_template,goods_spec_format,goods_attr_format,introduction,keywords,unit,sort,commission_rate,video_url,site_id,site_name,min_price,max_price,min_num')
    {
        $info = model('supply_goods')->getInfo($condition, $field);
        return $this->success($info);
    }

    /**
     * 获取商品详情
     * @param $goods_id
     * @return \multitype
     */
    public function getGoodsDetail($goods_id)
    {
        $info = model('supply_goods')->getInfo([['goods_id', '=', $goods_id]], "*");
        $info[ 'sku_data' ] = model('supply_goods_sku')->getList([['goods_id', '=', $goods_id]], 'sku_id, sku_name, sku_no, sku_spec_format, min_price,max_price,min_num, market_price, cost_price, stock, weight, volume,  sku_image, sku_images, sort');
        return $this->success($info);
    }

    /**
     * 商品sku 基础信息
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getGoodsSkuInfo($condition, $field = "sku_id,sku_name,sku_spec_format,market_price,stock,click_num,sale_num,collect_num,sku_image,sku_images,goods_id,goods_content,goods_state,verify_state,is_virtual,is_free_shipping,goods_spec_format,goods_attr_format,introduction,unit,video_url, min_price,max_price,min_num")
    {
        $info = model('supply_goods_sku')->getInfo($condition, $field);
        return $this->success($info);
    }

    /**
     * 商品SKU 详情
     * @param $sku_id
     * @return mixed
     */
    public function getGoodsSkuDetail($sku_id)
    {
        $info = model('supply_goods_sku')->getInfo([['sku_id', '=', $sku_id], ['is_delete', '=', 0]], "goods_id,sku_id,sku_name,sku_spec_format,price,market_price,stock,click_num,sale_num,collect_num,sku_image,sku_images,goods_id,site_id,goods_content,goods_state,verify_state,is_virtual,is_free_shipping,goods_spec_format,goods_attr_format,introduction,unit,video_url,evaluate,category_id,category_id_1,category_id_2,category_id_3,category_name, min_price,max_price,min_num,price_json");
        return $this->success($info);
    }

    /**
     * 获取商品列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getGoodsList($condition = [], $field = 'goods_id,goods_class,goods_class_name,goods_attr_name,goods_name,site_id,site_name,website_id,sort,category_name,brand_name,goods_image,goods_content,is_own,goods_state,verify_state,price,market_price,cost_price,goods_stock,goods_stock_alarm,is_virtual,is_free_shipping,shipping_template,goods_spec_format,goods_attr_format,create_time, min_price,max_price,min_num', $order = 'create_time desc', $limit = null)
    {
        $list = model('supply_goods')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取商品分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = 'goods_id,goods_name,site_name,site_id,goods_image,is_own,goods_state,verify_state,goods_stock,create_time,sale_num,is_virtual,goods_class,is_fenxiao,fenxiao_type,sku_id, min_price,max_price,min_num')
    {
        $list = model('supply_goods')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }


    /**
     * 获取商品sku列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getGoodsSkuList($condition = [], $field = 'sku_id,sku_name,price,stock,sale_num,sku_image,goods_id,goods_name,site_name,spec_name,price_json', $order = 'create_time desc', $limit = null)
    {
        $list = model('supply_goods_sku')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取商品sku分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     * @param string $alias
     * @param string $join
     */
    public function getGoodsSkuPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = 'sku_id,sku_name,price,sku_image,create_time,stock,goods_state,goods_class,goods_class_name', $alias = '', $join = '', $group = null)
    {
        $list = model('supply_goods_sku')->pageList($condition, $field, $order, $page, $page_size, $alias, $join, $group);
        return $this->success($list);
    }

    /**
     * 刷新商品关联属性关系
     * @param $goods_id
     * @param $goods_attr_format
     */
    private function refreshGoodsAttribute($goods_id, $goods_attr_format)
    {
        model('goods_attr_index')->delete([['goods_id', '=', $goods_id], ['app_module', '=','supply']]);
        if (!empty($goods_attr_format)) {

            $list = model('supply_goods_sku')->getList([['goods_id', '=', $goods_id]], 'sku_id');
            $goods_attr_format = json_decode($goods_attr_format, true);
            $attr_data = [];
            foreach ($goods_attr_format as $k => $v) {
                foreach ($list as $ck => $cv) {
                    $item = [
                        'goods_id' => $goods_id,
                        'sku_id' => $cv[ 'sku_id' ],
                        'attr_id' => $v[ 'attr_id' ],
                        'attr_value_id' => $v[ 'attr_value_id' ],
                        'attr_class_id' => $v[ 'attr_class_id' ],
                        'app_module' => 'supply'
                    ];
                    $attr_data[] = $item;
                }
            }
            model('goods_attr_index')->addList($attr_data);
        }
    }

    /**
     * 刷新SKU商品规格项/规格值JSON字符串
     * @param int $goods_id 商品id
     * @param string $goods_spec_format 商品完整规格项/规格值json
     */
    private function dealGoodsSkuSpecFormat($goods_id, $goods_spec_format)
    {
        if (empty($goods_spec_format)) return;

        $goods_spec_format = json_decode($goods_spec_format, true);

        //根据goods_id查询sku商品列表，查询：sku_id、sku_spec_format 列
        $sku_list = model('supply_goods_sku')->getList([['goods_id', '=', $goods_id], ['sku_spec_format', '<>', '']], 'sku_id,sku_spec_format', 'sku_id asc');
        if (!empty($sku_list)) {

//			$temp = 0;//测试性能，勿删

            //循环SKU商品列表
            foreach ($sku_list as $k => $v) {
//				$temp++;

                $sku_format = $goods_spec_format;//最终要存储的值
                $current_format = json_decode($v[ 'sku_spec_format' ], true);//当前SKU商品规格值json

                $selected_data = [];//已选规格/规格值json

                //1、找出已选规格/规格值json

                //循环完整商品规格json
                foreach ($sku_format as $sku_k => $sku_v) {
//					$temp++;

                    //循环当前SKU商品规格json
                    foreach ($current_format as $current_k => $current_v) {
//						$temp++;

                        //匹配规格项
                        if ($current_v[ 'spec_id' ] == $sku_v[ 'spec_id' ]) {

                            //循环规格值
                            foreach ($sku_v[ 'value' ] as $sku_value_k => $sku_value_v) {
//								$temp++;

                                //匹配规格值id
                                if ($current_v[ 'spec_value_id' ] == $sku_value_v[ 'spec_value_id' ]) {
                                    $sku_format[ $sku_k ][ 'value' ][ $sku_value_k ][ 'selected' ] = true;
                                    $sku_format[ $sku_k ][ 'value' ][ $sku_value_k ][ 'sku_id' ] = $v[ 'sku_id' ];
                                    $selected_data[] = $sku_format[ $sku_k ][ 'value' ][ $sku_value_k ];
                                    break;
                                }
                            }

                        }

                    }
                }

                //2、找出未选中的规格/规格值json
                foreach ($sku_format as $sku_k => $sku_v) {
//					$temp++;

                    foreach ($sku_v[ 'value' ] as $sku_value_k => $sku_value_v) {
//						$temp++;

                        if (!isset($sku_value_v[ 'selected' ])) {

                            $refer_data = [];//参考已选中的规格/规格值json
                            $refer_data[] = $sku_value_v;

//							根据已选中的规格值进行参考
                            foreach ($selected_data as $selected_k => $selected_v) {
//								$temp++;
//								排除自身，然后进行参考
                                if ($selected_v[ 'spec_id' ] != $sku_value_v[ 'spec_id' ]) {
                                    $refer_data[] = $selected_v;
                                }
                            }

                            foreach ($sku_list as $again_k => $again_v) {
//								$temp++;

                                //排除当前SKU商品
                                if ($again_v[ 'sku_id' ] != $v[ 'sku_id' ]) {

                                    $current_format_again = json_decode($again_v[ 'sku_spec_format' ], true);
                                    $count = count($current_format_again);//规格总数量
                                    $curr_count = 0;//当前匹配规格数量

                                    //循环当前SKU商品规格json
                                    foreach ($current_format_again as $current_again_k => $current_again_v) {
//										$temp++;

                                        foreach ($refer_data as $fan_k => $fan_v) {
//											$temp++;

                                            if ($current_again_v[ 'spec_value_id' ] == $fan_v[ 'spec_value_id' ] && $current_again_v['spec_id'] == $fan_v[ 'spec_id' ]) {
                                                $curr_count++;
                                            }
                                        }

                                    }

//									匹配数量跟规格总数一致表示匹配成功
                                    if ($curr_count == $count) {
                                        $sku_format[ $sku_k ][ 'value' ][ $sku_value_k ][ 'selected' ] = false;
                                        $sku_format[ $sku_k ][ 'value' ][ $sku_value_k ][ 'sku_id' ] = $again_v[ 'sku_id' ];
                                        break;
                                    }
                                }

                            }

                            //没有匹配到规格值，则禁用
                            if (!isset($sku_format[ $sku_k ][ 'value' ][ $sku_value_k ][ 'selected' ])) {
                                $sku_format[ $sku_k ][ 'value' ][ $sku_value_k ][ 'disabled' ] = false;
                            }

                        }
                    }
                }

//				var_dump($sku_format);
//				var_dump("=========");
                //修改goods_sku表表中的goods_spec_format字段，将$sku_format值传入
                model('supply_goods_sku')->update(['goods_spec_format' => json_encode($sku_format)], [['sku_id', '=', $v[ 'sku_id' ]]]);

            }

//			var_dump("性能：" . $temp);

        }

    }


    /**
     * 增加商品销量
     * @param $sku_id
     * @param $num
     */
    public function incGoodsSaleNum($sku_id, $num)
    {
        $condition = array(
            ["sku_id", "=", $sku_id]
        );
        //增加sku销量
        $res = model("supply_goods_sku")->setInc($condition, "sale_num", $num);
        if ($res !== false) {
            $sku_info = model("supply_goods_sku")->getInfo($condition, "goods_id");
            $res = model("supply_goods")->setInc([["goods_id", "=", $sku_info[ "goods_id" ]]], "sale_num", $num);
            return $this->success($res);
        }

        return $this->error($res);
    }

    /**
     * 减少商品销量
     * @param $sku_id
     * @param $num
     */
    public function decGoodsSaleNum($sku_id, $num)
    {
        $condition = array(
            ["sku_id", "=", $sku_id]
        );
        //增加sku销量
        $res = model("supply_goods_sku")->setDec($condition, "sale_num", $num);
        if ($res !== false) {
            $sku_info = model("supply_goods_sku")->getInfo($condition, "goods_id");
            $res = model("supply_goods")->setDec([["goods_id", "=", $sku_info[ "goods_id" ]]], "sale_num", $num);
            return $this->success($res);
        }
        return $this->error($res);
    }

    /**
     * 获取商品总数
     * @param array $condition
     * @return array
     */
    public function getGoodsTotalCount($condition = [])
    {
        $res = model('supply_goods')->getCount($condition);
        return $this->success($res);
    }

    /**
     * 获取商品规格项总数
     * @param array $condition
     * @return array
     */
    public function getGoodsSkuCount($condition = [])
    {
        $res = model('supply_goods_sku')->getCount($condition);
        return $this->success($res);
    }

}