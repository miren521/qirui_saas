<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Goods extends Model
{
    public function shopinfo(){
        return $this->hasOne('shops', 'id', 'shop_id');
    }

    public function category(){
        return $this->hasOne('category', 'id', 'category');
    }

    public function categoryTwo(){
        return $this->hasOne('category', 'id', 'category_two');
    }

}
