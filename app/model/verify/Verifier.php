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


namespace app\model\verify;

use app\model\BaseModel;

/**
 * 核销员管理
 */
class Verifier extends BaseModel
{
    
    /**
     * 添加核销人员
     * @param unknown $data
     */
	public function addVerifier($data)
	{
	    //检测会员是否在本店铺存在核销员
        if($data["member_id"] > 0){
            $member_condition = array(
                ["member_id", "=", $data["member_id"]],
//                ["site_id", "=", $data["site_id"]]
            );
            $member_count = model("verifier")->getCount($member_condition, "verifier_id");
            if($member_count > 0){
                return $this->error([], "当前会员已存在核销员身份");
            }
        }
	    $res = model("verifier")->add($data);
	    return $this->success($res);
	}

    /**
     * 编辑用户
     * @param $data
     * @param $condition
     */
    public function editVerifier($data, $condition)
    {
        $check_condition = array_column($condition, 2, 0);
        $verifier_id = isset($check_condition['verifier_id']) ? $check_condition['verifier_id'] : '';
        //检测会员是否在本店铺存在核销员
        if($data["member_id"] > 0){
            $member_condition = array(
                ["member_id", "=", $data["member_id"]],
//                ["site_id", "=", $site_id],
                ["verifier_id", "<>", $verifier_id]
            );
            $member_count = model("verifier")->getCount($member_condition, "verifier_id");
            if($member_count > 0){
                return $this->error([], "当前会员在当前店铺已存在核销员身份");
            }
        }
        $res = model("verifier")->update($data, $condition);
        if ($res === false) {
            return $this->error('', 'UNKNOW_ERROR');
        }
        return $this->success($res);
    }
	/**
	 * 删除核销人员
	 * @param unknown $verifier_id
	 * @param unknown $site_id
	 * @return multitype:number unknown
	 */
	public function deleteVerifier($verifier_id, $site_id)
    {
	    $res = model("verifier")->delete([['verifier_id', '=', $verifier_id], ['site_id', '=', $site_id]]);
	    return $this->success($res);
	}
	
	/**
	 * 获取核销人员信息
	 * @param array $condition
	 * @param string $field
	 */
	public function getVerifierInfo($condition, $field = '*')
	{
	    $res = model('verifier')->getInfo($condition, $field);
	    return $this->success($res);
	}


	/**
	 * 获取核销人员列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getVerifierList($condition = [], $field = '*', $order = '', $limit = null)
	{
	    
	    $list = model('verifier')->getList($condition, $field, $order, '', '', '', $limit);
	    return $this->success($list);
	}
	
	/**
	 * 获取核销人员分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getVerifierPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = 'create_time desc', $field = '*')
	{

	    $list = model('verifier')->pageList($condition, $field, $order, $page, $page_size);
	    return $this->success($list);
	}

    /**
     * 检测会员是否是核销员
     * @param $condition
     */
	public function checkIsVerifier($condition){
        $info = model('verifier')->getInfo($condition, "verifier_id");
        if(!empty($info)){
            return $this->success($info);
        }else{
            return $this->error();
        }
    }
    
}