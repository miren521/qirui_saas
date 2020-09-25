<?php
/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: https://www.niushop.com
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */

namespace app\api\controller;

use app\model\web\Notice as NoticeModel;

/**
 *
 * @author Administrator
 *
 */
class Notice extends BaseApi
{

	/**
	 * 基础信息
	 */
	public function info()
	{
		$id = isset($this->params[ 'id' ]) ? $this->params[ 'id' ] : 0;
		if (empty($id)) {
			return $this->response($this->error('', 'REQUEST_ID'));
		}
		$notice = new NoticeModel();
		$info = $notice->getNoticeInfo([ [ 'id', '=', $id ] ]);
		return $this->response($info);
	}

	public function page()
	{
		$page = isset($this->params[ 'page' ]) ? $this->params[ 'page' ] : 1;
		$page_size = isset($this->params[ 'page_size' ]) ? $this->params[ 'page_size' ] : PAGE_LIST_ROWS;
		$receiving_type = isset($this->params[ 'receiving_type' ]) ? $this->params[ 'receiving_type' ] : 'mobile';
		$notice = new NoticeModel();
		$list = $notice->getNoticePageList([ [ 'receiving_type', 'like', '%' . $receiving_type . '%' ] ], $page, $page_size);
		return $this->response($list);
	}

}