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
use app\model\shop\ShopCategory;

/**
 * 商品导出记录
 */
class GoodsExport extends BaseModel
{

    /**
     * 添加导出记录
     * @param $data
     * @return array
     */
    public function addExport($data)
    {
        $res = model("goods_export")->add($data);
        return $this->success($res);
    }

    /**
     * 更新导出记录
     * @param $data
     * @return array
     */
    public function editExport($data, $condition)
    {
        $res = model("goods_export")->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 删除导出记录
     * @param $data
     * @return array
     */
    public function deleteExport($condition)
    {
        $res = model("goods_export")->delete($condition);
        return $this->success($res);
    }

    /**
     * 获取导出记录
     * @param $member_id
     * @return array
     */
    public function getExport($condition, $field = "*", $order = '')
    {

        $list = model("goods_export")->getList($condition, $field, $order);
        return $this->success($list);
    }

    /**
     * 导出商品数据
     * @param $condition
     */
    public function exportData($condition, $condition_desc = []){
        try {
            $column_condition = array_column($condition, 2, 0);
            $site_id = $column_condition[ 'site_id' ] ?? 0;
//            if($site_id > 0){
//                $shop_category_model = new GoodsShopCategory();
//                $shop_category_list = $shop_category_model->getShopCategoryList([['site_id', '=', $site_id]])['data'] ?? [];
//                $shop_category_list = array_column($shop_category_list, 'category_name', 'category_id');
//            }

//            $condition_dict = array(
//                'goods_name' => ['name' => '商品名称'],
//                'goods_class' => ['name' => '商品类型'],
//                'goods_state' => ['name' => '上架状态', 'dict' => [1=>'正常', 0 => '下架']],
//                'verify_state' => ['name' => '审核状态', 'dict' => [1=>'已审核', 0 => '待审核', 10 => '违规下架', -1 => '审核中', -2 => '审核失败']],
//                'sale_num' => ['name' => '销量'],
//                'price' => ['name' => '价格'],
////                'goods_shop_category_ids' => ['name' => '店内分类', 'dict' => $shop_category_list ?? []],
//            );
            $field_dict = array(
                'goods_id' => ['name' => '商品id'],
                'goods_name' => ['name' => '商品名称'],
                'sku_id' => ['name' => '规格id'],
                'spec_name' => ['name' => '商品规格'],
                'goods_class_name' => ['name' => '商品类型'],
                'sku_no' => ['name' => '规格编号'],
                'price' => ['name' => '销售价'],
                'market_price' => ['name' => '市场价'],
                'cost_price' => ['name' => '成本价'],
                'stock' => ['name' => '库存'],
                'category_name' => ['name' => '所属分类'],
                'brand_name' => ['name' => '商品品牌'],
                'goods_state' => ['name' => '商品状态', 'dict' => [1=>'正常', 0 => '下架']],
                'verify_state' => ['name' => '审核状态', 'dict' => [1=>'已审核', 0 => '待审核', 10 => '违规下架', -1 => '审核中', -2 => '审核失败']],
                'click_num' => ['name' => '点击量'],
                'sale_num' => ['name' => '销量'],
                'collect_num' => ['name' => '收藏量'],
            );

            $field_string = implode(',', array_keys($field_dict));
            $goods_model = new Goods();
            //将要导出的商品数据
            $list = $goods_model->getGoodsSkuList($condition, $field_string)['data'] ?? [];

            $temp_val = [];
            $temp_key = [];
            foreach ($field_dict as $k => $v) {
                $temp_val[] = $v['name'];
                $temp_key[] = "{\$$k}";
            }

            //特殊值的转换
            foreach($list as $k => $v) {
                foreach ($v as $v_k => $v_v){
                    $tron_item = $field_dict[ $v_k ] ?? [];
                    if (!empty($tron_item)) {
                        $tron_dict_item = $tron_item[ 'dict' ] ?? [];
                        if (!empty($tron_dict_item)) {
                            $list[$k][ $v_k ] = $tron_dict_item[ $v_v ];
                        }
                    }
                }
            }

            //导出csv文件
            $file_path = __UPLOAD__ . "/common/csv/" . date("Ymd") . '/';
            $file_name = time() . '.csv';
            if (dir_mkdir($file_path)) {
                $file_name = downloadCsv($temp_val, $temp_key, $file_path . $file_name, $list);
//                $new_column_condition = array_column($condition, null, 0);
//                //格式化可读格式的数组形式
//                $condition_desc = [];
//                foreach($new_column_condition as $k => $v){
//                    $item = $condition_dict[$k] ?? [];
//                    $name = $item['name'] ?? '';
//                    //判断符号
//                    $value = $v[1] != '=' && $v[1] != '%' ? $v[1] : '';
//                    if(!empty($item)){
//                        $dict = $item['dict'] ?? [];
//                        if(!empty($dict)){
//                            $value .= $dict[$v[2]];
//                        }else{
//                            $value .= $v[2];
//                        }
//
//                    }
//                    $condition_desc[] = ['name' => $name, 'value' => $value];
//                }

                //创建记录
                $data = array(
                    'condition' => json_encode($condition_desc),
                    'status' => 1,
                    'create_time' => time(),
                    'path' => $file_name,
                    'site_id' => $site_id
                );
                $result = $this->addExport($data);
                return $result;
            }else{
                return $this->error([]);
            }
        }catch (\Exception $e){
            return $this->error([], $e->getMessage().$e->getLine());
        }


    }

}
