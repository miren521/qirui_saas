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


use app\model\system\Group;
use app\model\BaseModel;
use app\model\express\Config as ConfigModel;
use app\model\shop\ShopAccount as ShopaccountModel;
use app\model\web\WebSite;

/**
 * 店铺申请以及认证信息
 */
class ShopApply extends BaseModel
{
	
	//店铺申请状态
	private $apply_state = [
		-1 => '审核失败',
        -2 => '财务审核失败',
		1 => '待审核',
		2 => '财务凭据审核中',
		3 => '入驻通过',
	];
	
	/**
	 * 申请店铺
	 * @param unknown $apply_data 申请信息
	 * @param unknown $cert_data 认证信息
	 * @param unknown $user_info 用户信息(必传字段 username password(加密过后的) member_id app_module(shop))
	 */
	public function apply($apply_data, $cert_data)
	{
		model('shop_apply')->startTrans();
		try {

            $uid = isset($apply_data['uid']) ? $apply_data['uid'] : 0;

			//添加申请信息
			$apply_money = $this->getApplyMoney($apply_data['apply_year'], $apply_data['group_id'], $apply_data['category_id']);
			$apply_data['paying_deposit'] = $apply_money['code']['paying_deposit'];
			$apply_data['paying_apply'] = $apply_money['code']['paying_apply'];
			$apply_data['paying_amount'] = $apply_money['code']['paying_amount'];

			$apply_data['create_time'] = time();
            $apply_data['apply_no'] = date('YmdHi').rand(1111,9999);
            $apply_data['apply_state'] = 1;

            //获取商家申请信息
            $apply_info = model('shop_apply')->getInfo([ [ 'uid', '=', $uid ] ]);

			if($apply_info){
                //判断认证信息是否存在
                if($apply_info['cert_id'] == 0){
                    //添加认证信息
                    $cert_id = model('shop_cert')->add($cert_data);
                    //添加申请信息
                    $apply_data['cert_id'] = $cert_id;
                    $res = model('shop_apply')->update($apply_data,[[ 'uid', '=', $uid ]]);
                }else{
                    $res = model('shop_apply')->update($apply_data,[[ 'uid', '=', $uid ]]);
                    //修改认证信息
                    model('shop_cert')->update($cert_data,[['cert_id','=',$apply_info['cert_id']]]);
                }

			}else{
                //添加认证信息
                $cert_id = model('shop_cert')->add($cert_data);
                //添加申请信息
                $apply_data['cert_id'] = $cert_id;
                $res = model('shop_apply')->add($apply_data);
            }

			model('shop_apply')->commit();
			return $this->success($res);
		} catch (\Exception $e) {
			model('shop_apply')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}

	/**
	 * 获取申请金额
	 * @param unknown $apply_year
	 * @param unknown $group_id
	 * @param unknown $category_id
	 */
	public function getApplyMoney($apply_year, $group_id, $category_id)
	{
	    $shop_group = new ShopGroup();
	    $group_info = $shop_group->getGroupInfo([['group_id', '=', $group_id]], 'fee');
	    $shop_category = new ShopCategory();
	    $category_info = $shop_category->getCategoryInfo([['category_id', '=', $category_id]], 'baozheng_money');
	    $money = [
	        'paying_deposit' => $category_info['data']['baozheng_money'],
	        'paying_apply'   => number_format($group_info['data']['fee']*$apply_year,2, '.' , ''),
	        'paying_amount'  => number_format($category_info['data']['baozheng_money'] + $group_info['data']['fee']*$apply_year,2, '.' , '')
	    ];
	    return success($money);
	}
	
	/**
	 * 查询申请完整信息
	 * @param unknown $condition
	 */
	public function getApplyDetail($condition)
	{
		
		$field = 'nsa.apply_id, nsa.site_id,nsa.website_id, nsa.member_id, nsa.username, nsa.cert_id, nsa.shop_name, nsa.apply_state, 
		           nsa.apply_message, nsa.apply_year, nsa.category_name, nsa.category_id, nsa.group_name, nsa.group_id, 
		           nsa.paying_money_certificate, nsa.paying_money_certificate_explain, nsa.paying_deposit, nsa.paying_apply, 
		           nsa.paying_amount, nsa.create_time, nsa.audit_time, nsa.finish_time, 
		           nsc.cert_id, nsc.cert_type, nsc.company_name, nsc.company_province_id, nsc.company_city_id, nsc.company_district_id, 
		           nsc.company_address, nsc.contacts_name, nsc.contacts_mobile, nsc.contacts_card_no, nsc.contacts_card_electronic_1, 
		           nsc.contacts_card_electronic_2, nsc.contacts_card_electronic_3, nsc.business_licence_number, 
		           nsc.business_licence_number_electronic, nsc.business_sphere, nsc.taxpayer_id, nsc.general_taxpayer, 
		           nsc.tax_registration_certificate, nsc.tax_registration_certificate_electronic, nsc.bank_account_name, 
		           nsc.bank_account_number, nsc.bank_name, nsc.bank_address, nsc.bank_code, nsc.bank_type, nsc.settlement_bank_account_name, 
		           nsc.settlement_bank_account_number, nsc.settlement_bank_name, nsc.settlement_bank_address,nsc.company_full_address,
		           w.site_area_name';
		$alias = 'nsa';
		$join = [
			[
				'shop_cert nsc',
				'nsa.cert_id = nsc.cert_id',
				'left'
			],
            [
                'website w',
                'w.site_id = nsa.website_id',
                'left'
            ],

		];
		$info = model('shop_apply')->getInfo($condition, $field, $alias, $join);
		return $this->success($info);
	}
	
	/**
	 * 获取申请信息(不包含认证信息)
	 * @param unknown $condition
	 * @param unknown $field
	 */
	public function getApplyInfo($condition, $field = '*')
	{
		$info = model('shop_apply')->getInfo($condition, $field);
		return $this->success($info);
	}


    /**
     * 判断店铺名称是否存在
     * @param $shop_name
     * @return array
     */
	public function shopNameExist($shop_name)
    {
        $apply_count = model('shop_apply')->getCount([[ 'shop_name','=',$shop_name ]]);
        $shop_count = model('shop')->getCount([[ 'site_name','=',$shop_name ]]);
        if($apply_count == 0 && $shop_count==0){
            return $this->success();
        }else{
            return $this->error('','该店铺名称已存在');
        }
    }
	
	/**
	 * 获取店铺申请列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getApplyList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('shop_apply')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取店铺申请分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getApplyPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('shop_apply')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	
	/**
	 * 修改店铺申请
	 * @param array $data
	 */
	public function editApply($data, $condition)
	{
		$res = model('shop_apply')->update($data, $condition);
		return $this->success($res);
	}
	
	/**
	 * 审核通过
	 * @param unknown $apply_id
	 */
	public function applyPass($apply_id)
	{
		$res = model('shop_apply')->update([ 'apply_state' => 2, 'audit_time' => time() ], [ [ 'apply_id', '=', $apply_id ] ]);
		return $this->success($res);
	}
	
	/**
	 * 审核拒绝
	 * @param unknown $apply_id
	 * @param unknown $reason
	 */
	public function applyReject($apply_id, $reason)
	{
		$res = model('shop_apply')->update([ 'apply_state' => -1, 'apply_message' => $reason ], [ [ 'apply_id', '=', $apply_id ] ]);
		return $this->success($res);
	}
	
	/**
	 * 支付凭证（上传）
	 * @param unknown $data
	 * @param unknown $apply_id
	 */
	public function pay($data, $apply_id)
	{
		$res = model('shop_apply')->update($data, [ [ 'apply_id', '=', $apply_id ] ]);
		return $this->success($res);
	}

    /**
     * 入驻通过
     * @param $apply_id
     * @param $apply_message
     * @return array
     */
	public function openShop($apply_id,$apply_message='')
	{
        //检测店铺是否已存在
        $apply_info = model('shop_apply')->getInfo([ [ 'apply_id', '=', $apply_id ] ]);
        if ($apply_info['site_id'] != 0) {
            $shop_info = model("shop")->getInfo([ [ 'site_id', '=', $apply_info['site_id'] ] ]);
            if ($shop_info['cert_id'] == 0) {
                $res = $this->certOpenShop($apply_id,$apply_message);
                return $res;
            }else{
                model('shop_apply')->rollback();
                return $this->error('', 'SHOP_EXISTED');
            }
        }
		model('shop_apply')->startTrans();
		try {

			//添加系统站
			$site_id = model("site")->add([ 'site_type' => 'shop' ]);

			//获取用户账户信息
            $user_info = model('user')->getInfo([ ['uid','=',$apply_info['uid']] ],'username');
			//添加店铺
			$shop_data = [
				'site_id' => $site_id,
				'site_name' => $apply_info['shop_name'],
                'username' => $user_info['username'],
				'expire_time' => time() + 365 * 24 * 3600 * $apply_info['apply_year'],
				'website_id' => $apply_info['website_id'],
				'level_id' => $apply_info['level_id'],
				'level_name' => $apply_info['level_name'],
				'group_id' => $apply_info['group_id'],
				'group_name' => $apply_info['group_name'],
				'category_id' => $apply_info['category_id'],
				'category_name' => $apply_info['category_name'],
				'member_id' => $apply_info['uid'],
				'member_name' => $apply_info['member_name'],
				'cert_id' => $apply_info['cert_id'],
                'shop_baozhrmb' => $apply_info['paying_deposit'],
			    'shop_open_fee' => $apply_info['paying_apply'],
			    'create_time' => time()
			];
			model("shop")->add($shop_data);
			//点击支付保证金凭据
			if ($apply_info['paying_deposit'] > 0) {
				$data_deposit = [
				    'deposit_no' => date('YmdHi').rand(1111,9999),
					'site_id' => $site_id,
                    'site_name' => $apply_info['shop_name'],
					'money' => $apply_info['paying_deposit'],
					'pay_certificate' => $apply_info['paying_money_certificate'],
					'pay_certificate_explain' => $apply_info['paying_money_certificate_explain'],
					'remark' => '入驻支付保证金',
					'status' => 1,
					'create_time' => time(),
					'audit_time' => time()
				];
				model("shop_deposit")->add($data_deposit);
			}
			//添加入驻费用流水
            if($apply_info['paying_apply'] > 0){

			    if($apply_info['website_id'] > 0){
                    //获取分站信息
                    $website_model = new WebSite();
                    $website_info = $website_model->getWebSite([ ['site_id','=',$apply_info['website_id']] ],'site_area_name,shop_rate');
                    $website_name = $website_info['data']['site_area_name'];
                    if(isset($website_info['data']['shop_rate']) && $website_info['data']['shop_rate'] > 0){
                        $website_commission = floor($apply_info['paying_apply']*$website_info['data']['shop_rate'])/100;
                    }else{
                        $website_commission = 0;
                    }
                }else{
                    $website_name = '全国';
                    $website_commission = $apply_info['paying_apply'];
                }

                $open_shop_data = [
			        'account_no' => $apply_info['apply_no'],
                    'site_id' => $site_id,
                    'site_name' => $apply_info['shop_name'],
                    'money' => $apply_info['paying_apply'],
                    'type' => 1,
                    'type_name' => '店铺入驻费用',
                    'relate_id' => $apply_info['apply_id'],
                    'create_time' => time(),
                    'website_id' => $apply_info['website_id'],
                    'website_name' => $website_name,
                    'website_commission' => $website_commission
                ];
			    model('shop_open_account')->add($open_shop_data);
            }

			//添加系统用户组
			$group = new Group();
			$group_data = [
				'site_id' => $site_id,
				'app_module' => 'shop',
				'group_name' => '管理员组',
				'is_system' => 1,
				'create_time' => time()
			];
			$group_id = $group->addGroup($group_data)['data'];
			
			//更新管理员信息
			model("user")->update(
			    [ 'group_id' => $group_id,'group_name' => '管理员组', 'site_id' => $site_id, 'app_group' => $apply_info['group_id'] ],
                [ [ 'uid', '=', $apply_info['uid'] ], [ 'app_module', '=', 'shop' ] ]);
			//更新认证信息
			model("shop_cert")->update([ 'site_id' => $site_id ], [ [ 'cert_id', '=', $apply_info['cert_id'] ] ]);
			model("shop_apply")->update([ 'apply_state' => 3, 'site_id' => $site_id,'apply_message'=>$apply_message ], [ [ 'apply_id', '=', $apply_id ] ]);
			// 添加店铺相册默认分组
            model("album")->add(['site_id' => $site_id, 'album_name' => "默认分组", 'update_time' => time(), 'is_default' => 1]);
			//执行事件
			event("AddShop", [ 'site_id' => $site_id ]);
			model('shop_apply')->commit();
			
            $config_model = new ConfigModel();
            $is_use = 1;
            $config_model->setExpressConfig([], $is_use,$site_id );
			return $this->success($site_id);
		} catch (\Exception $e) {
			model('shop_apply')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 获取店铺申请状态
	 */
	public function getApplyState()
	{
		return $this->apply_state;
	}

    /**
     * 体验入驻
     * @param $shop_data
     * @param $user_info
     */
	public function experienceApply($shop_data,$user_info)
    {
        model('shop')->startTrans();
        try {
            //检测店铺名否已存在
            $shop_count = model("shop")->getCount([ [ 'site_name', '=', $shop_data['site_name'] ] ]);
            if ($shop_count > 0) {
                model('shop')->rollback();
                return $this->error('', 'SHOP_EXISTED');
            }
            //检测用户是否已经入驻
            $user = model('user')->getInfo([[ 'uid','=',$user_info['uid'] ],['app_module','=','shop']],'site_id');
            if($user['site_id'] != 0){
                model('shop')->rollback();
                return $this->error('', '请不要重复入驻');
            }

            //添加系统站
            $site_id = model("site")->add([ 'site_type' => 'shop' ]);

            //获取店铺体验入驻设置
            $config_model = new ShopaccountModel();
            $config = $config_model->getShopWithdrawConfig();
            $config_info = $config['data']['value'];

            //获取店铺等级
            $group_name = model('shop_group')->getInfo([['group_id','=',$config_info['group_id']]],'group_name');

            //添加店铺
            $data = [
                'site_id' => $site_id,
                'website_id' => $shop_data['website_id'],
                'site_name' => $shop_data['site_name'],
                'username' => $user_info['username'],
                'expire_time' => strtotime("+".$config_info['expire_time']."days",time()),
                'category_id' => $shop_data['category_id'],
                'category_name' => $shop_data['category_name'],
                'group_id' => $config_info['group_id'],
                'group_name' => $group_name['group_name'],
                'create_time' => time()
            ];

            model("shop")->add($data);
            //添加店铺申请

//            $shop_apply_data = [
//                'site_id' => $site_id,
//                'shop_name' => $shop_data['site_name'],
//                'username' => $user_info['username'],
//                'apply_state' => 3,
//                'category_id' => $shop_data['category_id'],
//                'category_name' => $shop_data['category_name'],
//                'group_id' => $config_info['group_id'],
//                'group_name' => $group_name['group_name'],
//                'create_time' => time(),
//                'audit_time' => time(),
//                'finish_time' => time(),
//                'uid' => $user_info['uid']
//            ];
//
//            model("shop_apply")->add($shop_apply_data);

            //添加系统用户组
            $group = new Group();
            $group_data = [
                'site_id' => $site_id,
                'app_module' => 'shop',
                'group_name' => '管理员组',
                'is_system' => 1,
                'create_time' => time()
            ];

            $group_id = $group->addGroup($group_data)['data'];

            //更新管理员信息
            model("user")->update(
                [ 'group_id' => $group_id,'group_name' => '管理员组', 'site_id' => $site_id, 'app_group' => $config_info['group_id'] ],
                [ [ 'uid', '=', $user_info['uid'] ], [ 'app_module', '=', 'shop' ] ]);

            // 添加店铺相册默认分组
            model("album")->add(['site_id' => $site_id, 'album_name' => "默认分组", 'update_time' => time(), 'is_default' => 1]);
            //执行事件
            event("AddShop", [ 'site_id' => $site_id ]);
            model('shop')->commit();

            $config_model = new ConfigModel();
            $is_use = 1;
            $config_model->setExpressConfig([], $is_use,$site_id );

            return $this->success($site_id);
        } catch (\Exception $e) {
            model('shop')->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 认证入驻通过
     * @param $apply_id
     * @param $apply_message
     * @return array
     */
    public function certOpenShop($apply_id,$apply_message = '')
    {
        model('shop_apply')->startTrans();
        try {

            $apply_info = model('shop_apply')->getInfo([ [ 'apply_id', '=', $apply_id ] ]);
            if ($apply_info['apply_state'] == 3) {

                model('shop_apply')->rollback();
                return $this->success();
            }

            //修改店铺信息
            $shop_data = [
                'expire_time' => strtotime('+'.$apply_info['apply_year'].'year',time()),
                'website_id' => $apply_info['website_id'],
                'level_id' => $apply_info['level_id'],
                'level_name' => $apply_info['level_name'],
                'group_id' => $apply_info['group_id'],
                'group_name' => $apply_info['group_name'],
                'member_id' => $apply_info['uid'],
                'member_name' => $apply_info['member_name'],
                'cert_id' => $apply_info['cert_id'],
                'shop_baozhrmb' => $apply_info['paying_deposit'],
                'shop_open_fee' => $apply_info['paying_apply']
            ];
            model("shop")->update($shop_data,[ ['site_id','=',$apply_info['site_id']] ]);

            //点击支付保证金凭据
            if ($apply_info['paying_deposit'] > 0) {
                $data_deposit = [
                    'deposit_no' => date('YmdHi').rand(1111,9999),
                    'site_id' => $apply_info['site_id'],
                    'site_name' => $apply_info['shop_name'],
                    'money' => $apply_info['paying_deposit'],
                    'pay_certificate' => $apply_info['paying_money_certificate'],
                    'pay_certificate_explain' => $apply_info['paying_money_certificate_explain'],
                    'remark' => '入驻支付保证金',
                    'status' => 1,
                    'create_time' => time(),
                    'audit_time' => time()
                ];
                model("shop_deposit")->add($data_deposit);
            }
            //添加入驻费用流水
            if($apply_info['paying_apply'] > 0){

                if($apply_info['website_id'] > 0){
                    //获取分站信息
                    $website_model = new WebSite();
                    $website_info = $website_model->getWebSite([ ['site_id','=',$apply_info['website_id']] ],'site_area_name,shop_rate');
                    $website_name = $website_info['data']['site_area_name'];
                    if($website_info['data']['shop_rate'] > 0){
                        $website_commission = floor($apply_info['paying_apply']*$website_info['data']['shop_rate'])/100;
                       
                    }else{
                        $website_commission = 0;
                    }
                }else{
                    $website_name = '全国';
                    $website_commission = $apply_info['paying_apply'];
                }

                $open_shop_data = [
                    'account_no' => $apply_info['apply_no'],
                    'site_id' => $apply_info['site_id'],
                    'site_name' => $apply_info['shop_name'],
                    'money' => $apply_info['paying_apply'],
                    'type' => 1,
                    'type_name' => '店铺入驻费用',
                    'relate_id' => $apply_info['apply_id'],
                    'create_time' => time(),
                    'website_id' => $apply_info['website_id'],
                    'website_name' => $website_name,
                    'website_commission' => $website_commission
                ];
                model('shop_open_account')->add($open_shop_data);
            }

            //更新管理员信息
            model("user")->update(
                [ 'app_group' => $apply_info['group_id'] ],
                [ [ 'uid', '=', $apply_info['uid'] ], [ 'app_module', '=', 'shop' ] ]
            );

            $res = model("shop_apply")->update([ 'apply_state' => 3,'apply_message'=>$apply_message ], [ [ 'apply_id', '=', $apply_id ] ]);

            model('shop_cert')->update(['site_id'=>$apply_info['site_id']],[[ 'cert_id','=',$apply_info['cert_id']]]);

            //执行事件
            model('shop_apply')->commit();

            return $this->success($res);
        } catch (\Exception $e) {
            model('shop_apply')->rollback();
            return $this->error('', $e->getMessage());
        }

    }

    /**
     * 获取申请店铺数
     * @param array $condition
     */
    public function getShopApplyCount($condition = [])
    {
        $res = model('shop_apply')->getCount($condition);
        return $this->success($res);
    }
}