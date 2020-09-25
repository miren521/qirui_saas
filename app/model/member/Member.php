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

use app\model\BaseModel;
use app\model\message\Email;
use app\model\message\Sms;
use app\model\system\Stat;
use app\model\upload\Upload;

/**
 * 会员管理
 */
class Member extends BaseModel
{
	
	/**
	 * 添加会员(注意等级名称)
	 * @param $data
	 * @return array
	 */
	public function addMember($data)
	{
		if ($data['username']) {
			$count = model('member')->getCount([
				'username' => $data['username'],
			]);
			if ($count > 0) {
				return $this->error('', 'USERNAME_EXISTED');
			}
		}
		
		if ($data['mobile']) {
			$count = model('member')->getCount([
				'mobile' => $data['mobile']
			]);
			if ($count > 0) {
				return $this->error('', 'MOBILE_EXISTED');
			}
		}
		
		if ($data['email']) {
			$count = model('member')->getCount([
				'email' => $data['email']
			]);
			if ($count > 0) {
				return $this->error('', 'EMAIL_EXISTED');
			}
		}
		$res = model('member')->add($data);
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		//添加统计
		$stat = new Stat();
		$stat->addShopStat([ 'member_count' => 1, 'site_id' => 0 ]);
		return $this->success($res);
	}
	
	/**
	 * 修改会员(注意标签与等级名称)
	 * @param $data
	 * @param $condition
	 * @return array
	 */
	public function editMember($data, $condition)
	{
		$res = model('member')->update($data, $condition);
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 修改会员状态
	 * @param $status
	 * @param $condition
	 * @return array
	 */
	public function modifyMemberStatus($status, $condition)
	{
		$res = model('member')->update([
			'status' => $status
		], $condition);
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 修改会员标签
	 * @param $label_ids
	 * @param $condition
	 * @return array
	 */
	public function modifyMemberLabel($label_ids, $condition)
	{
		//查询会员标签
		$label_list = model("member_label")->getList([ [ 'label_id', 'in', $label_ids ] ], 'label_id,label_name');
		
		$label_ids = '';
		$label_names = '';
		if (!empty($label_list)) {
			foreach ($label_list as $k => $v) {
				$label_ids = $label_ids . $v['label_id'] . ',';
				$label_names = $label_names . $v['label_name'] . ',';
			}
		}
		$res = model('member')->update([
			'member_label' => $label_ids,
			'member_label_name' => $label_names
		], $condition);
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		return $this->success($res);
	}

    /**
     * 重置密码
     * @param string $password
     * @param $condition
     * @return array
     */
	public function resetMemberPassword($password = '123456', $condition)
	{
		$res = model('member')->update([
			'password' => data_md5($password)
		], $condition);
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 修改密码
	 * @param $member_id
	 * @param $old_password
	 * @param $new_password
	 * @return array
	 */
	public function modifyMemberPassword($member_id, $old_password, $new_password)
	{
		$res = model('member')->getCount([
			[ 'password', '=', data_md5($old_password) ],
			[ 'member_id', '=', $member_id ]
		]);
		if ($res > 0) {
			$res = model('member')->update([
				'password' => data_md5($new_password)
			], [ [ 'member_id', '=', $member_id ] ]);
			if ($res === false) {
				return $this->error('', 'RESULT_ERROR');
			}
			return $this->success($res);
		} else {
			return $this->error('', 'PASSWORD_ERROR');
		}
	}
	
	/**
	 * 删除会员（应用后台）
	 * @param $condition
	 * @return array
	 */
	public function deleteMember($condition)
	{
		$res = model('member')->delete($condition);
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		return $this->success($res);
	}
	
	/**
	 * 获取会员信息
	 * @param array $condition
	 * @param string $field
	 * @return array
	 */
	public function getMemberInfo($condition = [], $field = '*')
	{
		$member_info = model('member')->getInfo($condition, $field);
		return $this->success($member_info);
	}
	
	/**
	 * 获取会员信息
	 * @param int $member_id
	 * @return array
	 */
	public function getMemberDetail($member_id)
	{
		$field = 'member_id,source_member,username,nickname,mobile,email,status,headimg,member_level,member_level_name,member_label,member_label_name,qq,realname,sex,location,birthday,reg_time,point,balance,growth,balance_money,account5,pay_password';
		$member_info = model('member')->getInfo([ [ 'member_id', '=', $member_id ] ], $field);
		if (!empty($member_info)) {
			$member_info['balance_total'] = $member_info['balance'] + $member_info['balance_money'];
			return $this->success($member_info);
		}
		return $this->error();
	}
	
	/**
	 * 获取会员数量
	 * @param array $condition
	 * @return array
	 */
	public function getMemberCount($condition = [])
	{
		$member_info = model('member')->getCount($condition);
		return $this->success($member_info);
	}
	
	
	/**
	 * 获取会员分页列表
	 * @param array $condition
	 * @param int $page
	 * @param int $page_size
	 * @param string $order
	 * @param string $field
	 * @return array
	 */
	public function getMemberPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{
		$list = model('member')->pageList($condition, $field, $order, $page, $page_size, '', '', '');
		return $this->success($list);
	}
	
	/**
	 * 获取会员列表
	 * @param array $where
	 * @param bool $field
	 * @param string $order
	 * @param string $alias
	 * @param array $join
	 * @param string $group
	 * @param null $limit
	 * @return array
	 */
	public function getMemberList($where = [], $field = true, $order = '', $alias = 'a', $join = [], $group = '', $limit = null)
	{
		$res = model('member')->getList($where, $field, $order, $alias, $join, $group, $limit);
		return $this->success($res);
	}
	
	
	/**
	 * 绑定发送验证码
	 * @param $data
	 * @return array|mixed|void
	 */
	public function bindCode($data)
	{
		//发送短信
		$sms_model = new Sms();
		$var_parse = array(
			"code" => $data["code"],//验证码
			"site_name" => $data["site_name"],//站点名称
		);
		$data["sms_account"] = $data["mobile"] ?? '';//手机号
		$data["var_parse"] = $var_parse;
		$sms_result = $sms_model->sendMessage($data);
		if ($sms_result["code"] < 0)
			return $sms_result;
		
		
		//发送邮箱
		$email_model = new Email();
		//有邮箱才发送
		$data["email_account"] = $data["email"] ?? '';//邮箱号
		$email_result = $email_model->sendMessage($data);
		if ($email_result["code"] < 0)
			return $email_result;
		
		return $this->success();
	}
	
	/**
	 * 找回密码发送验证码
	 * @param $data
	 * @return array|mixed|void
	 */
	public function findCode($data)
	{
		//发送短信
		$sms_model = new Sms();
		$var_parse = array(
			"code" => $data["code"],//验证码
			"site_name" => $data["site_name"],//站点名称
		);
		$data["sms_account"] = $data["mobile"] ?? '';//手机号
		$data["var_parse"] = $var_parse;
		$sms_result = $sms_model->sendMessage($data);
		if ($sms_result["code"] < 0)
			return $sms_result;
		
		
		//发送邮箱
		$email_model = new Email();
		//有邮箱才发送
		$data["email_account"] = $data["email"] ?? '';//邮箱号
		$email_result = $email_model->sendMessage($data);
		if ($email_result["code"] < 0)
			return $email_result;
		
		return $this->success();
	}

    /**
     * 设置会员交易密码
     * @param $member_id
     * @param $password
     * @return array
     */
	public function modifyMemberPayPassword($member_id, $password)
	{
		$res = model('member')->update([
			'pay_password' => data_md5($password)
		], [ [ 'member_id', '=', $member_id ] ]);
		if ($res === false) {
			return $this->error('', 'RESULT_ERROR');
		}
		return $this->success($res);
	}

    /**
     * 会员是否已设置支付密码
     * @param $member_id
     * @return array
     */
	public function memberIsSetPayPassword($member_id)
	{
		$info = model('member')->getInfo([ [ 'member_id', '=', $member_id ] ], 'pay_password');
		if (empty($info['pay_password'])) return $this->success(0);
		else return $this->success(1);
	}

    /**
     * 检测会员支付密码是否正确
     * @param $member_id
     * @param $pay_password
     * @return array
     */
	public function checkPayPassword($member_id, $pay_password)
	{
		$res = model('member')->getCount([
			[ 'pay_password', '=', data_md5($pay_password) ],
			[ 'member_id', '=', $member_id ]
		]);
		if ($res > 0) {
			return $this->success($res);
		} else {
			return $this->error('', 'PAY_PASSWORD_ERROR');
		}
	}
	
	
	/**
	 * 找回密码发送验证码
	 * @param $data
	 * @return array|mixed|void
	 */
	public function paypasswordCode($data)
	{
		//发送短信
		$sms_model = new Sms();
		$var_parse = array(
			"code" => $data["code"],//验证码
			"site_name" => $data["site_name"],//站点名称
		);
		$member_info_result = $this->getMemberInfo([ [ "member_id", "=", $data["member_id"] ] ], "mobile");
		$member_info = $member_info_result["data"];
		$data["sms_account"] = $member_info["mobile"] ?? '';//通过member_id获得手机号
		$data["var_parse"] = $var_parse;
		$sms_result = $sms_model->sendMessage($data);
		if ($sms_result["code"] < 0)
			return $sms_result;
		
		return $this->success();
	}

    /**
     * 拉取用户头像到本地
     * @param $member_id
     */
	public function pullHeadimg($member_id){
	    $member_info = model("member")->getInfo([ [ 'member_id', '=', $member_id ] ], 'headimg');
	    if (!empty($member_info['headimg']) && is_url($member_info['headimg'])) {
	        $upload = new Upload();
	        $res = $upload->setPath('upload/user/haedimg/')->remotePull($member_info['headimg']);
	        if ($res['code'] >= 0) {
	            model("member")->update(['headimg' => $res['data']['pic_path']], [ [ 'member_id', '=', $member_id ] ]);
	        }
	    }
	}
}