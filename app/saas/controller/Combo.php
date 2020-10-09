<?php
declare (strict_types = 1);

namespace app\saas\controller;

use app\common\traits\Curd;
use app\model\saas\AuthGroup;
use app\model\saas\CustomerLevel;
use app\saas\validate\ComboCheck;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use app\model\saas\Menu;

class Combo extends BaseSaas
{
    use Curd;
    /**
     * 基础方法
     * User: 刘兴豪
     * Date: 2020-09-25
     */
    public function information(){
        return view();
    }

    /**
     * 修改
     * User: 刘兴豪
     * Date: 2020-09-25
     */
    public function management(){
        return view();
    }

    /**
     * 套餐管理
     */
    public function update(){
        if (request()->post()){
            $org_data = input();
            $comboCheck = new ComboCheck();
            if (!$comboCheck->check($org_data)){
                return json(['code'=>428,'message'=>$comboCheck->getError()]);
            }
            $res = Db::table('customer_level')->where('id',$org_data['id'])->update($org_data);
            if ($res){
                return json(['code'=>0,'message'=>'success','data'=>$res]);
            }else{
                return json(['code'=>400,'message'=>'修改失败，请联系管理员']);
            }
        }
    }

    /**
     * 套餐信息
     */
    public function list(){
        $res = Db::table('customer_level')
            ->field('id,type,annual_fee,agent_discount,grade')
            ->order('grade','ASC')
            ->select();
        return json(['code'=>0,'message'=>'success','data'=>$res]);
    }

    /**
     * 用户权限设置
     * User: mi
     * Date: 2020-09-29
     */
    public function group(){
        if (Request::isPost()) {
            //条件筛选
            $title = Request::param('title');
            //全局查询条件
            $where = [];
            if ($title) {
                $where[] = ['title', 'like', '%' . $title . '%'];
            }
            //显示数量
            $pageSize = Request::param('page_size', Config::get('app.page_size'));

            //查出所有数据
            $list = CustomerLevel::where($where)
                ->paginate(
                    $this->pageSize, false,
                    ['query' => Request::param()]
                )->toArray();

            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data']];
        }
        return view();
    }

    // 用户组状态修改
    public function groupState()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $info = CustomerLevel::find($id);
            $info->status = $info['status'] == 1 ? 0 : 1;
//            if($this->uid==3){
//                $this->error(lang('test data cannot edit'));
//            }
            $info->save();
            return retMsg(200, '操作成功');

        }
    }

    /**
     * 用户组显示权限
     * User: mi
     * Date: 2020-09-29
     */
    public function groupAccess()
    {
        if (Cache::get('admin_rule') == null){
            $admin_rule = Menu::where(['app_module' => 'admin'])
                ->field('id,parent,title,name')
                ->order('sort asc')->cache(3600)
                ->select()->toArray();

            Cache::set('admin_rule', $admin_rule, 3600);
        }

        $rules = CustomerLevel::where('id', Request::param('id'))
            ->where('status', 1)
            ->value('rules');

        $list = CustomerLevel::authChecked($pid = '', $rules);

        $group_id = Request::param('id');
        $idList = Menu::where(['app_module' => 'admin'])->column('id');
        sort($idList);

        $view = [
            'list' => $list,
            'idList' => $idList,
            'group_id' => $group_id,
        ];
        View::assign($view);
        return view();
    }

    // 用户组保存权限
    public function groupSetaccess()
    {
        $rules = \request()->post('rules');
        $rules = \Qiniu\json_decode($rules, true);
        if (empty($rules)) {
            return retMsg(0, '提交权限为空');
        }

        $data = \request()->post();
        $rules = CustomerLevel::authNormal($rules);

        $rls = '';
        foreach ($rules as $k => $v) {
            $rls .= $v['id'] . ',';
        }

        $where['id'] = $data['group_id'];
        $where['rules'] = $rls;
        if (CustomerLevel::update($where)) {
            $admin = Session::get('admin');
            $admin['rules'] = $rls;
            Session::set('admin', $admin);
            return retMsg(200, '操作成功');
        } else {
            $this->error(lang('rule assign fail'));
        }
    }

    // 用户组删除
    public function groupDel()
    {
        $ids = \request()->post('ids');
        if ($ids > 1) {
            $res = AuthGroup::find($ids);
            if (!$res->delete()) return retMsg(500, '操作失败');
            return retMsg(200, '操作成功');
        } else {
            $this->error(lang('supper man cannot delete'));
        }

    }
}
