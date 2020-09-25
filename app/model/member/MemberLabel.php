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
 * 会员标签
 */
class MemberLabel extends BaseModel
{

    /**
     * 添加会员标签
     *
     * @param array $data
     */
    public function addMemberLabel($data)
    {
    
        $res = model('member_label')->add($data);
        Cache::tag("member_label")->clear();
        return $this->success($res);
    }
    
    /**
     * 修改会员标签
     *
     * @param array $data
     * @param array $condition
     */
    public function editMemberLabel($data, $condition)
    {
    
        $res = model('member_label')->update($data, $condition);
        Cache::tag("member_label")->clear();
        return $this->success($res);
    }
    
    /**
     * 删除会员标签
     * @param array $condition
     */
    public function deleteMemberLabel($condition)
    {
        $res = model('member_label')->delete($condition);
        Cache::tag("member_label")->clear();
        return $this->success($res);
    }

    /**
     * 修改标签排序
     * @param int $sort
     * @param int $label_id
     */
    public function modifyMemberLabelSort($sort, $label_id)
    {
    
        $res = model('member_label')->update(['sort' => $sort], [['label_id', '=', $label_id]]);
        Cache::tag("member_label")->clear();
        return $this->success($res);
    }
    
    /**
     * 获取会员标签信息
     *
     * @param array $condition
     * @param string $field
     */
    public function getMemberLabelInfo($condition = [], $field = '*')
    {
    
        $data = json_encode([ $condition, $field]);
        $cache = Cache::get("member_label_getMemberLabelInfo_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $info = model('member_label')->getInfo($condition, $field);
        Cache::tag("member_label")->set("member_label_getMemberLabelInfo_" . $data, $info);
        return $this->success($info);
    }
    
    /**
     * 获取会员标签列表
     *
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getMemberLabelList($condition = [], $field = '*', $order = 'sort asc, label_id asc', $limit = null)
    {
    
        $data = json_encode([ $condition, $field, $order, $limit ]);
        $cache = Cache::get("member_label_getMemberLabelList_" . $data);
    
        if (!empty($cache)) {
            return $this->success($cache);
        }
    
        $list = model('member_label')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("member_label")->set("member_label_getMemberLabelList_" . $data, $list);
    
        return $this->success($list);
    }
    
    /**
     * 获取会员标签分页列表
     *
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getMemberLabelPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'sort asc, level_id asc', $field = '*')
    {
    
        $data = json_encode([ $condition, $field, $order, $page, $page_size ]);
        $cache = Cache::get("member_label_getMemberLabelPageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('member_label')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("member_label")->set("member_label_getMemberLabelPageList_" . $data, $list);
        return $this->success($list);
    }
}