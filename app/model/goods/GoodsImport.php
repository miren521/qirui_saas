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


use app\model\BaseModel;
use Carbon\Carbon;
use think\facade\Cache;
use think\facade\Session;

/**
 * 商品导入
 */
class GoodsImport extends BaseModel
{
    /**
     * 导入商品
     * @param $data
     */
    public function importGoods($data, $table_head, $table_line, $site_id)
    {
        try {
            if (!isset($site_id))
                return $this->error('', 'REQUEST_SITE_ID');

            //判断是否有导入数据
            if (empty($data))
                return $this->error();

            //导入商品数据
            $goods_model = new Goods();
            $virtual_goods_model = new VirtualGoods();
            //先根据goods_id 分配好单个商品个体(区分出多规格的情况)
            $temp_list = [];
            $temp_category_list = [];
            $error_data = [];
            $success_data = [];
            $error_count = 0;
            $goods_category_model = new GoodsCategory();
            $goods_brand_model = new GoodsBrand();
            $goods_attribute_model = new GoodsAttribute();
            foreach ($data as $k => $v) {

                $init_sku_data = $v;
                $goods_image_array = explode(',', $v['goods_image']);

                if (!isset($temp_list[$v['goods_id']])) {
                    //处理分类, 处理分类的数据结构
                    $item_goods_category = $v['goods_category'];
                    $item_goods_category_array = explode(',', $item_goods_category);
                    if ($v['goods_class'] == 1) {
                        $is_free_shipping = $v['is_free_shipping'] ?? 0;
                    } else {
                        $virtual_indate = $v['virtual_indate'] ?? 1;
                    }
                    $item_temp = array(
                        'list' => [],
                        'init_list' => [],
                        'site_id' => $site_id,
                        'goods_name' => $v['goods_name'],
                        'goods_class' => $v['goods_class'],

                        'category_list' => $item_goods_category_array,
                        'brand_id' => $v['brand'] ?? 0,
                        'goods_content' => $v['goods_content'],//商品详情
                        'is_free_shipping' => $is_free_shipping ?? 0,//是否免邮
                        'introduction' => $v['introduction'],//促销语
                        'keywords' => $v['keywords'],//关键词
                        'unit' => $v['unit'],//单位
                        'goods_image' => $goods_image_array[0] ?? '',//默认的商品图
                        'video_url' => $v['video_url'],//取用第一条的数据信息
                        'goods_stock' => 0,
                        'goods_attr_class' => '',//无商品类型
                        'goods_attr_name' => '',
                        'goods_content' => $v['goods_content'],
                        'goods_attr_format' => '',
                        'goods_state' => 0,
                        'goods_stock_alarm' => $v['goods_stock_alarm'],
                        'shipping_template' => 0,
                        'sort' => 0,
                        'goods_shop_category_ids' => '',
                        'supplier_id' => 0,//供应商(暂时不设置)
                        'virtual_indate' => $virtual_indate ?? 1,//虚拟商品有效期
                        'price' => $v['price'],
                        'market_price' => $v['market_price'],
                        'cost_price' => $v['cost_price'],
                        'sku_no' => $v['sku_no'],
                        'weight' => $v['weight'],//虚拟商品不录入重量
                        'volume' => $v['volume'],//虚拟商品不录入体积
                    );

                    $temp_list[$v['goods_id']] = $item_temp;
                }
                //统计库存
                $temp_list[$v['goods_id']]['goods_stock'] += $v['stock'] ?? 0;
                //规格的属性整合
                //分析传入的规格与属性
                $spec_array = [];
                $v['item_sku_data'] = [];
                if (!empty($v['spec_list'])) {
                    //以英文逗号分开规格个体
                    $spec_array = [];
                    $temp_spec_list = explode(',', $v['spec_list']);
                    foreach ($temp_spec_list as $spec_k => $spec_v) {
                        $temp_spec_item = explode(':', $spec_v);
                        $spec_array[$temp_spec_item[0]][] = $temp_spec_item[1];
                        $v['item_sku_data'][$temp_spec_item[0]] = $temp_spec_item[1];
                    }

                    if (empty($temp_list[$v['goods_id']]['spec_list'])) {
                        $temp_list[$v['goods_id']]['spec_list'] = $spec_array;
                    } else {
                        foreach ($spec_array as $spec_k => $spec_v) {
                            //当前属性是否存在
                            if (!isset($temp_list[$v['goods_id']]['spec_list'][$spec_k])) {
                                $temp_list[$v['goods_id']]['spec_list'][$spec_k] = [];
                            }
                            //通过合并达到去重目的
                            $temp_list[$v['goods_id']]['spec_list'][$spec_k] = array_unique(array_merge($temp_list[$v['goods_id']]['spec_list'][$spec_k], $spec_v));
                        }
                    }
                }

                //维护商品个体的规格列表信息
                $spec_item_data = $v;
                $spec_item_data['goods_image_array'] = $goods_image_array;
                $spec_item_data['spec'] = $spec_array;//格式化规格信息
                $temp_list[$v['goods_id']]['list'][] = $spec_item_data;
                $temp_list[$v['goods_id']]['init_list'][] = $init_sku_data;

            }
            $error_sku_list = [];

            $error_export_list = [];
            $success_export_list = [];
            //整理 结构化的数据
            foreach ($temp_list as $k => $v) {
                //配置导入数据的site_id
                $data[$k]['site_id'] = $site_id;
                //补充site_name  站点名称 website_id  所属分站id
                // 分类本地化
                if (!empty($v['category_list'])) {

                    //导入的分类与本地的分类的比对(导入分类没有创建分类的权利, 暂时只要没有合适的分类就视为分发数据,不进行创建)
                    if (isset($item_goods_category_array[2])) {
                        $item_condition = array(
                            ['category_name', '=', $item_goods_category_array[2]],
                            ['level', '=', 3]
                        );
                        $category_result = $goods_category_model->getCategoryInfo($item_condition, 'category_id, category_id_1, category_id_2, category_id_3, category_full_name')['data'] ?? [];
                        $item_category_id = $category_result['category_id_3'] ?? 0;
                    } else if (isset($item_goods_category_array[1])) {
                        //如果由第二级分类
                        $item_condition = array(
                            ['category_name', '=', $item_goods_category_array[1]],
                            ['level', '=', 2]
                        );
                        $category_result = $goods_category_model->getCategoryInfo($item_condition, 'category_id, category_id_1, category_id_2, category_id_3,category_full_name')['data'] ?? [];
                        $item_category_id = $category_result['category_id_2'] ?? 0;
                    } else if (isset($item_goods_category_array[0])) {
                        //如果只有第一级分类
                        $item_condition = array(
                            ['category_name', '=', $item_goods_category_array[0]],
                            ['level', '=', 1]
                        );
                        $category_result = $goods_category_model->getCategoryInfo($item_condition, 'category_id, category_id_1, category_id_2, category_id_3,category_full_name')['data'] ?? [];
                        $item_category_id = $category_result['category_id_1'] ?? 0;
                    }

                    //分类是必须项,如果没有有效的分类,就跳过
                    $item_category = $category_result ?? [];
                    if (!empty($item_category)) {

                        //todo  类型以及属性的本地化

                        //图片同意整理,最后批量拉取到本地(更具设置)
                        //分类
                        $temp_list[$k]['category_id'] = $item_category_id;
                        $temp_list[$k]['category_id_1'] = $category_result['category_id_1'] ?? 0;
                        $temp_list[$k]['category_id_2'] = $category_result['category_id_2'] ?? 0;
                        $temp_list[$k]['category_id_3'] = $category_result['category_id_3'] ?? 0;
                        $temp_list[$k]['category_name'] = $category_result['category_full_name'] ?? '';


                        //整理品牌
                        if (!empty($v['brand_id'])) {
                            $item_brand_condition = array(
                                ['brand_name', '=', $v['brand_id']]
                            );
                            $brand_info = $goods_brand_model->getBrandInfo($item_brand_condition, 'brand_id, brand_name')['data'] ?? [];
                            //没有当前的品牌就创建一个品牌
                            if (empty($brand_info)) {
                                $item_brand_data = array(
                                    'brand_name' => $v['brand_id'],
                                    'site_id' => $site_id,
                                );
                                $item_goods_brand_result = $goods_brand_model->addBrand($item_brand_data);
                                if ($item_goods_brand_result['code'] >= 0) {
                                    $brand_id = $item_goods_brand_result['data'];
                                    $brand_name = $v['brand_id'];
                                }
                            } else {
                                $brand_id = $brand_info['brand_id'];
                                $brand_name = $brand_info['brand_name'];
                            }
                        }

                        $temp_list[$k]['brand_id'] = $brand_id ?? 0;
                        $temp_list[$k]['brand_name'] = $brand_name ?? '';

                        //整理规格
                        $spec_dict = [];
                        //分析传入的规格与属性
                        $spec_array = [];

                        if (!empty($v['spec_list'])) {

                            $spec_count = 0;
                            foreach ($v['spec_list'] as $spec_k => $spec_v) {
                                //先判断规格是否存在
                                $spec_value_count = 0;
                                $goods_attribute_info = $goods_attribute_model->getGoodsAttributeInfo([['attr_name', '=', $spec_k], ['site_id', '=', $site_id]])['data'] ?? [];

                                if (!empty($goods_attribute_info)) {
                                    //查询本地是否存在
                                    $spec_array_item = array(
                                        'spec_id' => $goods_attribute_info['attr_id'],
                                        'spec_name' => $spec_k,
                                        'value' => []
                                    );
                                    $spec_value_list = $goods_attribute_model->getSpecValueList([['attr_id', '=', $goods_attribute_info['attr_id']]], '')['data'] ?? [];

                                    if (!empty($spec_value_list)) {
                                        $spec_value_list = array_column($spec_value_list, 'attr_value_id', 'attr_value_name');
                                        foreach ($spec_v as $sprc_item_k => $spec_item_v) {
                                            if (!empty($spec_value_list[$spec_item_v])) {
                                                $temp_spec_value_item = array(
                                                    "spec_id" => $goods_attribute_info['attr_id'],
                                                    "spec_name" => $goods_attribute_info['attr_name'],
                                                    "spec_value_id" => $spec_value_list[$spec_item_v],
                                                    "spec_value_name" => $spec_item_v,
                                                    "image" => ""
                                                );
                                            } else {
                                                $second = date('s');//当前的秒
                                                $time = time() * 1000;
                                                $precise_time = Carbon::now()->getPreciseTimestamp(3);
                                                $precise_time = $precise_time - $time;
                                                $spec_value_id = -($spec_value_count + $second + $precise_time);
                                                $temp_spec_value_item = array(
                                                    "spec_id" => $goods_attribute_info['attr_id'],
                                                    "spec_name" => $goods_attribute_info['attr_name'],
                                                    "spec_value_id" => $spec_value_id,
                                                    "spec_value_name" => $spec_item_v,
                                                    "image" => ""
                                                );
                                                $spec_value_count++;
                                            }
                                            $spec_dict[] = $temp_spec_value_item;
                                            $spec_array_item['value'][] = $temp_spec_value_item;
                                        }
                                    }
                                } else {
                                    $second = date('s');//当前的秒
                                    $time = time() * 1000;
                                    $precise_time = Carbon::now()->getPreciseTimestamp(3);

                                    $precise_time = $precise_time - $time;
                                    $spec_array_item = array(
                                        'spec_id' => -($spec_count + $second + $precise_time),
                                        'spec_name' => $spec_k,
                                        'value' => []
                                    );
                                    $spec_count++;
                                    foreach ($spec_v as $sprc_item_k => $spec_item_v) {
                                        $second = date('s');//当前的秒
                                        $time = time() * 1000;
                                        $precise_time = Carbon::now()->getPreciseTimestamp(3);
                                        $precise_time = $precise_time - $time;
                                        $spec_value_id = -($spec_value_count + $second + $precise_time);
                                        $temp_spec_value_item = array(
                                            "spec_id" => $spec_array_item['spec_id'],
                                            "spec_name" => $spec_k,
                                            "spec_value_id" => $spec_value_id,
                                            "spec_value_name" => $spec_item_v,
                                            "image" => ""
                                        );
                                        $spec_array_item['value'][] = $temp_spec_value_item;
                                        $spec_dict[] = $temp_spec_value_item;
                                        $spec_value_count++;
                                    }
                                }
                                $spec_array[] = $spec_array_item;
                            }
                        }
                        //用于规格个体的值的翻译
                        $spec_dict = array_column($spec_array, null, 'spec_name');
                        $goods_sku_data = [];
                        //单规格模式
                        foreach ($v['list'] as $spec_list_k => $spec_list_v) {
                            $sku_spec_format = [];
                            $item_goods_image_array = $spec_list_v['goods_image_array'];
                            //判断规格是否存在
                            $item_goods_sku_data = [
                                'price' => $spec_list_v['price'],
                                'market_price' => $spec_list_v['market_price'],
                                'cost_price' => $spec_list_v['cost_price'],
                                'stock' => $spec_list_v['stock'],
                                'weight' => $spec_list_v['weight'],
                                'volume' => $spec_list_v['volume'],
                                'sku_no' => $spec_list_v['sku_no'],
                                'sku_image' => $item_goods_image_array[0],
                                'sku_images' => implode(',', $item_goods_image_array),
                                'sku_images_arr' => $item_goods_image_array,
                            ];

                            //通过商品查询到的规格数据,补全各个规格的信息数据
                            $spec_name = '';
                            if (!empty($spec_list_v['item_sku_data'])) {
                                $sku_spec_format = [];
                                foreach ($spec_list_v['item_sku_data'] as $item_sku_data_k => $item_sku_data_v) {
                                    //判断规格是否被实现
                                    $temp_spec_dict = $spec_dict[$item_sku_data_k] ?? [];
                                    if (!empty($temp_spec_dict)) {
                                        $item_sku_spec_format = array(
                                            "spec_id" => $temp_spec_dict['spec_id'],
                                            "spec_name" => $temp_spec_dict['spec_name'],
                                        );
                                        $spec_name .= $temp_spec_dict['spec_name'];
                                        $temp_spec_value_dict = $temp_spec_dict['value'] ?? [];
                                        if (!empty($temp_spec_value_dict)) {
                                            $temp_spec_value_dict_column = array_column($temp_spec_value_dict, 'spec_value_id', 'spec_value_name');
                                            $spec_value_id = $temp_spec_value_dict_column[$item_sku_data_v] ?? 0;
                                        }
                                        $item_sku_spec_format['spec_value_id'] = $spec_value_id;
                                        $item_sku_spec_format['spec_value_name'] = $spec_value_id;
                                        $sku_spec_format[] = $item_sku_spec_format;
                                    }
                                }
                            }
                            $item_goods_sku_data['spec_name'] = $spec_name;
                            $item_goods_sku_data['sku_spec_format'] = $sku_spec_format;
                            $goods_sku_data[] = $item_goods_sku_data;
                        }


                        if (count($v['list']) > 1) {
                            $temp_list[$k]['goods_spec_format'] = json_encode($spec_array);
                        } else {
                            $temp_list[$k]['goods_spec_format'] = '';
                        }
                        $temp_list[$k]['goods_sku_data'] = json_encode($goods_sku_data);
                        $goods_item = $temp_list[$k];

                        $goods_item['max_buy'] = 0;
                        $goods_item['min_buy'] = 0;
                        if ($goods_item['goods_class'] == 1) {
                            $goods_result = $goods_model->addGoods($goods_item);
                        } else {
                            $goods_result = $virtual_goods_model->addGoods($goods_item);
                        }

                        if ($goods_result['code'] >= 0) {
                            $success_data[] = $goods_item['goods_name'];

                            $success_export_list = array_merge($success_export_list, $v['init_list']);
                        } else {
                            $item_error = $goods_result['message'];
                            $error_data[] = $goods_result['message'];
                            $sku_error_list = $v['list'];
                            foreach ($sku_error_list as $sku_error_k => $sku_error_v) {
                                $sku_error_list[$sku_error_k]['error'] = $goods_result['message'];
                            }
                            $error_sku_list = array_merge($error_sku_list, $sku_error_list);
                        }
                    }else{
                        $item_error = '不是有效的分类';
                        $error_data[] = '不是有效的分类';
                        $sku_error_list = $v['list'];
                        foreach ($sku_error_list as $sku_error_k => $sku_error_v) {
                            $sku_error_list[$sku_error_k]['error'] = '不是有效的分类';
                        }
                        $error_sku_list = array_merge($error_sku_list, $sku_error_list);
//                        continue;
                    }
                }else{
                    $item_error = '分类为必填项';
                    $error_data[] = '分类为必填项';
                    $sku_error_list = $v['list'];
                    foreach ($sku_error_list as $sku_error_k => $sku_error_v) {
                        $sku_error_list[$sku_error_k]['error'] = '分类为必填项';
                    }
                    $error_sku_list = array_merge($error_sku_list, $sku_error_list);
//                    continue;//没有分类直接视为非法数据,进行下一个
                }
                if(!empty($item_error)){
                    $item_init_list = $v['init_list'];
                    foreach($item_init_list as $item_init_k => $item_init_v){
                        $item_init_list[$item_init_k]['error'] = $item_error;//错误原因
                    }
                    $error_export_list = array_merge($error_export_list, $item_init_list);
                }
            }
            //如果有错误导出数据
            if(!empty($error_export_list)){

                $table_head[] = '错误原因';
                $table_line[] = 'error';
                $error_count_data = array(
                    'table_head' => $table_head,
                    'table_line' => $table_line,
                    'data_list' => $error_export_list
                );
                //存取错误导出数据用于导出
                $key = 'error_import_list' . md5(uniqid(null, true));
                Cache::tag('error_import')->set($key, $error_count_data, 3600);
                Session::set('error_import_data', $error_sku_list);
            }

            $error_count = count($error_export_list);
            $success_count = count($success_export_list);
            return $this->success(['error_count' => $error_count, 'success_count' => $success_count, 'error_key' => $key ?? '']);
        } catch ( \Exception $e ) {
            return $this->error([], $e->getMessage() . $e->getFile() . $e->getLine());
        }
    }

    /**
     * 可以调用错误的导出记录,生成新的csv用于生成新的
     */
    public function exportError($error_key)
    {
        //获取错误的导入数据
        $error_export_array = Cache::get($error_key);
        if(!empty($error_export_array)){
            $cache_table_head = $error_export_array['table_head'] ?? [];
            $cache_temp_line = $error_export_array['table_line'] ?? [];
            $cache_data_list = $error_export_array['data_list'] ?? [];

            $head_html = implode(',', $cache_table_head);

            $html = str_replace("\n", '', $head_html) . "\n";
            $line_html = implode(',', $cache_temp_line);
            $html .= str_replace("\n", '', $line_html) . "\n";
            $temp_line = [];
            foreach($cache_temp_line as $temp_line_k => $temp_line_v){
                $temp_line[] = "{\$$temp_line_v}";
            }
            $tempLine = implode(',', $temp_line) . "\n";

            foreach ($cache_data_list as $listvalue) {
                $newvalue = $tempLine;
                foreach ($listvalue as $key => $value) {
                    //CSV比较简单，记得转义 逗号就好
                    $values = str_replace(',', '\\', $value);
                    $newvalue = str_replace("{\$$key}", $values, $newvalue);
                }
                $html .= $newvalue ;
            }

            $filename = date('Ymd his').'.csv'; //设置文件名
            header("Content-type:text/csv");
            header("Content-Disposition:attachment;filename=".$filename);
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            exit(mb_convert_encoding($html, "GBK", "UTF-8"));
        }
    }
}
