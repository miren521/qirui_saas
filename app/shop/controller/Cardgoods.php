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


namespace app\shop\controller;

/**
 * 卡券商品
 * Class Cardgoods
 * @package app\shop\controller
 */
class Cardgoods extends BaseShop
{

	
	public function __construct()
	{
		//执行父类构造函数
		parent::__construct();
		
	}

    /**
     * 商品列表
     * @return mixed
     */
	public function lists()
	{
	    return $this->fetch("cardgoods/lists");
	}

    /**
     * 添加商品
     * @return mixed
     */
	public function addGoods()
    {
        return $this->fetch("cardgoods/add_goods");
    }

    /**
     * 编辑商品
     * @return mixed
     */
    public function editGoods()
    {
        return $this->fetch("cardgoods/edit_goods");
    }
    
}