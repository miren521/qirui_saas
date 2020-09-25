<?php

namespace app\model\system;

use app\model\BaseModel;

/**
 * 楼层板块
 */
class FloorBlock extends BaseModel
{
    /**
     * 获取楼层样式列表
     */
    public function getList()
    {
        $list = model('pc_floor_block')->getList([], ['id', 'name', 'title', 'value']);
        return $this->success($list);
    }
}
