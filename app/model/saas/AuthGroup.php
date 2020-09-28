<?php
declare (strict_types = 1);

namespace app\model\saas;

use think\Model;

/**
 * @mixin think\Model
 */
class AuthGroup extends Model
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
}
