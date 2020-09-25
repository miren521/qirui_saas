<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Category extends Model
{
    // 追加属性
    protected $append = [
        'status_text'
    ];

    public function getStatusList()
    {
        return ['goods' => '商品'];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    /**
     * 无限分类-权限
     * @param $cate            栏目
     * @param string $lefthtml 分隔符
     * @param int $pid         父ID
     * @param int $lvl         层级
     * @return array
     */
    public static function cateTree($cate , $lefthtml = '|— ' , $pid = 0 , $level = 0 ){
        $arr = array();
        foreach ($cate as $v){
            if ($v['pid'] == $pid) {
                $v['level']      = $level + 1;
                $v['lefthtml'] = str_repeat($lefthtml,$level);
                $v['ltitle']   = $v['lefthtml'].$v['title'];
                $arr[] = $v;
                $arr = array_merge($arr, self::cateTree($cate, $lefthtml, $v['id'], $level+1));
            }
        }
        return $arr;
    }
}
