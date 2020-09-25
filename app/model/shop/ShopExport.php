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


namespace app\model\shop;

use app\model\BaseModel;

/**
 * 店铺导出
 */
class ShopExport extends BaseModel
{
    //商家导出字段
    public $shop_field = array(
        'expire_time' => '到期时间',
        'site_name' => '店铺名称',
        'username' => '用户账号',
        'is_own' => '是否自营',
        'level_name' => '开店套餐名称',
        'category_name' => '店铺类别名称',
        'group_name' => '开店套餐',
        'member_name' => '创建会员名称',
        'shop_status' => '店铺经营状态',
        'close_info' => '店铺关闭原因',
        'start_time' => '经营时间',
        'end_time' => '关闭时间',
        'seo_keywords' => '店铺关键字',
        'seo_description' => '店铺简介',
        'qq' => '联系人qq',
        'ww' => '联系人阿里旺旺',
        'telephone' => '联系电话',
        'is_recommend' => '是否推荐',
        'shop_desccredit' => '描述分值',
        'shop_servicecredit' => '服务分值',
        'shop_deliverycredit' => '发货速度分值',
        'workingtime' => '工作时间',
        'shop_baozh' => '保证服务开关',
        'shop_baozhopen' => '保证金显示开关',
        'shop_baozhrmb' => '保证金金额',
        'shop_qtian' => '7天退换',
        'shop_zhping' => '正品保障',
        'shop_erxiaoshi' => '两小时发货',
        'shop_tuihuo' => '退货承诺',
        'shop_shiyong' => '试用中心',
        'shop_shiti' => '实体验证',
        'shop_xiaoxie' => '消协保证',
        'shop_free_time' => '商家配送时间',
        'shop_sales' => '店铺销量',
        'account' => '账户流水',
        'work_week' => '工作日',
        'full_address' => '地址',
        'sub_num' => '关注会员数'
    );
    //商家导出字段
    public $shop_cert_field = array(
        'cert_type' => '申请类型',
        'company_name' => '公司名称',
        'company_address' => '公司地址',
        'contacts_name' => '联系人姓名',
        'contacts_mobile' => '联系人手机',
        'contacts_card_no' => '联系人身份证',
        'business_licence_number' => '统一社会信用码',
        'business_sphere' => '法定经营范围',
        'taxpayer_id' => '纳税人识别号',
        'general_taxpayer' => '一般纳税人证明',
        'tax_registration_certificate' => '税务登记证号',
        'bank_account_name' => '银行开户名',
        'bank_account_number' => '公司银行账号',
        'bank_name' => '开户银行支行名称',
        'bank_address' => '开户银行所在地',
        'bank_code' => '支行联行号',
        'bank_type' => '结算账户类型',
        'settlement_bank_account_name' => '结算银行开户名',
        'settlement_bank_account_number' => '结算公司银行账号',
        'settlement_bank_name' => '结算开户银行支行名称',
        'settlement_bank_address' => '结算开户银行所在地',
    );

    public $define_data = [
        'expire_time' => ['type' => 1], //到期时间
        'start_time' => ['type' => 1], //经营时间
        'end_time' => ['type' => 1], //关闭时间
        'workingtime' => ['type' => 1], //工作时间
        'shop_free_time' => ['type' => 1], //商家配送时间
        'is_own' => ['type' => 2, 'data' => ['否', '是']],//是否自营
        'is_recommend' => ['type' => 2, 'data' => ['否', '是']],//是否推荐
        'shop_baozh' => ['type' => 2, 'data' => ['关闭', '开启']],//保证服务开关
        'shop_baozhopen' => ['type' => 2, 'data' => ['关闭', '开启']],//保证金显示开关
        'shop_qtian' => ['type' => 2, 'data' => ['关闭', '开启']],//7天退换
        'shop_zhping' => ['type' => 2, 'data' => ['关闭', '开启']],//正品保障
        'shop_erxiaoshi' => ['type' => 2, 'data' => ['关闭', '开启']],//两小时发货
        'shop_tuihuo' => ['type' => 2, 'data' => ['关闭', '开启']],//退货承诺
        'shop_shiyong' => ['type' => 2, 'data' => ['关闭', '开启']],//试用中心
        'shop_shiti' => ['type' => 2, 'data' => ['关闭', '开启']],//实体验证
        'shop_xiaoxie' => ['type' => 2, 'data' => ['关闭', '开启']],//消协保证
        'cert_type' => ['type' => 2, 'data' => [1 => '个人', 2 => '公司']],//申请类型
        'bank_type' => ['type' => 2, 'data' => [1 => '银行卡', 2 => '支付宝']],//结算账户类型
        'shop_status' => ['type' => 2, 'data' => ['关闭', '正常', '审核中']],
    ];

    //数据处理
    public function handleData($data, $field)
    {
        $define_data = $this->define_data;
        foreach ($data as $k => $v) {
            //获取键
            $keys = array_keys($v);

            foreach ($keys as $key) {

                if (in_array($key, $field)) {

                    if (array_key_exists($key, $define_data)) {

                        $type = $define_data[$key]['type'];

                        switch ($type) {

                            case 1:
                                $data[$k][$key] = time_to_date($v[$key]);
                                break;
                            case 2:
                                $define_data_data = $define_data[$key]['data'];
                                $data[$k][$key] = !empty($v[$key]) ? $define_data_data[$v[$key]] : '';
                        }

                    }
                }
            }

        }
        return $data;
    }
}