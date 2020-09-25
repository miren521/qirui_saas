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

use addon\discount\model\Discount;
use app\model\BaseModel;
use app\model\system\Stat;
use app\model\message\Message;

/**
 * 虚拟商品
 */
class VirtualGoods extends BaseModel
{
    private $goods_class = array('id' => 2, 'name' => '虚拟商品');

    private $goods_state = array(
        1 => '正常',
        0 => '下架'
    );

    private $verify_state = array(
        1 => '已审核',
        0 => '待审核',
        -2 => '审核失败',
        10 => '违规下架',
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
        model('goods')->startTrans();

        try {

            //店铺信息
            $shop_info = model('shop')->getInfo([['site_id', '=', $data[ 'site_id' ]]], 'site_name, website_id, is_own,cert_id,shop_status');

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

            $goods_image = $data[ 'goods_image' ];

            //SKU商品数据
            if (!empty($data[ 'goods_sku_data' ])) {
                $data[ 'goods_sku_data' ] = json_decode($data[ 'goods_sku_data' ], true);
                if (empty($goods_image)) {
                    $goods_image = $data[ 'goods_sku_data' ][ 0 ][ 'sku_image' ];
                }
            }

            //todo  商品的分佣比率是通过选择的分类来计算的
            $goods_category_model = new GoodsCategory();
            $goods_category_info = $goods_category_model->getCategoryInfo([['category_id', '=', $data[ 'category_id' ]]], 'commission_rate')[ 'data' ] ?? [];
            if (empty($goods_category_info)) {
                model('goods')->rollback();
                return $this->error([], '您选择的分类不存在');
            }
            $commission_rate = $goods_category_info[ 'commission_rate' ];

            $goods_data = array(
                'goods_image' => $goods_image,
                'goods_stock' => $data[ 'goods_stock' ],
                'price' => $data[ 'goods_sku_data' ][ 0 ][ 'price' ],
                'market_price' => $data[ 'goods_sku_data' ][ 0 ][ 'market_price' ],
                'cost_price' => $data[ 'goods_sku_data' ][ 0 ][ 'cost_price' ],
                'goods_spec_format' => $data[ 'goods_spec_format' ],
            );

            $common_data = array(
                'goods_name' => $data[ 'goods_name' ],
                'goods_class' => $this->goods_class[ 'id' ],
                'goods_class_name' => $this->goods_class[ 'name' ],
                'goods_attr_class' => $data[ 'goods_attr_class' ],
                'goods_attr_name' => $data[ 'goods_attr_name' ],
                'site_id' => $data[ 'site_id' ],
                'site_name' => $shop_info[ 'site_name' ],
                'website_id' => $shop_info[ 'website_id' ],
                'category_id' => $data[ 'category_id' ],
                'category_id_1' => $data[ 'category_id_1' ],
                'category_id_2' => $data[ 'category_id_2' ],
                'category_id_3' => $data[ 'category_id_3' ],
                'category_name' => $data[ 'category_name' ],
                'brand_id' => $data[ 'brand_id' ],
                'brand_name' => $data[ 'brand_name' ],
                'goods_content' => $data[ 'goods_content' ],
                'is_own' => $shop_info[ 'is_own' ],
                'goods_state' => $data[ 'goods_state' ],
                'goods_stock_alarm' => $data[ 'goods_stock_alarm' ],
                'is_virtual' => 1,
                'virtual_indate' => $data[ 'virtual_indate' ],
                'goods_attr_format' => $data[ 'goods_attr_format' ],
                'introduction' => $data[ 'introduction' ],
                'keywords' => $data[ 'keywords' ],
                'unit' => $data[ 'unit' ],
                'commission_rate' => $commission_rate,
                'video_url' => $data[ 'video_url' ],
                'sort' => $data[ 'sort' ],
                'verify_state' => $verify_state,
                'goods_shop_category_ids' => $data[ 'goods_shop_category_ids' ],
                'supplier_id' => $data[ 'supplier_id' ],
                'create_time' => time(),

                'max_buy' => $data['max_buy'],
                'min_buy' => $data['min_buy']
            );

            $goods_id = model('goods')->add(array_merge($goods_data, $common_data));

            $sku_arr = array();
            //添加sku商品
            foreach ($data[ 'goods_sku_data' ] as $item) {

                $sku_data = array(
                    'sku_name' => $data[ 'goods_name' ] . ' ' . $item[ 'spec_name' ],
                    'spec_name' => $item[ 'spec_name' ],
                    'sku_no' => $item[ 'sku_no' ],
                    'sku_spec_format' => !empty($item[ 'sku_spec_format' ]) ? json_encode($item[ 'sku_spec_format' ]) : "",
                    'price' => $item[ 'price' ],
                    'market_price' => $item[ 'market_price' ],
                    'cost_price' => $item[ 'cost_price' ],
                    'discount_price' => $item[ 'price' ],//sku折扣价（默认等于单价）
                    'stock' => $item[ 'stock' ],
                    'sku_image' => $item[ 'sku_image' ],
                    'sku_images' => $item[ 'sku_images' ],
                    'goods_id' => $goods_id,

                    'max_buy' => $data['max_buy'],
                    'min_buy' => $data['min_buy']
                );

                $sku_arr[] = array_merge($sku_data, $common_data);
            }

            model('goods_sku')->addList($sku_arr);

            // 赋值第一个商品sku_id
            $first_info = model('goods_sku')->getFirstData(['goods_id' => $goods_id], 'sku_id', 'sku_id asc');
            model('goods')->update(['sku_id' => $first_info[ 'sku_id' ]], [['goods_id', '=', $goods_id]]);

//			添加商品属性关联关系
            $this->refreshGoodsAttribute($goods_id, $data[ 'goods_attr_format' ]);

            if (!empty($data[ 'goods_spec_format' ])) {
//				刷新SKU商品规格项/规格值JSON字符串
                $this->dealGoodsSkuSpecFormat($goods_id, $data[ 'goods_spec_format' ]);
            }

            //添加统计
            $stat = new Stat();
            $stat->addShopStat(['add_goods_count' => 1, 'site_id' => $data[ 'site_id' ]]);

            model('goods')->commit();

            return $this->success($goods_id);
        } catch ( \Exception $e ) {
            model('goods')->rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * 商品编辑
     * @param $data
     */
    public function editGoods($data)
    {

        model('goods')->startTrans();

        try {

            $goods_id = $data[ 'goods_id' ];

            //店铺信息
            $shop_info = model('shop')->getInfo([['site_id', '=', $data[ 'site_id' ]]], 'site_name, website_id, is_own,cert_id,shop_status');

            $goods_config = new Config();
            $goods_verify_config = $goods_config->getVerifyConfig();
            $goods_verify_config = $goods_verify_config[ 'data' ][ 'value' ];
            $verify_state = 1;
            $verify_state_remark = '';
            if (!empty($goods_verify_config[ 'is_open' ])) {
                $verify_state = 0;//开启商品审核后，审核状态为：待审核
            }

            // 店铺未认证、审核中的状态下，商品需要审核
            if (empty($shop_info[ 'cert_id' ]) || $shop_info[ 'shop_status' ] == 0 || $shop_info[ 'shop_status' ] == 2) {
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

            //todo  商品的分佣比率是通过选择的分类来计算的
            $goods_category_model = new GoodsCategory();
            $goods_category_info = $goods_category_model->getCategoryInfo([['category_id', '=', $data[ 'category_id' ]]], 'commission_rate')[ 'data' ] ?? [];
            if (empty($goods_category_info)) {
                model('goods')->rollback();
                return $this->error([], '您选择的分类不存在');
            }
            $commission_rate = $goods_category_info[ 'commission_rate' ];

            $goods_data = array(
                'goods_image' => $goods_image,
                'goods_stock' => $data[ 'goods_stock' ],
                'price' => $data[ 'goods_sku_data' ][ 0 ][ 'price' ],
                'market_price' => $data[ 'goods_sku_data' ][ 0 ][ 'market_price' ],
                'cost_price' => $data[ 'goods_sku_data' ][ 0 ][ 'cost_price' ],
                'goods_spec_format' => $data[ 'goods_spec_format' ],
            );

            $common_data = array(
                'goods_name' => $data[ 'goods_name' ],
                'goods_class' => $this->goods_class[ 'id' ],
                'goods_class_name' => $this->goods_class[ 'name' ],
                'goods_attr_class' => $data[ 'goods_attr_class' ],
                'goods_attr_name' => $data[ 'goods_attr_name' ],
                'site_id' => $data[ 'site_id' ],
                'site_name' => $shop_info[ 'site_name' ],
                'website_id' => $shop_info[ 'website_id' ],
                'category_id' => $data[ 'category_id' ],
                'category_id_1' => $data[ 'category_id_1' ],
                'category_id_2' => $data[ 'category_id_2' ],
                'category_id_3' => $data[ 'category_id_3' ],
                'category_name' => $data[ 'category_name' ],
                'brand_id' => $data[ 'brand_id' ],
                'brand_name' => $data[ 'brand_name' ],
                'goods_content' => $data[ 'goods_content' ],
                'is_own' => $shop_info[ 'is_own' ],
                'goods_state' => $data[ 'goods_state' ],
                'goods_stock_alarm' => $data[ 'goods_stock_alarm' ],
                'is_virtual' => 1,
                'virtual_indate' => $data[ 'virtual_indate' ],
                'goods_attr_format' => $data[ 'goods_attr_format' ],
                'introduction' => $data[ 'introduction' ],
                'keywords' => $data[ 'keywords' ],
                'unit' => $data[ 'unit' ],
                'commission_rate' => $commission_rate,
                'video_url' => $data[ 'video_url' ],
                'sort' => $data[ 'sort' ],
                'verify_state' => $verify_state,
                'verify_state_remark' => $verify_state_remark,
                'goods_shop_category_ids' => $data[ 'goods_shop_category_ids' ],
                'supplier_id' => $data[ 'supplier_id' ],
                'modify_time' => time(),

                'max_buy' => $data['max_buy'],
                'min_buy' => $data['min_buy']
            );

            model('goods')->update(array_merge($goods_data, $common_data), [['goods_id', '=', $goods_id], ['goods_class', '=', $this->goods_class[ 'id' ]]]);

            //判断是否参与显示折扣
            $discount_model = new Discount();
            $discount_info_result = $discount_model->getDiscountGoodsInfo([['pdg.goods_id', '=', $goods_id], ['pd.status', '=', 1]]);
            $discount_info = $discount_info_result[ 'data' ];
            //修改sku商品
            foreach ($data[ 'goods_sku_data' ] as $item) {

                $sku_data = array(
                    'sku_name' => $data[ 'goods_name' ] . ' ' . $item[ 'spec_name' ],
                    'spec_name' => $item[ 'spec_name' ],
                    'sku_no' => $item[ 'sku_no' ],
                    'sku_spec_format' => !empty($item[ 'sku_spec_format' ]) ? json_encode($item[ 'sku_spec_format' ]) : "",
                    'price' => $item[ 'price' ],
                    'market_price' => $item[ 'market_price' ],
                    'cost_price' => $item[ 'cost_price' ],
//					'discount_price' => $item['price'],//sku折扣价（默认等于单价）
                    'stock' => $item[ 'stock' ],
                    'sku_image' => $item[ 'sku_image' ],
                    'sku_images' => $item[ 'sku_images' ],
                    'goods_id' => $goods_id,

                    'max_buy' => $data['max_buy'],
                    'min_buy' => $data['min_buy']
                );
                if (empty($discount_info)) {
                    $sku_data[ 'discount_price' ] = $item[ 'price' ];
                }
                model('goods_sku')->update(array_merge($sku_data, $common_data), [['sku_id', '=', $item[ 'sku_id' ], ['goods_class', '=', $this->goods_class[ 'id' ]]]]);

            }

            // 赋值第一个商品sku_id
            $first_info = model('goods_sku')->getFirstData(['goods_id' => $goods_id], 'sku_id', 'sku_id asc');
            model('goods')->update(['sku_id' => $first_info[ 'sku_id' ]], [['goods_id', '=', $goods_id]]);

//			添加商品属性关联关系
            $this->refreshGoodsAttribute($goods_id, $data[ 'goods_attr_format' ]);

            if (!empty($data[ 'goods_spec_format' ])) {
//				刷新SKU商品规格项/规格值JSON字符串
                $this->dealGoodsSkuSpecFormat($goods_id, $data[ 'goods_spec_format' ]);
            }

            model('goods')->commit();
            return $this->success($goods_id);
        } catch ( \Exception $e ) {
            model('goods')->rollback();
            return $this->error($e->getMessage());
        }
    }

    /**
     * 获取商品信息
     * @param array $condition
     * @param string $field
     */
    public function getGoodsInfo($condition, $field = 'goods_id,goods_name,goods_class,goods_class_name,goods_attr_class,goods_attr_name,category_id,category_id_1,category_id_2,category_id_3,category_name,brand_id,brand_name,goods_image,goods_content,goods_state,verify_state,price,market_price,cost_price,goods_stock,goods_stock_alarm,goods_spec_format,goods_attr_format,introduction,keywords,unit,sort,commission_rate,video_url,evaluate,virtual_indate,goods_shop_category_ids,supplier_id,max_buy,min_buy')
    {
        $info = model('goods')->getInfo($condition, $field);
        return $this->success($info);
    }

    /**
     * 获取商品列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getGoodsList($condition = [], $field = 'goods_id,goods_class,goods_class_name,goods_attr_name,goods_name,site_id,site_name,website_id,sort,category_name,brand_name,goods_image,goods_content,is_own,goods_state,verify_state,price,market_price,cost_price,goods_stock,goods_stock_alarm,is_virtual,goods_spec_format,goods_attr_format,create_time,max_buy,min_buy', $order = 'create_time desc', $limit = null)
    {
        $list = model('goods')->getList($condition, $field, $order, '', '', '', $limit);
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
    public function getGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = 'goods_id,goods_name,site_id,site_name,goods_image,is_own,goods_state,verify_state,price,goods_stock,create_time,sale_num,max_buy,min_buy')
    {
        $list = model('goods')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 获取商品sku列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getGoodsSkuList($condition = [], $field = 'sku_id,sku_name,price,stock,sale_num,sku_image,goods_id,goods_name,site_id,site_name,spec_name,max_buy,min_buy', $order = 'create_time desc', $limit = null)
    {
        $list = model('goods_sku')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 刷新商品关联属性关系
     * @param $goods_id
     * @param $goods_attr_format
     */
    private function refreshGoodsAttribute($goods_id, $goods_attr_format)
    {

        model('goods_attr_index')->delete([['goods_id', '=', $goods_id], ['app_module', '=', 'shop']]);
        if (!empty($goods_attr_format)) {

            $list = model('goods_sku')->getList([['goods_id', '=', $goods_id]], 'sku_id');
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
                        'app_module' => 'shop'
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
        $sku_list = model('goods_sku')->getList([['goods_id', '=', $goods_id], ['sku_spec_format', '<>', '']], 'sku_id,sku_spec_format', 'sku_id asc');
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

                                            if ($current_again_v[ 'spec_value_id' ] == $fan_v[ 'spec_value_id' ]) {
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
                model('goods_sku')->update(['goods_spec_format' => json_encode($sku_format)], [['sku_id', '=', $v[ 'sku_id' ]]]);

            }

//			var_dump("性能：" . $temp);

        }

    }

    /************************************************************************* 购买的虚拟产品 start *******************************************************************/
    /**
     * 生成购买的虚拟产品
     * @param $site_id
     * @param $order_id
     * @param $order_no
     * @param $sku_id
     * @param $sku_name
     * @param $code
     * @param $member_id
     * @param $sku_image
     */
    public function addGoodsVirtual($site_id, $order_id, $order_no, $sku_id, $sku_name, $code, $member_id, $sku_image)
    {
        $data = array(
            "site_id" => $site_id,
            "order_id" => $order_id,
            "order_no" => $order_no,
            "sku_id" => $sku_id,
            "sku_name" => $sku_name,
            "code" => $code,
            "member_id" => $member_id,
            'sku_image' => $sku_image
        );
        $res = model("goods_virtual")->add($data);
        return $this->success($res);
    }


    /**
     * 删除
     * @param $condition
     */
    public function deleteGoodsVirtual($condition)
    {
        $res = model("goods_virtual")->delete($condition);
        if ($res === false)
            return $this->error();

        return $this->success();
    }

    /**
     * 核销虚拟商品
     * @param $code
     */
    public function verify($code)
    {
        $goods_virtual_info = model("goods_virtual")->getInfo([["code", '=', $code], ["is_veirfy", "=", 0]]);
        if (empty($goods_virtual_info))
            return $this->error();//虚拟商品不存在或已核销

        $data = array(
            "is_veirfy" => 1,
            "verify_time" => time()
        );
        $res = model("goods_virtual")->update($data, [["code", '=', $code], ["is_veirfy", "=", 0]]);

        return $this->success($res);
    }


    /**
     * 虚拟商品详情
     * @param $condition
     */
    public function getVirtualGoodsInfo($condition, $field = "*")
    {
        $info = model('goods_virtual')->getInfo($condition, $field);
        return $this->success($info);
    }

    /**
     * 获取虚拟商品分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getVirtualGoodsPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'id desc', $field = '*')
    {
        $list = model('goods_virtual')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }
    /************************************************************************* 购买的虚拟产品 end *******************************************************************/
}