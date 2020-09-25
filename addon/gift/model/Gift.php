<?php
// +---------------------------------------------------------------------+
// | NiuCloud | [ WE CAN DO IT JUST NiuCloud ]                |
// +---------------------------------------------------------------------+
// | Copy right 2019-2029 www.niucloud.com                          |
// +---------------------------------------------------------------------+
// | Author | NiuCloud <niucloud@outlook.com>                       |
// +---------------------------------------------------------------------+
// | Repository | https://github.com/niucloud/framework.git          |
// +---------------------------------------------------------------------+

namespace addon\gift\model;

use app\model\BaseModel;
/**
 * 礼品管理
 */
class Gift extends BaseModel
{
	/**
	 * 添加礼品
	 * @param unknown $data
	 */
	public function addGift($data)
	{
		$data['create_time'] = time();
		$gift_id = model('promotion_gift')->add($data);
		return $this->success($gift_id);
	}
	
	/**
	 * 修改礼品
	 * @param unknown $data
	 * @return multitype:string
	 */
	public function editGift($data)
	{
		$res = model('promotion_gift')->update($data, [ 'gift_id' => $data['gift_id'] ]);
		return $this->success($res);
	}
	
	/**
	 * 删除礼品
	 * @param unknown $condition
	 */
	public function deleteGift($condition)
	{
		$res = model('promotion_gift')->update(['is_delete' => 1], $condition);
		return $this->success($res);
	}
	
	/**
	 * 修改排序
	 * @param int $sort
	 * @param int $gift_id
	 */
	public function modifyGiftSort($sort, $gift_id)
	{
		$res = model('promotion_gift')->update([ 'sort' => $sort ], [ [ 'gift_id', '=', $gift_id ] ]);
		return $this->success($res);
	}

    /**
     * 增加库存
     * @param $param
     */
    public function incStock($param)
    {
        $condition = array(
            [ "gift_id", "=", $param["gift_id"] ]
        );
        $num = $param["num"];
        $gift_info = model("promotion_gift")->getInfo($condition, "gift_stock");
        if (empty($gift_info))
            return $this->error(-1, "");

        //编辑礼品库存
        $result = model("promotion_gift")->setInc($condition, "gift_stock", $num);
        return $this->success($result);
    }

    /**
     * 增加库存
     * @param $param
     */
    public function decStock($param)
    {
        $condition = array(
            [ "gift_id", "=", $param["gift_id"] ]
        );
        $num = $param["num"];
        $gift_info = model("promotion_gift")->getInfo($condition, "gift_stock");
        if (empty($gift_info))
            return $this->error(-1, "找不到赠品");

        //编辑sku库存
        if($gift_info["gift_stock"] < $num)
            return $this->error(-1, "库存不足");

        $result = model("promotion_gift")->setDec($condition, "gift_stock", $num);
        if ($result === false)
            return $this->error();

        return $this->success($result);
    }
    
	/**
	 * 获取礼品信息
	 * @param array $condition
	 * @param string $field
	 */
	public function getGiftInfo($condition, $field = '*')
	{
		$res = model('promotion_gift')->getInfo($condition, $field);
		return $this->success($res);
	}
	
	/**
	 * 获取礼品列表
	 * @param array $condition
	 * @param string $field
	 * @param string $order
	 * @param string $limit
	 */
	public function getGiftList($condition = [], $field = '*', $order = '', $limit = null)
	{
	    
		$list = model('promotion_gift')->getList($condition, $field, $order, '', '', '', $limit);
		return $this->success($list);
	}
	
	/**
	 * 获取礼品分页列表
	 * @param array $condition
	 * @param number $page
	 * @param string $page_size
	 * @param string $order
	 * @param string $field
	 */
	public function getGiftPageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
	{

		$list = model('promotion_gift')->pageList($condition, $field, $order, $page, $page_size);
		return $this->success($list);
	}
	
    /**
     * 生成礼品编号
     */
    public function getGiftNo($prefix = 'GIFT')
    {
        $rand = mt_rand(10000, 99999);
        $time = date('YmdHis', time());
        return $prefix.$time.$rand;
    }
    
}