<?php
/**
 * lemocms
 * ============================================================================
 * 版权所有 2018-2027 lemocms，并保留所有权利。
 * 网站地址: https://www.lemocms.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/8/2
 */
namespace app\common\traits;
/**
 * Trait Curd
 * @package common\traits
 */
trait Curd
{

    protected $pageSize=15;
    protected $page;
    protected  $modelClass;
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        if ($this->request->isPost()) {
            list($this->page, $this->pageSize,$where) = $this->buildParames();
            $list = $this->modelClass
                ->where($where)
                ->paginate(['list_rows' => $this->pageSize, 'page' => $this->page])
                ->toArray();
            $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list, 'count' => count($list)];
            return json($result);
        }
        return view();
    }

    /**
     * @return \think\response\View
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            try {
                $save = $this->model->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        return view();
    }

    /**
     * @param $id
     * @return \think\response\View
     */
    public function edit($id)
    {
        $list = $this->modelClass->find($id);
        if(empty($list)) $this->error('data is not exist');
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            try {
                $save = $info->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('save succss') : $this->error('save fail');
        }
        $view = ['info'=>$info];
        return view('',$view);
    }
    /**
     * 删除
     * @param $id
     * @return mixed
     */
    public function delete($ids)
    {
        $list = $this->modelClass->whereIn('id', $ids)->select();
        if(empty($list))$this->error('data is not exist');

        try {
            $save = $list->delete();
        } catch (\Exception $e) {
            $this->error(lang("delete success"));
        }

        $save ? $this->success(lang('delete success')) :  $this->error(lang("delete fail"));
    }

    /**
     * 伪删除
     * @param $id
     * @return mixed
     */
    public function destroy($ids)
    {
        $list = $this->modelClass->whereIn('id', $ids)->select();
        if(empty($list)) $this->error('data is not exist');
        try {
            foreach ($list as $k=>$v){
                $v->status = -1;
                $v->save();
            }
        } catch (\Exception $e) {
            $this->error(lang("delete success"));
        }

        $this->success(lang("delete success"));

    }
    public function sort($id)
    {
        $model = $this->findModel($id);
        if(empty($model))$this->error('data is not exist');
        $sort = input('sort');
        $save = $model->sort = $sort;
        $save ? $this->success(lang('save success')) :  $this->error(lang("save fail"));


    }

    /**
     * 返回模型
     * @param $id
     */
    protected function findModel($id)
    {
        if (empty($id) || empty($model = $this->modelClass->find($id))) {
            return '';
        }
        return $model;
    }
    protected function buildParames()
    {
        $param = $this->request->param();
        $page = isset($param['page']) && !empty($param['page']) ? $param['page'] : 1;
        $pageSize = isset($param['limit']) && !empty($param['limit']) ? $param['limit'] : 15;
        $where = ['name|title','like','%'.input('keys').'%'];
        return [$page, $pageSize,$where];
    }
}