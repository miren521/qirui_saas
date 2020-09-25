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


namespace app\model\system;

use app\model\BaseModel;
/**
 * 站点地址库
 */
class SiteAddress extends BaseModel
{
    /**
     * ***********************************************商家地址开始*********************************************************************
     */

    /**
     * 添加商家地址
     * @param array $data
     */
    public function addSiteAddress($data){

        $address_model = model('site_address');

        $address_model->startTrans();
        try{
            $condition = array(
                ['site_id', '=', $data['site_id']]
            );
            $info = model('site_address')->getInfo($condition);
            if(empty($info)){
                $data['is_default'] = 1;
            }
            $res = $address_model->add($data);
            $address_model->commit();
            return $this->success($res);
        }catch(\Exception $e){
            $address_model->rollback();
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 修改商家地址
     * @param array $data
     * @param array $condition
     */
    public function editSiteAddress($data, $condition){

        $address_model = model('site_address');

        $address_model->startTrans();
        try{
            $res = $address_model->update($data, $condition);
            $address_model->commit();
            return $this->success($res);
        }catch(\Exception $e){
            $address_model->rollback();
            return $this->error([], $e->getMessage());
        }
    }


    /**
     * 设置地址为默认地址
     * @param $id
     * @param $site_id
     */
    public function setSiteAddressDefault($id,$site_id){
        $address_model = model('site_address');

        $address_model->startTrans();
        try{
            //现将其他地址转化为非默认地址
            model('site_address')->update(['is_default' => 0], [['site_id', '=', $site_id]]);
            model('site_address')->update(['is_default' => 1], [['site_id', '=', $site_id], ['id', '=', $id]]);

            $address_model->commit();
            return $this->success();
        }catch(\Exception $e){
            $address_model->rollback();
            return $this->error([], $e->getMessage());
        }
    }

    /**
     *
     * @param $condition
     * @return array
     */
    public function deleteSiteAddress($id,$site_id){

        $address_model = model('site_address');
        $address_model->startTrans();
        try{
            $condition = array(
                ['id', '=', $id],
                ['site_id', '=', $site_id],
            );
            $info = $address_model->getInfo($condition);
            if($info['is_default'] == 1){
                $address_model->rollback();
                return $this->error([], '默认地址不能删除');
            }
            $res = $address_model->delete($condition);
            $address_model->commit();
            return $this->success($res);
        }catch(\Exception $e){
            $address_model->rollback();
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 获取商家地址列表
     * @param array $condition
     * @param int $page
     * @param string $page_size
     * @param string $order
     * @param string $field
     */
    public function getSiteAddressPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*'){

        $address_model = model('site_address');
        $list = $address_model->pageList($condition, $field, $order, $page, $page_size);

        return $this->success($list);
    }

    /**
     * 获取商家地址列表
     * @param array $condition
     * @param string $order
     * @param string $field
     */
    public function getSiteAddressList($condition = [], $order = '', $field = '*'){

        $address_model = model('site_address');
        $list = $address_model->getList($condition, $field, $order);

        return $this->success($list);
    }

    /**
     * 获取商家地址详情
     * @param array $condition
     * @param string $field
     */
    public function getSiteAddressInfo($condition, $field = '*'){

        $address_model = model('site_address');
        $res = $address_model->getInfo($condition, $field);

        return $this->success($res);
    }


    /**
     * ***********************************************商家地址结束*********************************************************************
     */
}
