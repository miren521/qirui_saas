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


namespace app\model\member;

use think\facade\Cache;
use app\model\BaseModel;

/**
 * 会员等级
 */
class MemberLevel extends BaseModel
{
	
	/**
	 * 添加会员等级
	 *
	 * @param array $data
	 */
	public function addMemberLevel($data)
	{
        $is_default = $data['is_default'] ?? 0;

        //判断是否重复
        $count = model('member_level')->getCount([['growth', '=', $data['growth']]]);
        if($count > 0) return $this->error('', '等级成长值重复');
        $count = model('member_level')->getCount([['level_name', '=', $data['level_name']]]);
        if($count > 0) return $this->error('', '等级名称已存在');

		if ($is_default == 1) {
			$this->refreshLevel();
		}
		$res = model('member_level')->add($data);
		$this->refreshSort();
		Cache::tag("member_level")->clear();
		return $this->success($res);
	}
	
	/**
	 * 修改会员等级(不允许批量处理)
	 *
	 * @param array $data
	 * @param array $condition
	 */
	public function editMemberLevel($data, $condition)
	{
        $is_default = $data['is_default'] ?? 0;

        //判断是否重复
//        $count = model('member_level')->getCount([['growth', '=', input('growth', 0)]]);
//        if($count > 0) return $this->error('', '等级成长值重复');
//        $count = model('member_level')->getCount([['level_name', '=', $data['level_name']]]);
//        if($count > 0) return $this->error('', '等级名称重复');

        if ($is_default == 1) {
			$this->refreshLevel();
		}
		$res = model('member_level')->update($data, $condition);

            $check_condition = array_column($condition, 2, 0);
            model('member')->update(["member_level_name" => $data["level_name"]], [["member_level", "=", $check_condition["level_id"]]]);
		$this->refreshSort();
		Cache::tag('member_level')->clear();
		return $this->success($res);
	}
	
	/**
	 * 刷新会员等级排序
	 */
	private function refreshSort()
	{
		$list = model('member_level')->getList([], 'level_id, growth', 'growth asc');
		foreach ($list as $k => $v) {
			model('member_level')->update([ 'sort' => $k ], [ [ 'level_id', '=', $v['level_id'] ] ]);
		}
	}
	
	/**
	 * 刷新会员等级
	 */
	private function refreshLevel()
	{
		model('member_level')->update([ 'is_default' => 0 ], [ [ 'is_default', '=', 1 ] ]);
	}
	
	/**
	 * 删除会员等级
	 * @param array $condition
	 */
	public function deleteMemberLevel($condition)
	{
		$res = model('member_level')->delete($condition);
		$this->refreshSort();
		Cache::tag("member_level")->clear();
		return $this->success($res);
	}
	
	/**
	 * 获取会员等级信息
	 *
	 * @param array $condition
	 * @param string $field
	 */
	public function getMemberLevelInfo($condition = [], $field = '*')
	{
		
		$data = json_encode([ $condition, $field ]);
		$cache = Cache::get("member_level_getMemberLevelInfo_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$info = model('member_level')->getInfo($condition, $field);
		Cache::tag("member_level")->set("member_level_getMemberLevelInfo_" . $data, $info);
		return $this->success($info);
	}
	
	/**
	 * 获取会员等级列表
	 *
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getMemberLevelList($condition = [], $field = '*', $order = 'sort asc, level_id asc', $limit = null)
	{
		
		$data = json_encode([ $condition, $field, $order, $limit ]);
		$cache = Cache::get("member_level_getMemberLevelList_" . $data);
		
		if (!empty($cache)) {
			return $this->success($cache);
		}
		
		$list = model('member_level')->getList($condition, $field, $order, '', '', '', $limit);
		Cache::tag("member_level")->set("member_level_getMemberLevelList_" . $data, $list);
		
		return $this->success($list);
	}
	
	/**
	 * 获取会员等级分页列表
	 *
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getMemberLevelPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'sort asc, level_id asc', $field = '*')
	{
		
		$data = json_encode([ $condition, $field, $order, $page, $page_size ]);
		$cache = Cache::get("member_level_getMemberLevelPageList_" . $data);
		if (!empty($cache)) {
			return $this->success($cache);
		}
		$list = model('member_level')->pageList($condition, $field, $order, $page, $page_size);
		Cache::tag("member_level")->set("member_level_getMemberLevelPageList_" . $data, $list);
		return $this->success($list);
	}
	
	/**
	 * 设置默认等级
	 * @param unknown $level_id
	 */
	public function setDefaultLevel($level_id){
		model('member_level')->update(['is_default' => 1], [ ['level_id', '=', $level_id] ]);
		model('member_level')->update(['is_default' => 0], [ ['level_id', '<>', $level_id] ]);
		Cache::tag("member_level")->clear();
		return $this->success();
	}
	
}