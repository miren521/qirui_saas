<?php
declare (strict_types = 1);

namespace app\model\saas;

use think\Model;

/**
 * @mixin think\Model
 */
class AuthRule extends BaseModel
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }


    //获取所有子权限id 集合
    public static function getAuthChildIds($id){
        $ids = AuthRule::where('pid',$id)->column('id');
        $list = $ids;
        if($ids){
            foreach ($ids as $k=>$v){
                $ids =  self::getAuthChildIds($v);
                $list = array_merge($ids,$list);
            }
        }
        return $list;

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

    /**
     * 权限设置选中状态
     * @param $cate  栏目
     * @param int $pid 父ID
     * @param $rules 规则
     * @return array
     */
    public static function authChecked($cate , $pid = 0,$rules){
        $list = [];
        $rulesArr = explode(',',$rules);
        foreach ($cate as $v){
            if ($v['pid'] == $pid) {
                $v['spread'] = true;
                if(self::authChecked($cate,$v['id'],$rules)){
                    $v['children'] =self::authChecked($cate,$v['id'],$rules);
                }else{
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
