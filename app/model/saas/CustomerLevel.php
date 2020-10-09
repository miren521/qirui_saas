<?php

namespace app\model\saas;

use think\facade\Cache;
use think\Model;

/**
 * @mixin think\Model
 */
class CustomerLevel extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * 权限设置选中状态
     * @param $cate  栏目
     * @param int $pid 父ID
     * @param $rules 规则
     * User: mi
     * Date: 2020-09-29
     */
    public static function authChecked($pid = '', $rules)
    {
        $cate = Cache::get('admin_rule');
        $list = [];
        $rulesArr = explode(',', $rules);
        foreach ($cate as $v) {
            if ($v['parent'] == $pid) {
                $v['spread'] = true;
                if (self::authChecked($v['name'], $rules)) {
                    $v['children'] = self::authChecked($v['name'], $rules);
                } else {
                    if (in_array($v['id'], $rulesArr)) {
                        $v['checked'] = true;
                    }
                }
                $list[] = $v;
            }
        }
        return $list;
    }

    /**
     * 权限多维转化为二维
     * @param $cate  栏目
     * @param int $pid 父ID
     * @param $rules 规则
     * @return array
     */
    public static function authNormal($cate){
        $list = [];
        foreach ($cate as $v){
            $list[]['id'] = $v['id'];
//        $list[]['title'] = $v['title'];
//        $list[]['pid'] = $v['pid'];
            if (!empty($v['children'])) {
                $listChild =  self::authNormal($v['children']);
                $list = array_merge($list,$listChild);
            }
        }
        return $list;
    }

}
