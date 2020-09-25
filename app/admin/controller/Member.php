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


namespace app\admin\controller;

use app\model\member\Member as MemberModel;
use app\model\member\MemberAddress as MemberAddressModel;
use app\model\member\MemberLabel as MemberLabelModel;
use app\model\member\MemberLevel as MemberLevelModel;
use app\model\member\MemberAccount as MemberAccountModel;
use app\model\member\Config as ConfigModel;
use think\facade\Db;
use app\model\order\Order as OrderModel;
use app\model\order\OrderCommon as OrderCommonModel;
use phpoffice\phpexcel\Classes\PHPExcel;
use phpoffice\phpexcel\Classes\PHPExcel\Writer\Excel2007;

/**
 * 会员管理 控制器
 */
class Member extends BaseAdmin
{
	/**
	 * 会员列表
	 */
	public function memberList()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			$search_text_type = input('search_text_type', 'username');//可以传username mobile email
			$level_id = input('level_id', 0);
			$label_id = input('label_id', 0);
			$reg_start_date = input('reg_start_date', '');
			$reg_end_date = input('reg_end_date', '');
			$status = input('status', '');
			
			$condition = [];
			//下拉选择
			$condition[] = [ $search_text_type, 'like', "%" . $search_text . "%" ];
			//会员等级
			if ($level_id != 0) {
				$condition[] = [ 'member_level', '=', $level_id ];
			}
			//会员标签
			if ($label_id != 0) {
				//raw方法变为public类型 需要实例化以后调用
				$condition[] = [ "", 'exp', Db::raw("FIND_IN_SET({$label_id}, member_label)") ];
			}
			//注册时间
			if ($reg_start_date != '' && $reg_end_date != '') {
				$condition[] = [ 'reg_time', 'between', [ strtotime($reg_start_date), strtotime($reg_end_date) ] ];
			} else if ($reg_start_date != '' && $reg_end_date == '') {
				$condition[] = [ 'reg_time', '>=', strtotime($reg_start_date) ];
			} else if ($reg_start_date == '' && $reg_end_date != '') {
				$condition[] = [ 'reg_time', '<=', strtotime($reg_end_date) ];
			}
			//会员状态
			if ($status != '') {
				$condition[] = [ 'status', '=', $status ];
			}
			
			$order = 'reg_time desc';
			$field = 'member_id, username, mobile, email, status, headimg, member_level, member_level_name, member_label, member_label_name, qq, qq_openid, wx_openid, wx_unionid, ali_openid, baidu_openid, toutiao_openid, douyin_openid, login_ip, login_type, login_time, last_login_ip, last_login_type, last_login_time, login_num, nickname, realname, sex, location, birthday, reg_time, point, balance, balance_money, growth, account5';
			
			$member_model = new MemberModel();
			$list = $member_model->getMemberPageList($condition, $page, $page_size, $order, $field);
			return $list;
		} else {
			//会员等级
			$member_level_model = new MemberLevelModel();
			$member_level_list = $member_level_model->getMemberLevelList([], 'level_id, level_name', 'growth asc');
			$this->assign('member_level_list', $member_level_list['data']);
			
			//会员标签
			$member_label_model = new MemberLabelModel();
			$member_label_list = $member_label_model->getMemberLabelList([], 'label_id, label_name', 'sort asc');
			$this->assign('member_label_list', $member_label_list['data']);

			/*奖励规则*/
			//积分
            $point = event('MemberAccountRule',['account' => 'point']);
            $this->assign('point',$point);
            //余额
            $balance = event('MemberAccountRule',['account' => 'balance']);
            $this->assign('balance',$balance);
            //成长值
            $growth = event('MemberAccountRule',['account' => 'growth']);
            $this->assign('growth',$growth);

			return $this->fetch('member/member_list');
		}
	}
	
	/**
	 * 会员添加
	 */
	public function addMember()
	{
		if (request()->isAjax()) {
			$data = [
				'username' => input('username', ''),
				'mobile' => input('mobile', ''),
				'email' => input('email', ''),
				'password' => data_md5(input('password', '')),
				'status' => input('status', 1),
				'headimg' => input('headimg', ''),
				'member_level' => input('member_level', ''),
				'member_level_name' => input('member_level_name', ''),
				'nickname' => input('nickname', ''),
				'sex' => input('sex', 0),
				'birthday' => input('birthday', '') ? strtotime(input('birthday', '')) : 0,
				'realname' => input('realname', ''),
				'reg_time' => time(),
			];
			
			$member_model = new MemberModel();
			$this->addLog("添加会员" . $data['username'] . $data['mobile']);
			return $member_model->addMember($data);
		} else {
			//会员等级
			$member_level_model = new MemberLevelModel();
			$member_level_list = $member_level_model->getMemberLevelList([], 'level_id, level_name', 'growth asc');
			$this->assign('member_level_list', $member_level_list['data']);
			
			return $this->fetch('member/add_member');
		}
	}
	
	/**
	 * 会员编辑
	 */
	public function editMember()
	{
		if (request()->isAjax()) {
			$data = [
				'mobile' => input('mobile', ''),
				'email' => input('email', ''),
				'status' => input('status', 1),
				'headimg' => input('headimg', ''),
				'member_level' => input('member_level', ''),
				'member_level_name' => input('member_level_name', ''),
				'nickname' => input('nickname', ''),
				'sex' => input('sex', 0),
				'birthday' => input('birthday', '') ? strtotime(input('birthday', '')) : 0,
			];
			
			$member_id = input('member_id', 0);
			$member_model = new MemberModel();
			$this->addLog("编辑会员:id" . $member_id, $data);
			return $member_model->editMember($data, [ [ 'member_id', '=', $member_id ] ]);
		} else {
			
			//会员等级
			$member_level_model = new MemberLevelModel();
			$member_level_list = $member_level_model->getMemberLevelList([], 'level_id, level_name', 'growth asc');
			$this->assign('member_level_list', $member_level_list['data']);
			
			//会员信息
			$member_id = input('member_id', 0);
			$member_model = new MemberModel();
			$member_info = $member_model->getMemberInfo([ [ 'member_id', '=', $member_id ] ]);
			$this->assign('member_info', $member_info);
			
			//会员详情四级菜单
			$this->forthMenu([ 'member_id' => $member_id ]);
			
			return $this->fetch('member/edit_member');
		}
	}
	
	/**
	 * 会员删除
	 */
	public function deleteMember()
	{
		$member_ids = input('member_ids', '');
		$member_model = new MemberModel();
		$this->addLog("删除会员:id" . $member_ids);
		return $member_model->deleteMember([ [ 'member_id', 'in', $member_ids ] ]);
	}
	
	/**
	 * 修改会员标签
	 */
	public function modifyLabel()
	{
		$member_ids = input('member_ids', '');
		$label_ids = input('label_ids', '');
		$member_model = new MemberModel();
		return $member_model->modifyMemberLabel($label_ids, [ [ 'member_id', 'in', $member_ids ] ]);
	}
	
	/**
	 * 修改会员状态
	 */
	public function modifyStatus()
	{
		$member_ids = input('member_ids', '');
		$status = input('status', 0);
		$member_model = new MemberModel();
		return $member_model->modifyMemberStatus($status, [ [ 'member_id', 'in', $member_ids ] ]);
	}
	
	/**
	 * 修改会员密码
	 */
	public function modifyPassword()
	{
		$member_ids = input('member_ids', '');
		$password = input('password', '123456');
		$member_model = new MemberModel();
		return $member_model->resetMemberPassword($password, [ [ 'member_id', 'in', $member_ids ] ]);
	}
	
	/**
	 * 账户详情
	 */
	public function accountDetail()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$account_type = input('account_type', '');
			$from_type = input('from_type', '');
			$start_date = input('start_date', '');
			$end_date = input('end_date', '');
			$member_id = input('member_id', 0);
			
			$condition = [];
			$condition[] = [ 'member_id', '=', $member_id ];
			//账户类型
			if ($account_type != '') {
				$condition[] = [ 'account_type', '=', $account_type ];
			}
			//来源类型
			if ($from_type != '') {
				$condition[] = [ 'from_type', '=', $from_type ];
			}
			//发生时间
			if ($start_date != '' && $end_date != '') {
				$condition[] = [ 'create_time', 'between', [ strtotime($start_date), strtotime($end_date) ] ];
			} else if ($start_date != '' && $end_date == '') {
				$condition[] = [ 'create_time', '>=', strtotime($start_date) ];
			} else if ($start_date == '' && $end_date != '') {
				$condition[] = [ 'create_time', '<=', strtotime($end_date) ];
			}
			
			$member_account_model = new MemberAccountModel();
			$res = $member_account_model->getMemberAccountPageList($condition, $page, $page_size);
			$account_type_arr = $member_account_model->getAccountType();
			foreach ($res['data']['list'] as $key => $val) {
				$res['data']['list'][ $key ]['account_type_name'] = $account_type_arr[ $val['account_type'] ];
			}
			return $res;
			
		} else {
			$member_id = input('member_id', 0);
			
			//会员信息
			$member_model = new MemberModel();
			$member_info = $member_model->getMemberDetail($member_id);
			$this->assign('member_info', $member_info['data']);
			
			//账户类型和来源类型
			$member_account_model = new MemberAccountModel();
			$account_type_arr = $member_account_model->getAccountType();
//			$from_type_arr = $member_account_model->getFromType();
			$this->assign('account_type_arr', $account_type_arr);
//			$this->assign('from_type_arr', $from_type_arr['point']);
			
			//会员详情四级菜单
			$this->forthMenu([ 'member_id' => $member_id ]);
			
			return $this->fetch('member/account_detail');
		}
	}
	
	/**
	 * 余额调整（不可提现）
	 */
	public function adjustBalance()
	{
		$member_id = input('member_id', 0);
		$adjust_num = input('adjust_num', 0);
		$remark = input('remark', '');
		$this->addLog("会员余额调整id:" . $member_id . "金额" . $adjust_num);
		$member_account_model = new MemberAccountModel();
		return $member_account_model->addMemberAccount($member_id, 'balance', $adjust_num, 'adjust', 0, $remark);
	}
	
	/**
	 * 余额调整（可提现）
	 */
	public function adjustBalanceMoney()
	{
		$member_id = input('member_id', 0);
		$adjust_num = input('adjust_num', 0);
		$remark = input('remark', '');
		$this->addLog("会员余额调整id:" . $member_id . "金额" . $adjust_num);
		$member_account_model = new MemberAccountModel();
		return $member_account_model->addMemberAccount($member_id, 'balance_money', $adjust_num, 'adjust', 0, $remark);
	}
	
	/**
	 * 积分调整
	 */
	public function adjustPoint()
	{
		$member_id = input('member_id', 0);
		$adjust_num = input('adjust_num', 0);
		$remark = input('remark', '');
		$this->addLog("会员积分调整id:" . $member_id . "数量" . $adjust_num);
		$member_account_model = new MemberAccountModel();
		return $member_account_model->addMemberAccount($member_id, 'point', $adjust_num, 'adjust', 0, $remark);
	}
	
	/**
	 * 成长值调整
	 */
	public function adjustGrowth()
	{
		$member_id = input('member_id', 0);
		$adjust_num = input('adjust_num', 0);
		$remark = input('remark', '');
		$this->addLog("会员成长值调整id:" . $member_id . "数量" . $adjust_num);
		$member_account_model = new MemberAccountModel();
		return $member_account_model->addMemberAccount($member_id, 'growth', $adjust_num, 'adjust', 0, $remark);
	}
	
	/**
	 * 注册协议
	 */
	public function regAgreement()
	{
		if (request()->isAjax()) {
			//设置注册协议
			$title = input('title', '');
			$content = input('content', '');
			$config_model = new ConfigModel();
			return $config_model->setRegisterDocument($title, $content);
		} else {
			//获取注册协议
			$config_model = new ConfigModel();
			$document_info = $config_model->getRegisterDocument();
			$this->assign('document_info', $document_info);
			
			return $this->fetch('member/reg_agreement');
		}
	}
	
	/**
	 * 注册设置
	 */
	public function regConfig()
	{
		$config_model = new ConfigModel();
		if (request()->isAjax()) {
			//设置注册设置
			$data = array(
				'is_enable' => input('is_enable', 1),
				'type' => input('type', ''),
				'keyword' => input('keyword', ''),
				'pwd_len' => input('pwd_len', 6),
				'pwd_complexity' => input('pwd_complexity', 'number,letter,upper_case,symbol'),
				'dynamic_code_login' => input('dynamic_code_login', 1)
			);
			return $config_model->setRegisterConfig($data);
		} else {
			//获取注册设置
			$config_info = $config_model->getRegisterConfig();
			$value = $config_info['data']['value'];
			if (!empty($value)) {
				$value['type_arr'] = explode(',', $value['type']);
				$value['pwd_complexity_arr'] = explode(',', $value['pwd_complexity']);
			}
			$this->assign('value', $value);
			return $this->fetch('member/reg_config');
		}
	}
	
	/**
	 * 搜索会员
	 * 不是菜单 不入权限
	 */
	public function searchMember()
	{
		$search_text = input('search_text', '');
		$member_model = new MemberModel();
		$member_info = $member_model->getMemberInfo([ [ 'username|mobile', '=', $search_text ] ]);
		return $member_info;
	}
	
	/**
	 * 导出会员信息
	 */
	public function exportMember()
	{
		//获取会员信息
		$search_text = input('search_text', '');
		$search_text_type = input('search_text_type', 'username');//可以传username mobile email
		$level_id = input('level_id', 0);
		$label_id = input('label_id', 0);
		$reg_start_date = input('reg_start_date', '');
		$reg_end_date = input('reg_end_date', '');
		$status = input('status', '');
		
		$condition = [];
		//下拉选择
		$condition[] = [ $search_text_type, 'like', "%" . $search_text . "%" ];
		//会员等级
		if ($level_id != 0) {
			$condition[] = [ 'member_level', '=', $level_id ];
		}
		//会员标签
		if ($label_id != 0) {
			//raw方法变为public类型 需要实例化以后调用
			$condition[] = [ "", 'exp', Db::raw("FIND_IN_SET({$label_id}, member_label)") ];
		}
		//注册时间
		if ($reg_start_date != '' && $reg_end_date != '') {
			$condition[] = [ 'reg_time', 'between', [ strtotime($reg_start_date), strtotime($reg_end_date) ] ];
		} else if ($reg_start_date != '' && $reg_end_date == '') {
			$condition[] = [ 'reg_time', '>=', strtotime($reg_start_date) ];
		} else if ($reg_start_date == '' && $reg_end_date != '') {
			$condition[] = [ 'reg_time', '<=', strtotime($reg_end_date) ];
		}
		//会员状态
		if ($status != '') {
			$condition[] = [ 'status', '=', $status ];
		}
		
		$order = 'reg_time desc';
		$field = 'username,nickname,realname,mobile,sex,birthday,email,member_level_name,member_label_name,
        qq,location,balance,balance_money,point,growth,reg_time,last_login_ip,last_login_time';
		
		$member_model = new MemberModel();
		$list = $member_model->getMemberList($condition, $field, $order);
		
		// 实例化excel
		$phpExcel = new \PHPExcel();
		
		$phpExcel->getProperties()->setTitle("会员信息");
		$phpExcel->getProperties()->setSubject("会员信息");
		// 对单元格设置居中效果
		$phpExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('K')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('L')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('M')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('N')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('P')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$phpExcel->getActiveSheet()->getStyle('Q')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//单独添加列名称
		$phpExcel->setActiveSheetIndex(0);
		$phpExcel->getActiveSheet()->setCellValue('A1', '会员账号');//可以指定位置
		$phpExcel->getActiveSheet()->setCellValue('B1', '会员昵称');
		$phpExcel->getActiveSheet()->setCellValue('C1', '真实姓名');
		$phpExcel->getActiveSheet()->setCellValue('D1', '手机号');
		$phpExcel->getActiveSheet()->setCellValue('E1', '性别');
		$phpExcel->getActiveSheet()->setCellValue('F1', '生日');
		$phpExcel->getActiveSheet()->setCellValue('G1', '邮箱');
		$phpExcel->getActiveSheet()->setCellValue('H1', '会员等级');
		$phpExcel->getActiveSheet()->setCellValue('I1', '会员标签');
		$phpExcel->getActiveSheet()->setCellValue('J1', 'qq');
		$phpExcel->getActiveSheet()->setCellValue('K1', '地址');
		$phpExcel->getActiveSheet()->setCellValue('L1', '余额');
		$phpExcel->getActiveSheet()->setCellValue('M1', '积分');
		$phpExcel->getActiveSheet()->setCellValue('N1', '成长值');
		$phpExcel->getActiveSheet()->setCellValue('O1', '上次登录时间');
		$phpExcel->getActiveSheet()->setCellValue('P1', '上次登录ip');
		$phpExcel->getActiveSheet()->setCellValue('Q1', '注册时间');
		//循环添加数据（根据自己的逻辑）
		$sex = [ '保密', '男', '女' ];
		foreach ($list['data'] as $k => $v) {
			$i = $k + 2;
			$phpExcel->getActiveSheet()->setCellValue('A' . $i, $v['username']);
			$phpExcel->getActiveSheet()->setCellValue('B' . $i, $v['nickname']);
			$phpExcel->getActiveSheet()->setCellValue('C' . $i, $v['realname']);
			$phpExcel->getActiveSheet()->setCellValue('D' . $i, $v['mobile']);
			$phpExcel->getActiveSheet()->setCellValue('E' . $i, $sex[ $v['sex'] ]);
			$phpExcel->getActiveSheet()->setCellValue('F' . $i, date('Y-m-d', $v['birthday']));
			$phpExcel->getActiveSheet()->setCellValue('G' . $i, $v['email']);
			$phpExcel->getActiveSheet()->setCellValue('H' . $i, $v['member_level_name']);
			$phpExcel->getActiveSheet()->setCellValue('I' . $i, $v['member_label_name']);
			$phpExcel->getActiveSheet()->setCellValue('J' . $i, $v['qq']);
			$phpExcel->getActiveSheet()->setCellValue('K' . $i, $v['location']);
			$phpExcel->getActiveSheet()->setCellValue('L' . $i, $v['balance'] + $v['balance_money']);
			$phpExcel->getActiveSheet()->setCellValue('M' . $i, $v['point']);
			$phpExcel->getActiveSheet()->setCellValue('N' . $i, $v['growth']);
			$phpExcel->getActiveSheet()->setCellValue('O' . $i, date('Y-m-d H:i:s', $v['last_login_time']));
			$phpExcel->getActiveSheet()->setCellValue('P' . $i, $v['last_login_ip']);
			$phpExcel->getActiveSheet()->setCellValue('Q' . $i, date('Y-m-d H:i:s', $v['reg_time']));
		}

//        // 重命名工作sheet
//        $phpExcel->getActiveSheet()->setTitle('会员信息');
//        // 对文件进行保存
//        $filename = date('Y年m月d日-会员信息表',time()).'.xlsx';
//        header('Content-Type: application/vnd.ms-excel');
//        header("Content-Disposition: attachment;filename=\"$filename\"");
//        header('Cache-Control: max-age=0');
//        // 通过工厂类实例化excel5,本来我想使用 excel2007,但是本地测试没有问题,到线上就出错,报错链接找不到
//        $objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
//        $objWriter->save('php://output'); //文件通过浏览器下载
		
		// 重命名工作sheet
		$phpExcel->getActiveSheet()->setTitle('会员信息');
		// 设置第一个sheet为工作的sheet
		$phpExcel->setActiveSheetIndex(0);
		// 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
		$objWriter = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
		$file = date('Y年m月d日-会员信息表', time()) . '.xlsx';
		$objWriter->save($file);
		
		header("Content-type:application/octet-stream");
		
		$filename = basename($file);
		header("Content-Disposition:attachment;filename = " . $filename);
		header("Accept-ranges:bytes");
		header("Accept-length:" . filesize($file));
		readfile($file);
		unlink($file);
		exit;
	}
	
	/**
	 * 订单管理
	 */
	public function order()
	{
		$member_id = input("member_id", 0);//会员id
		$this->assign('member_id', $member_id);
		//会员详情四级菜单
		$this->forthMenu([ 'member_id' => $member_id ]);
		return $this->fetch('member/order');
		
	}
	
	/**
	 * 会员地址
	 */
	public function addressDetail()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$member_id = input('member_id', 0);
			
			$condition = [];
			$condition[] = [ 'member_id', '=', $member_id ];
			
			//会员地址
			$member_address_model = new MemberAddressModel();
			$res = $member_address_model->getMemberAddressPageList($condition, $page, $page_size);
			return $res;
			
		} else {
			$member_id = input('member_id', 0);
			$this->assign('member_id', $member_id);
			
			//会员详情四级菜单
			$this->forthMenu([ 'member_id' => $member_id ]);
			
			return $this->fetch('member/address_detail');
		}
	}

    /**
     * 根据账户类型获取来源类型
     * @return array
     */
    public function getFromType()
    {
        $type = input('type','');
        $model = new MemberAccountModel();
        $res = $model->getFromType();

        return $res[$type];
    }
}