<?php


namespace app\saas\validate;


use think\Validate;

class CustomeCheck extends Validate
{
    protected $rule = [
        'account|账号'             => 'require|length:5,32',
        'passwd|密码'              => 'require|alphaNum|length:6,16',
        'province|省份'            => 'require|chs|length:2,12',
        'city|市/州'               => 'require|chs|length:2,12',
        'district|区/县'           => 'require|chs|length:2,12',
        'address|详细地址'          => 'chsDash|length:2,64',
        'customer_manager|客户经理' =>'length:2,12',
        'contact|联系人'            => 'require|chs|length:2,12',
        'contact_phone|电话'       =>'mobile',
        'mobile|手机号'             => 'mobile',
        'email|邮箱'               => 'email',
        'qq|QQ'                    => 'number|length:6,10',
        'wechat|微信号'             =>'alphaDash|length:5,24',
    ];

    //设置验证消息
    protected $message  =   [
    ];
}