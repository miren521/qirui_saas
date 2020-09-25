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


namespace app\model\express;

use think\facade\Cache;
use app\model\BaseModel;

/**
 * 物流公司
 */
class ExpressCompany extends BaseModel
{

    /***************************************************************** 系统物流公司start **********************************************************************/
    /**
     * 添加系统物流公司
     * @param unknown $data
     */
    public function addExpressCompany($data)
    {
        $id = model('express_company')->add($data);
        Cache::tag("express_company")->clear();
        return $this->success($id);
    }
    
    /**
     * 修改系统物流公司
     * @param unknown $data
     * @return multitype:string
     */
    public function editExpressCompany($data)
    {
        $res = model('express_company')->update($data, [[ 'company_id', '=', $data['company_id'] ]]);
        Cache::tag("express_company")->clear();
        return $this->success($res);
    }
    
    /**
     * 删除系统物流公司
     * @param unknown $condition
     */
    public function deleteExpressCompany($condition)
    {
        $res = model('express_company')->delete($condition);
        Cache::tag("express_company")->clear();
        return $this->success($res);
    }
    /**
     * 获取物流公司信息
     * @param unknown $condition
     * @param string $field
     */
    public function getExpressCompanyInfo($condition, $field = '*')
    {
        $data = json_encode([ $condition, $field]);
        $cache = Cache::get("express_company_getExpressCompanyInfo_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $res = model('express_company')->getInfo($condition, $field);
        if(!empty($res)){
            if(empty($res['content_json'])){
                $res['content_json'] = json_encode($this->getPrintItemList());
            }
        }
        Cache::tag("express_company")->set("express_company_getExpressCompanyInfo_" . $data, $res);
        return $this->success($res);
    }
    
    /**
     * 获取物流公司列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getExpressCompanyList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $data = json_encode([ $condition, $field, $order, $limit ]);
        $cache = Cache::get("express_company_getExpressCompanyList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('express_company')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("express_company")->set("express_company_getExpressCompanyList_" . $data, $list);
    
        return $this->success($list);
    }
    
    /**
     * 获取物流公司分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getExpressCompanyPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $data = json_encode([ $condition, $field, $order, $page, $page_size ]);
        $cache = Cache::get("express_company_getExpressCompanyPageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('express_company')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("express_company")->set("express_company_getExpressCompanyPageList_" . $data, $list);
        return $this->success($list);
    }

    /**
     * 修改物流公司排序
     * @param $sort
     * @param $company_id
     * @return array|\multitype
     */
    public function modifyExpressCompanySort($sort, $company_id)
    {
        Cache::tag("express_company")->clear();
        $res = model('express_company')->update(['sort' => $sort], [['company_id', '=', $company_id]]);
        return $this->success($res);
    }

    /***************************************************************** 系统物流公司end **********************************************************************/

    /***************************************************************** 店铺物流公司start **********************************************************************/

    /**
     * 添加店铺物流公司
     * @param unknown $data
     */
    public function addExpressCompanyShop($data)
    {
        $data["create_time"] = time();
        $data["modify_time"] = time();
        $company_info = $this->getExpressCompanyInfo([["company_id", "=", $data["company_id"]]]);
        $data["company_name"] = $company_info["data"]["company_name"];
        $brand_id = model('express_company_shop')->add($data);
        Cache::tag("express_company_shop")->clear();
        return $this->success($brand_id);
    }

    /**
     * 修改店铺物流公司
     * @param unknown $data
     * @return multitype:string
     */
    public function editExpressCompanyShop($data, $condition)
    {
        $data["modify_time"] = time();
        $res = model('express_company_shop')->update($data, $condition);
        Cache::tag("express_company_shop")->clear();
        return $this->success($res);
    }

    /**
     * 删除店铺物流公司
     * @param unknown $condition
     */
    public function deleteExpressCompanyShop($condition)
    {
        $res = model('express_company_shop')->delete($condition);
        Cache::tag("express_company_shop")->clear();
        return $this->success($res);
    }
    /**
     * 获取店铺物流公司信息
     * @param unknown $condition
     * @param string $field
     */
    public function getExpressCompanyShopInfo($condition, $field = 'id, site_id, company_id, content_json, background_image, font_size, width, height, create_time, modify_time, scale')
    {
        $data = json_encode([ $condition, $field]);
        $cache = Cache::get("express_company_shop_getExpressCompanyShopInfo_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $res = model('express_company_shop')->getInfo($condition, $field);
        if(!empty($res)){
            if(empty($res['content_json'])){
                $res['content_json'] = json_encode($this->getPrintItemList());
            }
        }
        Cache::tag("express_company_shop")->set("express_company_shop_getExpressCompanyShopInfo_" . $data, $res);
        return $this->success($res);
    }

    /**
     * 获取店铺物流公司列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param string $limit
     */
    public function getExpressCompanyShopList($condition = [], $field = 'id, site_id, company_id, content_json, background_image, font_size, width, height, create_time, modify_time, scale, company_name', $order = '', $limit = null)
    {
        $data = json_encode([ $condition, $field, $order, $limit ]);
        $cache = Cache::get("express_company_shop_getExpressCompanyShopList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('express_company_shop')->getList($condition, $field, $order, '', '', '', $limit);
        Cache::tag("express_company_shop")->set("express_company_shop_getExpressCompanyShopList_" . $data, $list);

        return $this->success($list);
    }

    /**
     * 获取店铺物流公司分页列表
     * @param array $condition
     * @param number $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getExpressCompanyShopPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = 'id, site_id, company_id, content_json, background_image, font_size, width, height, create_time, modify_time, scale')
    {
        $data = json_encode([ $condition, $field, $order, $page, $page_size ]);
        $cache = Cache::get("express_company_shop_getExpressCompanyShopPageList_" . $data);
        if (!empty($cache)) {
            return $this->success($cache);
        }
        $list = model('express_company_shop')->pageList($condition, $field, $order, $page, $page_size);
        Cache::tag("express_company_shop")->set("express_company_shop_getExpressCompanyShopPageList_" . $data, $list);
        return $this->success($list);
    }


    /***************************************************************** 店铺物流公司end **********************************************************************/
    /**
     * 获取打印项
     * @return array
     */
    public function getPrintItemList()
    {
        $data = [
            [
                'item_name' => 'order_no',
                'item_title'=>'订单编号',
            ],
            [
                'item_name' => 'sender_company',
                'item_title'=>'发件人公司',
            ],
            [
                'item_name' => 'sender_name',
                'item_title'=>'发件人姓名',
            ],
            [
                'item_name' => 'sender_address',
                'item_title'=>'发件人地址',
            ],
            [
                'item_name' => 'sender_phone',
                'item_title'=>'发件人电话',
            ],
            [
                'item_name' => 'sender_post_code',
                'item_title'=>'发件人邮编',
            ],
            [
                'item_name' => 'receiver_name',
                'item_title'=>'收件人姓名',
            ],
            [
                'item_name' => 'receiver_address',
                'item_title'=>'收件人地址',
            ],
            [
                'item_name' => 'receiver_phone',
                'item_title'=>'收件人电话',
            ],
            [
                'item_name' => 'receiver_post_code',
                'item_title'=>'收件人邮编',
            ],
            [
                'item_name' => 'logistics_number',
                'item_title'=>'货到付款物流编号',
            ],
            [
                'item_name' => 'collection_payment',
                'item_title'=>'代收金额',
            ],
            [
                'item_name' => 'remark',
                'item_title'=>'备注',
            ],
        ];
        return $data;
    }
}