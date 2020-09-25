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

use app\model\goods\Inform as InformModel;
use app\model\goods\InformSubject as InformSubjectModel;
use app\model\goods\InformSubjectType as InformSubjectTypeModel;

/**
 * 商品管理 控制器
 */
class Inform extends BaseAdmin
{
	/**
	 * 举报列表
	 * @return mixed
	 */
	public function lists()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$search_text = input('search_text', '');
			$site_id = input("site_id", "");
			$state = input("state", "");
			$subject_id = input("subject_id", "");
			$condition = [];
			if ($search_text) {
				$condition[] = [ 'sku_name', 'like', '%' . $search_text . '%' ];
			}
			if (!empty($site_id)) {
				$condition[] = [ 'site_id', '=', $site_id ];
			}
			if (!empty($state)) {
				$condition[] = [ 'state', '=', $state ];
			}
			if (!empty($subject_id)) {
				$condition[] = [ 'subject_id', '=', $subject_id ];
			}
			$order = 'create_time desc';
			$field = '*';
			$inform_model = new InformModel();
			return $inform_model->getInformPageList($condition, $page, $page_size, $order, $field);
		} else {
			$this->forthMenu();
			return $this->fetch("inform/lists");
		}
	}
	
	/**
	 *举报类型
	 */
	public function subjecttype()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$order = '';
			$condition = [];
			$field = '*';
			$subjecttype_model = new InformSubjectTypeModel();
			return $subjecttype_model->getSubjectTypePageList($condition, $page, $page_size, $order, $field);
		} else {
			$this->forthMenu();
			return $this->fetch("inform/subjecttype");
		}
	}
	
	/**
	 *举报主题
	 */
	public function subject()
	{
		if (request()->isAjax()) {
			$page = input('page', 1);
			$page_size = input('page_size', PAGE_LIST_ROWS);
			$order = '';
			$condition = [];
			$field = '*';
			$subjecttype_model = new InformSubjectModel();
			return $subjecttype_model->getSubjectPageList($condition, $page, $page_size, $order, $field);
		} else {
			$this->forthMenu();
			$subjecttype_model = new InformSubjectTypeModel();
			$subjecttype = $subjecttype_model->getSubjectTypeList();
			$this->assign("list", $subjecttype['data']);
			return $this->fetch("inform/subject");
		}
	}
	
	/**举报类型添加
	 * @return mixed
	 */
	public function subjecttypeadd()
	{
		if (request()->isAjax()) {
			$data = [
				'type_name' => input('type_name', ''),
				'type_desc' => input('type_desc', ''),
			];
			$type_model = new InformSubjectTypeModel();
			return $type_model->addSubjectType($data);
		} else {
			return $this->fetch("inform/subjecttypeadd");
		}
	}
	
	/**举报主题添加
	 * @return mixed
	 */
	public function subjectadd()
	{
		if (request()->isAjax()) {
			$data = [
				'subject_content' => input('subject_content', ''),
				'subject_type_id' => input('subject_type_id', ''),
				'subject_type_name' => input('type_name', ''),
			];
			$type_model = new InformSubjectModel();
			return $type_model->addSubject($data);
		} else {
			$subjecttype_model = new InformSubjectTypeModel();
			$subjecttype = $subjecttype_model->getSubjectTypeList();
			$this->assign("typeid", $subjecttype['data']);
			return $this->fetch("inform/subjectadd");
		}
	}
	
	/**删除举报主题
	 * @return mixed
	 */
	public function deletesubject()
	{
		if (request()->isAjax()) {
			$subject_id = input('subject_id', 0);
			$gift_model = new InformSubjectModel();
			return $gift_model->deleteSubject([ [ 'subject_id', '=', $subject_id ] ]);
		}
	}
	
	/**删除举报类型
	 * @return mixed
	 */
	public function deletesubjecttype()
	{
		if (request()->isAjax()) {
			$type_id = input('type_id', 0);
			$gift_model = new InformSubjectTypeModel();
			return $gift_model->deleteSubjectType([ [ 'type_id', '=', $type_id ] ]);
		}
	}
	
	/**举报详情
	 * @return mixed
	 */
	public function detail()
	{
		if (request()->isAjax()) {
			$inform_id = input('inform_id', 0);
			$order_model = new InformModel();
			$order_info = $order_model->getInformInfo([ [ 'inform_id', '=', $inform_id ] ]);
			return $order_info;
		}
		
	}
	
	/**举报类型详情
	 * @return mixed
	 */
	public function subjecttypeinfo()
	{
		if (request()->isAjax()) {
			$type_id = input('type_id', 0);
			$type_model = new InformSubjectTypeModel();
			$type_info = $type_model->getSubjectTypeInfo([ [ 'type_id', '=', $type_id ] ]);
			
			return $type_info;
		}
		
	}
	
	/**举报类型编辑
	 * @return mixed
	 */
	public function editsubjecttype()
	{
		if (request()->isAjax()) {
			$type_id = input('type_id', 0);
			$data = [
				'type_name' => input('type_name', ''),
				'type_desc' => input('type_desc', ''),
				'type_id' => $type_id,
			];
			$subject_type_model = new InformSubjectTypeModel();
			return $subject_type_model->editSubjectType($data);
		}
	}
	
	/**举报主题详情
	 * @return mixed
	 */
	public function subjectinfo()
	{
		if (request()->isAjax()) {
			$subject_id = input('subject_id', '');
			$subject_model = new InformSubjectModel();
			$subject_info = $subject_model->getSubjectInfo([ [ 'subject_id', '=', $subject_id ] ]);
			
			$subjecttype_model = new InformSubjectTypeModel();
			$subjecttype = $subjecttype_model->getSubjectTypeList();
			
			$data = [
				'typeid' => $subjecttype['data'],
				'subject_type_id' => $subject_info['data']['subject_type_id'],
				'subject_info' => $subject_info['data']['subject_content']
			];
			return $data;
		}
	}
	
	/**举报主题编辑
	 * @return mixed
	 */
	public function editsubject()
	{
		if (request()->isAjax()) {
			$subject_id = input('subject_id', 0);
			$data = [
				'subject_content' => input('subject_content', ''),
				'subject_type_name' => input('subject_type_name', ''),
				'subject_state' => input('subject_state', ''),
				'subject_id' => $subject_id,
			];
			$subject_type_model = new InformSubjectModel();
			return $subject_type_model->editSubject($data);
		}
	}
	
	/**
	 * 举报处理
	 */
	public function editinform()
	{
		if (request()->isAjax()) {
			$inform_id = input('inform_id', 0);
			$data = [
				'state' => input('state', '1'),
				'deal_time' => time(),
				'deal_content' => input('deal_content', ''),
				'inform_id' => $inform_id,
			];
			$subject_type_model = new InformModel();
			return $subject_type_model->editInform($data);
		}
	}
	
}