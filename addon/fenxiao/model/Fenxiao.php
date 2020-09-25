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

namespace addon\fenxiao\model;

use app\model\BaseModel;


/**
 * 分销
 */
class Fenxiao extends BaseModel
{
	
	public $fenxiao_status_zh = [
		1 => '正常',
		-1 => '冻结',
	];
	
	
	/**
	 * 添加分销商
	 * @param $data
	 * @return mixed
	 */
	public function addFenxiao($data)
	{
		$fenxiao_info = model('fenxiao')->getInfo([ [ 'member_id', '=', $data['member_id'] ] ], 'fenxiao_id');
		if (!empty($fenxiao_info)) return $this->error('', '当前分销商已申请');
		
		$data['fenxiao_no'] = date('YmdHi') . rand(1000, 9999);
		$data['create_time'] = time();
		$data['audit_time'] = time();
		
		model('fenxiao')->startTrans();
		try {
			
			if (!empty($data['parent'])) {
				//获取上级分销商信息
				$parent_info = $this->getFenxiaoInfo([ [ 'fenxiao_id', '=', $data['parent'] ] ], 'parent');
				//添加上级分销商一级下线人数
				model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $data['parent'] ] ], 'one_child_fenxiao_num');
				//获取上上级分销商id
				$grand_parent_id = model('fenxiao')->getInfo([ [ 'fenxiao_id', '=', $data['parent'] ] ], 'parent');
				
				if (!empty($grand_parent_id) || $grand_parent_id['parent'] != 0) {
					//添加上上级分销商二级下线人数
					model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $grand_parent_id['parent'] ] ], 'two_child_fenxiao_num');
				}
				
				// 分销商检测升级
				event('FenxiaoUpgrade', $data['parent']);
			}
			
			$res = model('fenxiao')->add($data);
			//修改会员信息
			model('member')->update([ 'fenxiao_id' => $res, 'is_fenxiao' => 1 ], [ [ 'member_id', '=', $data['member_id'] ] ]);
			
			model('fenxiao')->commit();
			return $this->success($res);
		} catch (\Exception $e) {
			model('fenxiao')->rollback();
			return $this->error('', $e->getMessage());
		}
		
	}
	
	/**
	 * 冻结
	 * @param $fenxiao_id
	 * @return array
	 */
	public function frozen($fenxiao_id)
	{
		$data = [
			'status' => -1,
			'lock_time' => time()
		];
		
		$res = model('fenxiao')->update($data, [ [ 'fenxiao_id', '=', $fenxiao_id ] ]);
		return $this->success($res);
	}
	
	/**
	 * 解冻
	 * @param $fenxiao_id
	 * @return array
	 */
	public function unfrozen($fenxiao_id)
	{
		$data = [
			'status' => 1,
			'lock_time' => time()
		];
		
		$res = model('fenxiao')->update($data, [ [ 'fenxiao_id', '=', $fenxiao_id ] ]);
		return $this->success($res);
	}
	
	/**
	 * 获取分销商详细信息
	 * @param array $condition
	 * @param string $field
	 * @return array
	 */
	public function getFenxiaoInfo($condition = [], $field = '*')
	{
		$res = model('fenxiao')->getInfo($condition, $field);
		return $this->success($res);
	}
	
	/**
	 * 获取分销商详细信息
	 * @param array $condition
	 * @param string $field
	 * @return array
	 */
	public function getFenxiaoDetailInfo($condition = [])
	{
		$field = 'f.*,pf.fenxiao_name as parent_name,nm.nickname,nm.headimg';
		$alias = 'f';
		$join = [
			[
				'fenxiao pf',
				'pf.fenxiao_id = f.parent',
				'left'
			],
			[
				'member nm',
				'nm.member_id = f.member_id',
				'inner'
			]
		];
		$res = model('fenxiao')->getInfo($condition, $field, $alias, $join);
		return $this->success($res);
	}
	
	
	/**
	 * 获取分销列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getFenxiaoList($condition = [], $field = '*', $order = '', $limit = null)
	{
		
		$list = model('fenxiao')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取分销分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getFenxiaoPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '')
	{
		$field = 'f.*,pf.fenxiao_name as parent_name';
		$alias = 'f';
		$join = [
			[
				'fenxiao pf',
				'pf.fenxiao_id = f.parent',
				'left'
			]
		];
		$list = model('fenxiao')->pageList($condition, $field, $order, $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
	/**
	 * 获取分销商团队
	 * @param unknown $level
	 * @param unknown $fenxiao_id
	 * @param number $page
	 * @param string $page_size
	 */
	public function getFenxiaoTeam($level, $fenxiao_id, $page = 1, $page_size = PAGE_LIST_ROWS)
	{
		$condition = '';
		$prefix = config("database")["connections"]["mysql"]["prefix"];
		switch ($level) {
			case 0:
				$condition = "m.fenxiao_id = {$fenxiao_id} AND m.is_fenxiao = 0";
				break;
			case 1:
				$condition = "f.parent = {$fenxiao_id}";
				break;
			case 2:
				$data = model('fenxiao')->query("SELECT GROUP_CONCAT(fenxiao_id) as fenxiao_id FROM {$prefix}fenxiao WHERE parent = {$fenxiao_id}");
				if (isset($data[0]) && isset($data[0]['fenxiao_id']) && !empty($data[0]['fenxiao_id'])) {
					$fenxiao_id = $data[0]['fenxiao_id'];
					$condition = "f.parent in ({$fenxiao_id})";
				}
				break;
		}
		
		if (empty($condition)) return $this->success([
			'page_count' => 1,
			'count' => 0,
			'list' => []
		]);
		
		$field = 'm.member_id,m.nickname,m.headimg,m.is_fenxiao,m.reg_time,m.order_money,m.order_complete_money,m.order_num,m.order_complete_num,f.fenxiao_id,f.fenxiao_no,f.fenxiao_name,f.audit_time';
		$alias = 'm';
		$join = [
			[ 'fenxiao f', 'm.member_id = f.member_id', 'left' ]
		];
		$list = model('member')->pageList($condition, $field, '', $page, $page_size, $alias, $join);
		return $this->success($list);
	}
	
	/**
	 * 查询我的团队的数量
	 * @param unknown $fenxiao_id
	 * @return array
	 */
	public function getFenxiaoTeamNum($fenxiao_id)
	{
		// 查询分销基础配置
		$config_model = new Config();
		$fenxiao_basic_config = $config_model->getFenxiaoBasicsConfig();
		$level = $fenxiao_basic_config['data']['value']['level'];
		
		$prefix = config("database")["connections"]["mysql"]["prefix"];
		
		$num = 0;
		switch ($level) {
			case 2:
				$data = model('fenxiao')->query("SELECT GROUP_CONCAT(fenxiao_id) as fenxiao_id FROM {$prefix}fenxiao WHERE parent = {$fenxiao_id}");
				if (isset($data[0]) && isset($data[0]['fenxiao_id']) && !empty($data[0]['fenxiao_id'])) {
					$fenxiao_id = $data[0]['fenxiao_id'];
					$num = count(explode(',', $fenxiao_id));
				}
				break;
			case 3:
				$two_data = model('fenxiao')->query("SELECT GROUP_CONCAT(fenxiao_id) as fenxiao_id FROM {$prefix}fenxiao WHERE parent = {$fenxiao_id}");
				if (isset($two_data[0]) && isset($two_data[0]['fenxiao_id']) && !empty($two_data[0]['fenxiao_id'])) {
					$two_fenxiao_ids = $two_data[0]['fenxiao_id'];
					$num += count(explode(',', $two_fenxiao_ids));
					
					$three_data = model('fenxiao')->query("SELECT GROUP_CONCAT(fenxiao_id) as fenxiao_id FROM {$prefix}fenxiao WHERE parent in ({$two_fenxiao_ids})");
					if (isset($three_data[0]) && isset($three_data[0]['fenxiao_id']) && !empty($three_data[0]['fenxiao_id'])) {
						$three_fenxiao_ids = $three_data[0]['fenxiao_id'];
						$num += count(explode(',', $three_fenxiao_ids));
					}
				}
				break;
		}
		return $this->success($num);
	}
	
	/**
	 * 会员注册之后
	 * @param unknown $member_id
	 */
	public function memberRegister($member_id)
	{
		$config = new Config();
		
		// 分销商基础设置
		$basics_config = $config->getFenxiaoBasicsConfig();
		$basics_config = $basics_config['data']['value'];
		
		// 如果开启分销功能
		if ($basics_config['level']) {
			// 成为分销商的资格
			$fenxiao_config = $config->getFenxiaoConfig();
			$fenxiao_config = $fenxiao_config['data']['value'];
			
			// 成为下线条件为：首次点击分享链接成为下线
//     		$config_info = $config->getFenxiaoRelationConfig();
//     		$child_condition = $config_info['data']['value']['child_condition'];
//     		if ($child_condition == 1) {
			$this->bindRelation($member_id);
//     		}
			
			// 成为分销商无任何条件且无需完善资料 则直接成为分销商
			if (!$fenxiao_config['fenxiao_condition']) {
				// 分销商是否需要审核
				if ($basics_config['is_examine']) {
					$fenxiao_apply = new FenxiaoApply();
					$fenxiao_apply->applyFenxiao($member_id);
				} else {
					$this->directlyBecomeFenxiao($member_id);
				}
			}
		}
	}
	
	/**
	 * 自动成为分销商
	 * @param unknown $member_id
	 */
	public function autoBecomeFenxiao($member_id)
	{
		$config = new Config();
		
		// 分销商基础设置
		$basics_config = $config->getFenxiaoBasicsConfig();
		$basics_config = $basics_config['data']['value'];
		
		// 如果开启分销功能
		if ($basics_config['level']) {
			// 成为分销商的资格
			$fenxiao_config = $config->getFenxiaoConfig();
			$fenxiao_config = $fenxiao_config['data']['value'];
			
			// 成为分销商无任何条件且无需完善资料 则直接成为分销商
			if (!$fenxiao_config['fenxiao_condition']) {
				// 分销商是否需要审核
				if ($basics_config['is_examine']) {
					$fenxiao_apply = new FenxiaoApply();
					$res = $fenxiao_apply->applyFenxiao($member_id);
					return $res;
				} else {
					$res = $this->directlyBecomeFenxiao($member_id);
					return $res;
				}
			}
		}
	}
	
	/**
	 * 会员直接成为分销商
	 */
	private function directlyBecomeFenxiao($member_id)
	{
		//获取用户信息
		$member_field = 'member_id,source_member,fenxiao_id,nickname,headimg,mobile,reg_time,order_money,order_complete_money,order_num,order_complete_num';
		$member_info = model('member')->getInfo([ [ 'member_id', '=', $member_id ] ], $member_field);
		
		if (!empty($member_info)) {
			$parent = 0;
			if (!empty($member_info['source_member'])) {
				$fenxiao_info = model('fenxiao')->getInfo([ [ 'member_id', '=', $member_info['source_member'] ] ], 'fenxiao_id');
				if (!empty($fenxiao_info)) $parent = $fenxiao_info['fenxiao_id'];
			}
			//获取分销等级信息
			$level_model = new FenxiaoLevel();
			$level_info = $level_model->getMinLevel([], 'level_id,level_name');
			
			$data = [
				'fenxiao_name' => $member_info['nickname'],
				'mobile' => $member_info['mobile'],
				'member_id' => $member_info['member_id'],
				'parent' => $parent,
				'level_id' => $level_info['data']['level_id'],
				'level_name' => $level_info['data']['level_name']
			];
			$res = $this->addFenxiao($data);
			return $res;
		}
	}
	
	/**
	 * 绑定上下线关系
	 * @param unknown $member_id
	 */
	public function bindRelation($member_id)
	{
		$member_info = model('member')->getInfo([ [ 'member_id', '=', $member_id ], [ 'fenxiao_id', '=', '' ] ], 'member_id,source_member');
		
		if (!empty($member_info) && !empty($member_info['source_member'])) {
			// 查询推荐人是否是分销商
			$fenxiao_info = model('fenxiao')->getInfo([ [ 'member_id', '=', $member_info['source_member'] ] ], 'fenxiao_id,parent');
			if (!empty($fenxiao_info)) {
				model('member')->startTrans();
				try {
					$member_data = [
						'fenxiao_id' => $fenxiao_info['fenxiao_id']
					];
					model('member')->update($member_data, [ [ 'member_id', '=', $member_id ] ]);
					model('fenxiao')->setInc([ [ 'fenxiao_id', '=', $fenxiao_info['fenxiao_id'] ] ], 'one_child_num');
					// 分销商检测升级
					event('FenxiaoUpgrade', $fenxiao_info['fenxiao_id']);
					model('member')->commit();
				} catch (\Exception $e) {
					model('member')->rollback();
				}
			}
		}
	}
	
	/**
	 * 分销商检测升级
	 * @param unknown $fenxiao_id
	 */
	public function fenxiaoUpgrade($fenxiao_id)
	{
		$join = [
			[ 'member m', 'f.member_id = m.member_id', 'inner' ],
			[ 'fenxiao_level fl', 'f.level_id = fl.level_id', 'inner' ]
		];
		$fenxiao_info = model('fenxiao')->getInfo([ [ 'f.fenxiao_id', '=', $fenxiao_id ], [ 'f.status', '=', 1 ] ], 'f.level_id,m.order_num,m.order_money,f.one_fenxiao_order_num,f.one_fenxiao_order_money,f.one_child_num,f.one_child_fenxiao_num,fl.one_rate', 'f', $join);
		if (!empty($fenxiao_info)) {
			$level_list = model('fenxiao_level')->getList([ [ 'one_rate', '>=', $fenxiao_info['one_rate'] ], [ 'level_id', '<>', $fenxiao_info['level_id'] ] ], '*', 'one_rate desc');
			if (!empty($level_list)) {
				$upgrade_level = null;
				foreach ($level_list as $item) {
					if ($item['upgrade_type'] == 2) {
						if ($fenxiao_info['order_num'] >= $item['order_num'] && $fenxiao_info['order_money'] >= $item['order_money'] && $fenxiao_info['one_fenxiao_order_num'] >= $item['one_fenxiao_order_num'] && $fenxiao_info['one_fenxiao_order_money'] >= $item['one_fenxiao_order_money'] && $fenxiao_info['one_child_num'] >= $item['one_child_num'] && $fenxiao_info['one_child_fenxiao_num'] >= $item['one_child_fenxiao_num']) {
							$upgrade_level = $item;
							break;
						}
					} else {
						if (($fenxiao_info['order_num'] >= $item['order_num'] && $item['order_num'] > 0) || ($fenxiao_info['order_money'] >= $item['order_money'] && $item['order_money'] > 0) || ($fenxiao_info['one_fenxiao_order_num'] >= $item['one_fenxiao_order_num'] && $item['one_fenxiao_order_num'] > 0) || ($fenxiao_info['one_fenxiao_order_money'] >= $item['one_fenxiao_order_money'] && $item['one_fenxiao_order_money'] > 0) || ($fenxiao_info['one_child_num'] >= $item['one_child_num'] && $item['one_child_num'] > 0) || ($fenxiao_info['one_child_fenxiao_num'] >= $item['one_child_fenxiao_num'] && $item['one_child_fenxiao_num'] > 0)) {
							$upgrade_level = $item;
							break;
						}
					}
				}
				if ($upgrade_level) {
					model('fenxiao')->update([ 'level_id' => $upgrade_level['level_id'], 'level_name' => $upgrade_level['level_name'] ], [ [ 'fenxiao_id', '=', $fenxiao_id ] ]);
				}
			}
		}
	}

    /**
     * 获取下一个可升级的分销商等级 及当前分销商已达成的条件
     * @param $member_id
     * @param $site_id
     */
    public function geFenxiaoLastLevel($member_id){

        $array = [];
        $join = [
            [ 'member m', 'f.member_id = m.member_id', 'inner' ],
            [ 'fenxiao_level fl', 'f.level_id = fl.level_id', 'inner' ]
        ];
        $fenxiao_info = model('fenxiao')->getInfo([ [ 'f.member_id', '=', $member_id ],[ 'f.status', '=', 1 ] ], 'f.level_id,m.order_num,m.order_money,f.one_fenxiao_order_num,f.one_fenxiao_order_money,f.one_child_num,f.one_child_fenxiao_num,fl.one_rate', 'f', $join);
        $array['fenxiao'] = $fenxiao_info;
        $last_level = [];
        if (!empty($fenxiao_info)) {
            $last_level = model('fenxiao_level')->getFirstData([ [ 'one_rate', '>=', $fenxiao_info['one_rate'] ], [ 'level_id', '<>', $fenxiao_info['level_id'] ] ], '*', 'one_rate asc');

        }
        $array['last_level'] = $last_level;
        return $this->success($array);
    }


}