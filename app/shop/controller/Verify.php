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


namespace app\shop\controller;

use app\model\member\Member;
use app\model\verify\Verifier;
use app\model\verify\Verify as VerifyModel;
use app\model\system\User as UserModel;

/**
 * 核销
 * Class Verify
 * @package app\shop\controller
 */
class Verify extends BaseShop
{
	
	
	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
		
	}
	
	/**
	 * 核销记录
	 * @return mixed
	 */
	public function records()
	{
		$verify_model = new VerifyModel();
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$order = input("order", "create_time desc");
			$verify_type = input('verify_type', "");//验证类型
			$verify_code = input('verify_code', "");//验证码
			$verifier_name = input('verifier_name', "");
			$start_time = input("start_time", '');
			$end_time = input("end_time", '');
			
			$condition = [
				[ 'site_id', "=", $this->site_id ],
				[ 'is_verify', '=', 1 ]
			];
			if (!empty($verify_type)) {
				$condition[] = [ "verify_type", "=", $verify_type ];
			}
			if (!empty($verify_code)) {
				$condition[] = [ "verify_code", 'like', '%' . $verify_code . '%' ];
			}
			if (!empty($verifier_name)) {
				$condition[] = [ 'verifier_name', 'like', '%' . $verifier_name . '%' ];
			}
			if (!empty($start_time) && empty($end_time)) {
				$condition[] = [ 'create_time', '>=', date_to_time($start_time) ];
			} elseif (empty($start_time) && !empty($end_time)) {
				$condition[] = [ "create_time", "<=", date_to_time($end_time) ];
			} elseif (!empty($start_time) && !empty($end_time)) {
				$condition[] = [ 'create_time', 'between', [ date_to_time($start_time), date_to_time($end_time) ] ];
			}
			$list = $verify_model->getVerifyPageList($condition, $page, $page_size, $order, $field = 'id, verify_code, verify_type, verify_type_name, verify_content_json, verifier_id, verifier_name, is_verify, create_time, verify_time');
			return $list;
		} else {
			$verify_type = $verify_model->getVerifyType();
			$this->assign('verify_type', $verify_type);
			return $this->fetch("verify/records");
		}
		
	}
	
	/**
	 * 核销台
	 * @return mixed
	 */
	public function verifyCard()
	{
		if (request()->isAjax()) {
			//先验证登录用户是否具备核销权限
			$verifier = new Verifier();
			$verifier_info_result = $verifier->getVerifierInfo([ [ "uid", "=", $this->uid ], [ "site_id", "=", $this->site_id ] ]);
			
			$verifier_info = $verifier_info_result["data"];
			if (empty($verifier_info)) {
				return $verifier->error([], 'VERIFIER_FAIL');
			}
			$verify_code = input("verify_code", "");
			$verify_model = new VerifyModel();
			$res = $verify_model->getVerifyInfo([ [ "verify_code", "=", $verify_code ], [ "site_id", "=", $this->site_id ] ]);
			return $res;
		} else {
			return $this->fetch("verify/verify_card");
		}
		
	}
	
	/**
	 * 核销人员
	 * @return mixed
	 */
	public function user()
	{
		if (request()->isAjax()) {
			$verifier = new Verifier();
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$order = input("order", "create_time desc");
			$verifier_name = input('verifier_name', '');
			$condition = [];
			$condition[] = [ 'site_id', "=", $this->site_id ];
			if ($verifier_name) {
				$condition[] = [ 'verifier_name', '=', $verifier_name ];
			}
			$list = $verifier->getVerifierPageList($condition, $page, $page_size, $order, $field = 'verifier_id, verifier_name, site_id, member_id, uid, create_time, modify_time');
			return $list;
		} else {
			return $this->fetch("verify/user");
		}
	}
	
	/**
	 * 添加核销人员
	 * @return mixed
	 */
	public function addUser()
	{
		if (request()->isAjax()) {
			$verifier_name = input("verifier_name", "");
			$member_id = input("member_id", 0);//会员账号
                $model = new Verifier();
                    if($member_id <= 0){
                        $model->error([], "EMPTY_BIND_MEMBER");
                    }
			$uid = input("uid", 0);//管理员账号

			$data = array();
			$data['site_id'] = $this->site_id;
			$data['create_time'] = time();
			$data["verifier_name"] = $verifier_name;
			$data["member_id"] = $member_id;
			$data["uid"] = $uid;
			$result = $model->addVerifier($data);
			return $result;
		} else {
			return $this->fetch("verify/add_user");
		}
	}
	
	/**
	 * 编辑核销人员
	 * @return mixed
	 */
	public function editUser()
	{
		$verifier_id = input("verifier_id", 0);//核销员id
		if (request()->isAjax()) {
			$verifier_name = input("verifier_name", "");
			$member_id = input("member_id", 0);//会员账号
                    $model = new Verifier();
                    if($member_id <= 0){
                        $model->error([], "EMPTY_BIND_MEMBER");
                    }

			$data = [
				'verifier_name' => $verifier_name,
				'modify_time' => time(),
			];
			$data["member_id"] = $member_id;
			$data["uid"] = 0;
			$condition = array(
				[ 'verifier_id', '=', $verifier_id ],
				[ 'site_id', '=', $this->site_id ],
			);
			
			$model = new Verifier();
			$result = $model->editVerifier($data, $condition);
			return $result;
		} else {
			$verifier_id = input("verifier_id", 0);
			
			//用户信息
			$model = new Verifier();
			$condition = [
				[ "verifier_id", "=", $verifier_id ],
				[ "site_id", "=", $this->site_id ],
			];
			$info_result = $model->getVerifierInfo($condition);
			$info = $info_result["data"];
			$member_account = "";
			if (!empty($info["member_id"])) {
				$member_model = new Member();
				$member_info_result = $member_model->getMemberInfo([ [ "member_id", "=", $info["member_id"] ] ], "username");
				$member_info = $member_info_result["data"];
				if (!empty($member_info)) {
					$member_account = $member_info["username"];
				}
				
			}
			$info["member_account"] = $member_account;
			$this->assign("data", $info);
			$this->assign("verifier_id", $verifier_id);
			return $this->fetch("verify/edit_user");
		}
		
	}
	
	
	/**
	 * 删除核销人员
	 * @return mixed
	 */
	public function deleteUser()
	{
		if (request()->isAjax()) {
			$verifier = new Verifier();
			$verifier_id = input('ids', 0);
			$res = $verifier->deleteVerifier($verifier_id, $this->site_id);
			return $res;
		}
	}
	
	
	/**
	 * 核销
	 */
	public function verify()
	{
		//先验证登录用户是否具备核销权限
          $info = array(
              "verifier_id" => $this->uid,
              "verifier_name" => $this->user_info['username'],
          );
		$verify_code = input("verify_code", "");
		$verify_model = new VerifyModel();
		$res = $verify_model->verify($info, $verify_code);
		return $res;
	}
	
	/**
	 * 搜索会员
	 * 不是菜单 不入权限
	 */
	public function searchMember()
	{
		if (request()->isAjax()) {
			$search_text = input('search_text', '');
			$member_model = new Member();
			$member_info = $member_model->getMemberInfo([ [ 'username|mobile', '=', $search_text ] ]);
			return $member_info;
		}
	}
	

	
}