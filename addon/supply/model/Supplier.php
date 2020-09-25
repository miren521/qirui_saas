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


namespace addon\supply\model;

use app\model\BaseModel;
use app\model\system\Group;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Cache;

/**
 * 供应商表
 */
class Supplier extends BaseModel
{

    /**
     * 添加供应商
     * @param $data
     * @param $user_info
     * @return array
     */
    public function addSupplier($data, $user_info)
    {

        $count = model('supplier')->getCount([['title', '=', $data['title']]]);
        if ($count > 0) {
            return $this->error('', '该供应商已经存在！');
        }

        //添加系统站
        $site_id                  = model("site")->add(['site_type' => 'supply']);
        $data['supplier_site_id'] = $site_id;
        $data['username']         = $user_info['username'];

        $res = model('supplier')->add($data);

        //添加系统用户组
        $group      = new Group();
        $group_data = [
            'site_id'     => $site_id,
            'app_module'  => 'supply',
            'group_name'  => '管理员组',
            'is_system'   => 1,
            'create_time' => time()
        ];
        $group_id   = $group->addGroup($group_data)['data'];
        // 添加供应商相册默认分组
        model("album")->add([
            'site_id' => $site_id,
            'album_name' => "默认分组",
            'update_time' => time(),
            'is_default' => 1,
            'app_module' => 'supply'
        ]);

        //用户检测
        if (empty($user_info['username'])) {
            return $this->error('', 'USER_NOT_EXIST');
        }
        $user_count = model("user")->getCount(
            [['username', '=', $user_info['username']], ['app_module', '=', 'supply']]
        );
        if ($user_count > 0) {
            return $this->error('', 'USERNAME_EXISTED');
        }

        //添加用户
        $data_user = [
            'app_module' => 'supply',
            'app_group'  => 0,
            'is_admin'   => 1,
            'group_id'   => $group_id,
            'group_name' => '管理员组',
            'site_id'    => $site_id
        ];
        $user_info = array_merge($data_user, $user_info);
        model("user")->add($user_info);
        Cache::tag("supply")->clear();
        return $this->success($res);
    }

    /**
     * 修改供应商
     * @param $condition
     * @param $data
     * @return array
     * @throws DbException
     */
    public function editSupplier($condition, $data)
    {
        $res = model('supplier')->update($data, $condition);
        Cache::tag("supply")->clear();

        //订单关闭
        if ($data["status"] == 0) {
            $check_condition = array_column($condition, 2, 0);
            $close_result = event("SupplyClose", ["site_id" => $check_condition["site_id"]], true);
            if ($close_result["code"] < 0) {
                return $close_result;
            }
        }
        return $this->success($res);
    }

    /**
     * 删除供货商
     * @param $supplier_id
     * @return array
     * @throws DbException
     */
    public function deleteSupplier($supplier_id)
    {
        //todo 删除供应商
        $goods_count = model('supply_goods')->getCount([['supplier_id', '=', $supplier_id]]);
        if ($goods_count > 0) {
            return $this->error('', '供应商下有商品，不可删除');
        }
        $res = model('supplier')->delete([['supplier_id', '=', $supplier_id]]);
        Cache::tag("supply")->clear();
        return $this->success($res);
    }

    /**
     * 获取供应商分页列表
     * @param array $where
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getSupplierPageList($where = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $data  = json_encode([$where, $field, $order, $page, $page_size]);
        $cache = Cache::get("supplier_getSupplierPageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model("supplier")->pageList($where, $field, $order, $page, $page_size);
        Cache::tag("supply")->set("supplier_getSupplierPageList_" . $data, $list);
        return $this->success($list);
    }

    /**
     * 获取供应商信息
     * @param array $condition
     * @param string $field
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getSupplierInfo($condition = [], $field = '*')
    {
//        $data = json_encode([$condition, $field]);
//        $cache = Cache::get("supplier_getSupplierInfo_" . $data);
//        if (!empty($cache)) {
//            return $this->success($cache);
//        }
        $info = model("supplier")->getInfo($condition, $field);
//        Cache::tag("supply")->set("supplier_getSupplierInfo_" . $data, $info);
        return $this->success($info);
    }

    /**
     * 获取供应商认证信息(包含结算账户)
     * @param $condition
     * @param string $field
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getSupplierCert($condition, $field = '*')
    {
        $res = model('supply_cert')->getInfo($condition, $field);
        return $this->success($res);
    }

    /**
     * 编辑店铺认证信息
     * @param $data
     * @param $condition
     * @return array
     * @throws DbException
     */
    public function editSupplierCert($data, $condition)
    {
        $res = model('supply_cert')->update($data, $condition);
        return $this->success($res);
    }

    /**
     * 供应商关闭
     * @param $site_id
     * @return array|mixed|string
     * @throws DbException
     */
    public function supplyClose($site_id)
    {
        $res = model("supplier")->update(["status" => 0], [["supplier_site_id", "=", $site_id]]);
        if ($res === false) {
            return $this->error();
        }

        $result = event("SupplyClose", ["site_id" => $site_id], true);
        if ($result["code"] < 0) {
            return $result;
        }
        return $this->success();
    }

    /**
     * 获取列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function getSupplyList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('supplier')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

}
