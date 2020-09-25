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


namespace app\model\shop;


use app\model\BaseModel;
use app\model\system\Cron;
use app\model\web\WebSite;

/**
 * 店铺续签表
 */
class ShopReopen extends BaseModel
{
    /**
     * 申请续签(上传支付凭据)
     * @param unknown $data
     */
    public function addReopen($data)
    {
        //查询是否已有申请记录
        $reopen = $this->getReopenList([['site_id', '=', $data['site_id']], ['apply_state', '=', 1]], 'id');
        if (!empty($reopen['data'])) {
            return $this->error('', '请勿重复申请续签');
        }
        //查询店铺的结束时间
        $shop_model = new Shop();
        $shop = $shop_model->getShopInfo([['site_id', '=', $data['site_id']]], 'website_id,expire_time');
        $expire_time = $shop['data']['expire_time'];
        if ($expire_time == 0) {
            return $this->error('', '您的店铺已永久授权，不需要申请续签');
        }
        if ($expire_time > time()) {
            $cha = $expire_time - time();
            $date = ceil(($cha/86400));

            if($date > 30){
                return $this->error('', '离到期30天内才可以申请续签');
            }
        }

        $data['website_id'] = $shop['data']['website_id'];
        $data['create_time'] = time();
        $data['apply_state'] = 1;
        $data['reopen_no'] = date('YmdHi').rand(1111,9999);
        $res = model("shop_reopen")->add($data);
        return $this->success($res);
    }

    /**
     * 获取续签申请金额
     * @param unknown $apply_year
     * @param unknown $group_id
     */
    public function getReopenMoney($apply_year, $group_id)
    {
        $shop_group = new ShopGroup();
        $group_info = $shop_group->getGroupInfo([['group_id', '=', $group_id]], 'fee');

        return success($group_info['data']['fee'] * $apply_year);
    }

    /**
     * 编辑续签信息
     * @param unknown $data
     * @return multitype:number unknown
     */
    public function editReopen($data)
    {
        $data['apply_state'] = 1;
        $res = model("shop_reopen")->update($data, [['id', '=', $data['id'], ['site_id', '=', $data['site_id']]]]);
        return $this->success($res);
    }

    /**
     * 拒绝续签
     * @param unknown $id
     * @param unknown $reason
     */
    public function refuse($id, $reason)
    {
        $res = model("shop_reopen")->update(['apply_message' => $reason, 'apply_state' => -1], [['id', '=', $id]]);
        return $this->success($res);
    }

    /**
     * 续签通过
     * @param unknown $id
     * @return multitype:number unknown
     */
    public function pass($id, $site_id)
    {
        $data = [
            'apply_state' => 2,
            'audit_time' => time()
        ];
        model("shop_reopen")->startTrans();
        try{

            $shop_info = model("shop")->getInfo([['site_id', '=', $site_id]], '*');
            $reopen_info = model("shop_reopen")->getInfo([['id', '=', $id], ['site_id', '=', $site_id]]);

            model("shop_reopen")->update($data, [['id', '=', $id], ['site_id', '=', $site_id]]);

            model("shop")->setInc([['site_id', '=', $site_id]], "shop_open_fee", $reopen_info['paying_amount']);

            //修改店铺信息
            $shop_data = [
                'expire_time' => strtotime('+'.$reopen_info['apply_year'].'year',$shop_info['expire_time']),
                'group_id' => $reopen_info['shop_group_id'],
                'group_name' => $reopen_info['shop_group_name'],
            ];
            model('shop')->update($shop_data,[['site_id', '=', $site_id]]);
            //修改用户权限
            $user_data = [
                'app_group' => $reopen_info['shop_group_id'],
                'group_name' => $reopen_info['shop_group_name'],
            ];
            model('user')->update($user_data,[['site_id', '=', $site_id]]);

            //添加入驻费用流水
            if ($reopen_info['paying_amount'] > 0) {

                if($reopen_info['website_id'] > 0){
                    //获取分站信息
                    $website_model = new WebSite();
                    $website_info = $website_model->getWebSite([ ['site_id','=',$reopen_info['website_id']] ],'site_area_name,shop_rate');
                    $website_name = $website_info['data']['site_area_name'];
                    if(isset($website_info['data']['shop_rate']) && $website_info['data']['shop_rate'] > 0){
                        $website_commission = floor($reopen_info['paying_amount']*$website_info['data']['shop_rate'])/100;

                    }else{
                        $website_commission = 0;
                    }
                }else{
                    $website_name = '全国';
                    $website_commission = $reopen_info['paying_amount'];
                }

                $open_shop_data = [
                    'account_no' => $reopen_info['reopen_no'],
                    'site_id' => $reopen_info['site_id'],
                    'site_name' => $shop_info['site_name'],
                    'money' => $reopen_info['paying_amount'],
                    'type' => 2,
                    'type_name' => '店铺续签费用',
                    'relate_id' => $reopen_info['id'],
                    'create_time' => time(),
                    'website_id' => $reopen_info['website_id'],
                    'website_name' => $website_name,
                    'website_commission' => $website_commission
                ];
                model('shop_open_account')->add($open_shop_data);
            }

            model("shop_reopen")->commit();
            return $this->success();

        }catch(\Exception $e)
        {
            model("shop_reopen")->rollback();
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 续签申请通过事件执行
     * @param unknown $id
     */
    public function cronShopReopen($id)
    {
        $reopen_info = model("shop_reopen")->getInfo([['id', '=', $id]], '*');

        if ($reopen_info['apply_state'] == 2) {
            try {
                $shop_group = new ShopGroup();
                $shop_group_info = $shop_group->getGroupInfo([['group_id', '=', $reopen_info['shop_group_id']]], 'group_name');
                $data = [
                    'expire_time' => $reopen_info['expire_time'] + 365 * 24 * 3600 * $reopen_info['apply_year'],
                    'group_id' => $reopen_info['shop_group_id'],
                    'group_name' => $shop_group_info['group_name']
                ];
                $res = model("shop")->update($data, [['site_id', '=', $reopen_info['site_id']]]);
                return $this->success($res);
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

        } else {
            return $this->error("unknow Reopen info");
        }

    }

    /**
     * 获取申请信息
     * @param $condition
     * @param string $field
     * @return array
     */
    public function getReopenInfo($condition, $field = '*')
    {
        $field = 'sr.*,s.site_name';
        $alias = 'sr';
        $join = [
            [
                'shop s',
                's.site_id = sr.site_id',
                'left'
            ],
        ];
        $info = model("shop_reopen")->getInfo($condition, $field, $alias, $join);
        return $this->success($info);
    }

    /**
     * 获取申请列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getReopenList($condition = [], $field = '*', $order = '', $limit = null)
    {

        $list = model('shop_reopen')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取店铺分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getReopenPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {

        $list = model('shop_reopen')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }


    /**
     * 后台获取店铺申请续签分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getApplyReopenPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS)
    {
        $field = 'r.*,s.site_name,s.category_name,s.group_name,r.paying_money_certificate';
        $alias = 'r';
        $join = [
            [
                'shop s',
                'r.site_id = s.site_id',
                'left'
            ],

        ];
        $list = model("shop_reopen")->pageList($condition, $field,
            'r.apply_state,r.create_time desc', $page, $page_size, $alias, $join);
        return $this->success($list);

    }


    /*
     *  删除续签
     */
    public function deleteReopen($id)
    {
        $reopen = model("shop_reopen")->getInfo([['id', '=', $id]]);
        if (in_array($reopen['apply_state'], [1, -1])) {
            $res = model("shop_reopen")->delete([['id', '=', $id]]);
            return $this->success($res);
        } else {
            return $this->error("error");
        }

    }

    /**
     * @param $condition
     * @return array续签数量
     */
    public function getApplyReopenCount($condition){
        $count = model("shop_reopen")->getCount($condition);
        return $this->success($count);
    }

}