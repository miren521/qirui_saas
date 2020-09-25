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

namespace addon\printer\model;

use app\model\BaseModel;

class PrinterTemplate extends BaseModel
{

    /**
     * 添加打印模板
     * @param $data
     * @return array
     */
    public function addPrinterTemplate($data)
    {
        $data['create_time'] = time();
        $res = model('printer_template')->add($data);
        return $this->success($res);
    }


    /**
     * 编辑打印模板
     * @param $data
     * @return array
     */
    public function editPrinterTemplate($data)
    {
        $data['update_time'] = time();
        $res = model('printer_template')->update($data,[ ['template_id','=',$data['template_id']] ]);
        return $this->success($res);
    }

    /**
     * 删除
     * @param $condition
     * @return array
     */
    public function deletePrinterTemplate($condition)
    {
        $res = model('printer_template')->delete($condition);
        return $this->success($res);
    }

    /**
     * 获取打印模板信息
     * @param array $condition
     * @param string $field
     * @return array
     */
    public function getPrinterTemplateInfo($condition = [], $field = '*')
    {
        $res = model('printer_template')->getInfo($condition,$field);
        return $this->success($res);
    }

    /**
     * 获取打印模板列表
     * @param array $condition
     * @param string $field
     * @param string $order
     * @param null $limit
     * @return array
     */
    public function getPrinterTemplateList($condition = [], $field = '*', $order = '', $limit = null)
    {
        $list = model('printer_template')->getList($condition, $field, $order, '', '', '', $limit);
        return $this->success($list);
    }

    /**
     * 获取打印模板分页列表
     * @param array $condition
     * @param int $page
     * @param int $page_size
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getPrinterTemplatePageList($condition = [], $page = 1, $page_size = PAGE_LIST_ROWS, $order = '', $field = '*')
    {
        $list = model('printer_template')->pageList($condition, $field, $order, $page, $page_size);
        return $this->success($list);
    }

}