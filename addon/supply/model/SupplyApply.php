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

use app\model\system\Group;
use app\model\BaseModel;
use app\model\express\Config as ConfigModel;
use Exception;
use think\facade\Cache;

/**
 * 供货商申请以及认证信息
 */
class SupplyApply extends BaseModel
{

    //申请状态
    private $apply_state = [
        -1 => '审核失败',
        -2 => '财务审核失败',
        1  => '待审核',
        2  => '财务凭据审核中',
        3  => '入驻通过',
    ];

    /**
     * 申请
     * @param $apply_data
     * @param $cert_data
     * @return array
     */
    public function apply($apply_data, $cert_data)
    {
        model('supply_apply')->startTrans();
        try {
            $uid = isset($apply_data['uid']) ? $apply_data['uid'] : 0;

            //添加申请信息
            $apply_money = $this->getApplyMoney($apply_data['apply_year'], $apply_data['category_id']);
            $apply_data['paying_deposit'] = $apply_money['code']['paying_deposit'];
            $apply_data['paying_apply']   = $apply_money['code']['paying_apply'];
            $apply_data['paying_amount']  = $apply_money['code']['paying_amount'];

            $apply_data['create_time'] = time();
            $apply_data['apply_no']    = date('YmdHi') . rand(1111, 9999);
            $apply_data['apply_state'] = 1;

            //获取商家申请信息
            $apply_info = model('supply_apply')->getInfo([['uid', '=', $uid]]);

            if ($apply_info) {
                //判断认证信息是否存在
                if ($apply_info['cert_id'] == 0) {
                    //添加认证信息
                    $cert_id = model('supply_cert')->add($cert_data);
                    //添加申请信息
                    $apply_data['cert_id'] = $cert_id;
                    $res = model('supply_apply')->update($apply_data, [['uid', '=', $uid]]);
                } else {
                    $res = model('supply_apply')->update($apply_data, [['uid', '=', $uid]]);
                    //修改认证信息
                    model('supply_cert')->update($cert_data, [['cert_id', '=', $apply_info['cert_id']]]);
                }
            } else {
                //添加认证信息
                $cert_id = model('supply_cert')->add($cert_data);
                //添加申请信息
                $apply_data['cert_id'] = $cert_id;
                $res = model('supply_apply')->add($apply_data);
            }

            model('supply_apply')->commit();
            return $this->success($res);
        } catch (Exception $e) {
            model('supply_apply')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取申请金额
     * @param $apply_year
     * @param $category_id
     * @return array
     */
    public function getApplyMoney($apply_year, $category_id)
    {
        // 保证金
        $category = new SupplyCategory();
        $category_info = $category->getCategoryInfo([['category_id', '=', $category_id]], 'baozheng_money');
        $baozheng = $category_info['data']['baozheng_money'];

        // 开店费用
        $config_model = new Config();
        $config = $config_model->getSupplyConfig();
        $fee = $config['data']['value']['fee'];

        $money = [
            'paying_deposit' => empty($baozheng) ? 0 : $baozheng,
            'paying_apply'   => number_format($fee * $apply_year, 2, '.', ''),
            'paying_amount'  => number_format($baozheng + ($fee * $apply_year), 2, '.', '')
        ];
        return success($money);
    }

    /**
     * 查询申请完整信息
     * @param $condition
     * @return array
     */
    public function getApplyDetail($condition)
    {
        $field = 'nsa.apply_id, nsa.site_id,nsa.website_id, nsa.member_id, nsa.username, nsa.cert_id, nsa.supplier_name,
            nsa.apply_state, nsa.apply_message, nsa.apply_year, nsa.category_name, nsa.category_id, nsa.group_name,
            nsa.group_id, nsa.paying_money_certificate, nsa.paying_money_certificate_explain, nsa.paying_deposit,
            nsa.paying_apply, nsa.paying_amount, nsa.create_time, nsa.audit_time, nsa.finish_time, nsc.cert_id,
            nsc.cert_type, nsc.company_name, nsc.company_province_id, nsc.company_city_id, nsc.company_district_id, 
            nsc.company_address, nsc.contacts_name, nsc.contacts_mobile, nsc.contacts_card_no, nsc.contacts_card_electronic_1, 
            nsc.contacts_card_electronic_2, nsc.contacts_card_electronic_3, nsc.business_licence_number,
            nsc.business_licence_number_electronic, nsc.business_sphere, nsc.taxpayer_id, nsc.general_taxpayer, 
            nsc.tax_registration_certificate, nsc.tax_registration_certificate_electronic, nsc.bank_account_name, 
            nsc.bank_account_number, nsc.bank_name, nsc.bank_address, nsc.bank_code, nsc.bank_type, nsc.settlement_bank_account_name, 
            nsc.settlement_bank_account_number, nsc.settlement_bank_name, nsc.settlement_bank_address,
            nsc.company_full_address, w.site_area_name';
        $alias = 'nsa';
        $join  = [
            [
                'supply_cert nsc',
                'nsa.cert_id = nsc.cert_id',
                'left'
            ],
            [
                'website w',
                'w.site_id = nsa.website_id',
                'left'
            ],

        ];
        $info  = model('supply_apply')->getInfo($condition, $field, $alias, $join);
        return $this->success($info);
    }

    /**
     * 获取申请信息(不包含认证信息)
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getApplyInfo($condition, $field = '*')
    {
        $info = model('supply_apply')->getInfo($condition, $field);
        return $this->success($info);
    }

    /**
     * 判断供货商名称是否存在
     * @param $supplier_name
     * @return array
     */
    public function shopNameExist($supplier_name)
    {
        $apply_count = model('supply_apply')->getCount([['supplier_name', '=', $supplier_name]]);
        $count  = model('supplier')->getCount([['title', '=', $supplier_name]]);
        if ($apply_count == 0 && $count == 0) {
            return $this->success();
        } else {
            return $this->error('', '该供货商名称已存在');
        }
    }

    /**
     * 获取供货商申请列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getApplyList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('supply_apply')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取供货商申请分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getApplyPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('supply_apply')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

    /**
     * 修改供货商申请
     * @param $data
     * @param $condition
     * @return array
     */
    public function editApply($data, $condition)
    {
        $res = model('supply_apply')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 审核通过
     * @param $apply_id
     * @return array
     */
    public function applyPass($apply_id)
    {
        $res = model('supply_apply')->update(
            ['apply_state' => 2, 'audit_time' => time()],
            [['apply_id', '=', $apply_id]]
        );
        return $this->success($res);
    }

    /**
     * 审核拒绝
     * @param $apply_id
     * @param $reason
     * @return array
     */
    public function applyReject($apply_id, $reason)
    {
        $res = model('supply_apply')->update(
            ['apply_state' => -1, 'apply_message' => $reason],
            [['apply_id', '=', $apply_id]]
        );
        return $this->success($res);
    }

    /**
     * 支付凭证（上传）
     * @param $data
     * @param $apply_id
     * @return array
     */
    public function pay($data, $apply_id)
    {
        $res = model('supply_apply')->update($data, [['apply_id', '=', $apply_id]]);
        return $this->success($res);
    }

    /**
     * 入驻通过
     * @param $apply_id
     * @param string $apply_message
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function openSupply($apply_id, $apply_message = '')
    {
        //检测供货商是否已存在
        $apply_info = model('supply_apply')->getInfo([['apply_id', '=', $apply_id]]);
        if ($apply_info['site_id'] != 0) {
            $info = model("supplier")->getInfo([['supplier_site_id', '=', $apply_info['site_id']]]);
            if ($info['cert_id'] == 0) {
                $res = $this->certOpenSupply($apply_id, $apply_message);
                return $res;
            } else {
                model('supply_apply')->rollback();
                return $this->error('', 'SUPPLIER_EXISTED');
            }
        }
        $cert = model('supply_cert')->getInfo([['cert_id', '=', $apply_info['cert_id']]]);
        model('supply_apply')->startTrans();
        try {
            //添加系统站
            $site_id = model("site")->add(['site_type' => 'supply']);

            //获取用户账户信息
            $user_info = model('user')->getInfo([['uid', '=', $apply_info['uid']]], 'username');
            //添加供货商
            $supplier_data = [
                'supplier_site_id' => $site_id,
                'title'            => $apply_info['supplier_name'],
                'username'         => $user_info['username'],
                'expire_time'      => time() + 365 * 24 * 3600 * $apply_info['apply_year'],
                'supplier_phone'   => $cert['contacts_mobile'],
                'supplier_address' => $cert['company_full_address'],
                'category_id'      => $apply_info['category_id'],
                'category_name'    => $apply_info['category_name'],
                'cert_id'          => $apply_info['cert_id'],
                'bond'             => $apply_info['paying_deposit'],
                'open_fee'         => $apply_info['paying_apply'],
                'status'           => 1,
                'create_time'      => time()
            ];
            model("supplier")->add($supplier_data);
            //点击支付保证金凭据
            if ($apply_info['paying_deposit'] > 0) {
                $data_deposit = [
                    'deposit_no' => date('YmdHi') . rand(1111, 9999),
                    'site_id' => $site_id,
                    'site_name' => $apply_info['supplier_name'],
                    'money' => $apply_info['paying_deposit'],
                    'pay_certificate' => $apply_info['paying_money_certificate'],
                    'pay_certificate_explain' => $apply_info['paying_money_certificate_explain'],
                    'remark' => '入驻支付保证金',
                    'status' => 1,
                    'create_time' => time(),
                    'audit_time' => time()
                ];
                model("supply_deposit")->add($data_deposit);
            }
            //添加入驻费用流水
            if ($apply_info['paying_apply'] > 0) {
                $website_name       = '全国';
                $website_commission = $apply_info['paying_apply'];

                $open_data = [
                    'account_no'         => $apply_info['apply_no'],
                    'site_id'            => $site_id,
                    'site_name'          => $apply_info['supplier_name'],
                    'money'              => $apply_info['paying_apply'],
                    'type'               => 1,
                    'type_name'          => '供货商入驻费用',
                    'relate_id'          => $apply_info['apply_id'],
                    'create_time'        => time(),
                    'website_name'       => $website_name,
                    'website_commission' => $website_commission
                ];
                model('supply_open_account')->add($open_data);
            }

            //添加系统用户组
            $group      = new Group();
            $group_data = [
                'site_id'     => $site_id,
                'app_module'  => 'supply',
                'group_name'  => '管理员组',
                'is_system'   => 1,
                'create_time' => time()
            ];
            $group_id   = $group->addGroup($group_data)['data'];

            //更新管理员信息
            model("user")->update(
                [
                    'group_id' => $group_id, 'group_name' => '管理员组',
                    'site_id' => $site_id, 'app_group' => $apply_info['group_id']
                ],
                [['uid', '=', $apply_info['uid']], ['app_module', '=', 'supply']]
            );
            //更新认证信息
            model("supply_cert")->update(['site_id' => $site_id], [['cert_id', '=', $apply_info['cert_id']]]);
            model("supply_apply")->update(
                ['apply_state' => 3, 'site_id' => $site_id, 'apply_message' => $apply_message],
                [['apply_id', '=', $apply_id]]
            );
            // 添加供货商相册默认分组
            model("album")->add([
                'site_id' => $site_id,
                'album_name' => "默认分组",
                'update_time' => time(),
                'is_default' => 1,
                'app_module' => 'supply'
            ]);
            //执行事件
            model('supply_apply')->commit();
            Cache::tag("supply")->clear();
            $config_model = new ConfigModel();
            $is_use       = 1;
            $config_model->setExpressConfig([], $is_use, $site_id);
            return $this->success($site_id);
        } catch (Exception $e) {
            model('supply_apply')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取供货商申请状态
     */
    public function getApplyState()
    {
        return $this->apply_state;
    }

    /**
     * 认证入驻通过
     * @param $apply_id
     * @param $apply_message
     * @return array
     */
    public function certOpenSupply($apply_id, $apply_message = '')
    {
        model('supply_apply')->startTrans();
        try {
            $apply_info = model('supply_apply')->getInfo([['apply_id', '=', $apply_id]]);
            if ($apply_info['apply_state'] == 3) {
                model('supply_apply')->rollback();
                return $this->success();
            }

            //修改供货商信息
            $supplier_data = [
                'expire_time' => strtotime('+' . $apply_info['apply_year'] . 'year', time()),
                'group_id'    => $apply_info['group_id'],
                'group_name'  => $apply_info['group_name'],
                'cert_id'     => $apply_info['cert_id'],
                'bond'        => $apply_info['paying_deposit'],
                'status'      => 1,
                'open_fee'    => $apply_info['paying_apply']
            ];
            model("supplier")->update($supplier_data, [['supplier_site_id', '=', $apply_info['site_id']]]);

            //点击支付保证金凭据
            if ($apply_info['paying_deposit'] > 0) {
                $data_deposit = [
                    'deposit_no'              => date('YmdHi') . rand(1111, 9999),
                    'site_id'                 => $apply_info['site_id'],
                    'site_name'               => $apply_info['supplier_name'],
                    'money'                   => $apply_info['paying_deposit'],
                    'pay_certificate'         => $apply_info['paying_money_certificate'],
                    'pay_certificate_explain' => $apply_info['paying_money_certificate_explain'],
                    'remark'                  => '入驻支付保证金',
                    'status'                  => 1,
                    'create_time'             => time(),
                    'audit_time'              => time()
                ];
                model("supply_deposit")->add($data_deposit);
            }
            //添加入驻费用流水
            if ($apply_info['paying_apply'] > 0) {
                $open_supplier_data = [
                    'account_no'         => $apply_info['apply_no'],
                    'site_id'            => $apply_info['site_id'],
                    'site_name'          => $apply_info['supplier_name'],
                    'money'              => $apply_info['paying_apply'],
                    'type'               => 1,
                    'type_name'          => '供货商入驻费用',
                    'relate_id'          => $apply_info['apply_id'],
                    'create_time'        => time(),
                ];
                model('supply_open_account')->add($open_supplier_data);
            }

            //更新管理员信息
            model("user")->update(
                ['app_group' => $apply_info['group_id']],
                [['uid', '=', $apply_info['uid']], ['app_module', '=', 'supply']]
            );

            $res = model("supply_apply")->update(
                ['apply_state' => 3, 'apply_message' => $apply_message],
                [['apply_id', '=', $apply_id]]
            );

            model('supply_cert')->update(
                ['site_id' => $apply_info['site_id']],
                [['cert_id', '=', $apply_info['cert_id']]]
            );

            //执行事件
            model('supply_apply')->commit();

            return $this->success($res);
        } catch (Exception $e) {
            model('supply_apply')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取申请供货商数
     * @param array $condition
     * @return array
     */
    public function getShopApplyCount($condition = [])
    {
        $res = model('supply_apply')->getCount($condition);
        return $this->success($res);
    }
}
