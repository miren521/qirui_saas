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


namespace app\model\member;

use addon\wechat\model\Message as WechatMessage;
use app\model\BaseModel;
use app\model\message\Email;
use app\model\message\Sms;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 登录
 *
 * @author Administrator
 *
 */
class Login extends BaseModel
{

    /**
     * 用户登录
     * @param unknown $data 必然传输username
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function login($data)
    {

        //必然传输usern
        $info = model("member")->getInfo([['username|mobile|email', '=', $data['username']], ['password', '=', data_md5($data['password'])]], 'member_id,
            username, nickname, mobile, email, status,last_login_time');
        if (empty($info)) {
            return $this->error('', 'USERNAME_OR_PASSWORD_ERROR');
        } elseif ($info['status'] == 0) {
            return $this->error('', 'MEMBER_IS_LOCKED');
        } else {
            //更新登录时间
            model("member")->update([
                'login_time'      => time(),
                'last_login_time' => time(),
                'login_ip'        => request()->ip(),
            ], [['member_id', '=', $info['member_id']]]);


            //执行登录奖励
            event("MemberLogin", ['member_id' => $info['member_id']], true);

            //用户第三方信息刷新
            $this->refreshAuth($info['member_id'], $data);
            return $this->success($info);
        }
    }

    /**
     * 第三方登录
     * @param array $data 必然传输auth_tag, auth_openid
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function authLogin($data)
    {
        $info = model("member")->getInfo([[$data['auth_tag'], '=', $data['auth_openid']]], 'member_id,
            username, nickname, mobile, email, status, last_login_time');
        if (empty($info)) {
            return $this->error('', 'MEMBER_NOT_EXIST');
        } elseif ($info['status'] == 0) {
            return $this->error('', 'MEMBER_IS_LOCKED');
        } else {
            event("MemberLogin", ['member_id' => $info['member_id']], true);
            return $this->success($info);
        }
    }

    /**
     * 刷新第三方信息
     * @param $member_id
     * @param $data
     * @return array
     * @throws DbException
     */
    private function refreshAuth($member_id, $data)
    {
        $data = [
            'qq_openid'      => isset($data['qq_openid']) ? $data['qq_openid'] : '',
            'wx_openid'      => isset($data['wx_openid']) ? $data['wx_openid'] : '',
            'weapp_openid'   => isset($data['weapp_openid']) ? $data['weapp_openid'] : '',
            'wx_unionid'     => isset($data['wx_unionid']) ? $data['wx_unionid'] : '',
            'ali_openid'     => isset($data['ali_openid']) ? $data['ali_openid'] : '',
            'baidu_openid'   => isset($data['baidu_openid']) ? $data['baidu_openid'] : '',
            'toutiao_openid' => isset($data['toutiao_openid']) ? $data['toutiao_openid'] : '',
        ];
        if (!empty($data['qq_openid'])) {
            model("member")->update(['qq_openid' => ''], [['qq_openid', '=', $data['qq_openid']]]);
            model("member")->update(['qq_openid' => $data['qq_openid']], [['member_id', '=', $member_id]]);
        }
        if (!empty($data['wx_openid'])) {
            model("member")->update(['wx_openid' => ''], [['wx_openid', '=', $data['wx_openid']]]);
            model("member")->update(['wx_openid' => $data['wx_openid']], [['member_id', '=', $member_id]]);
        }
        if (!empty($data['weapp_openid'])) {
            model("member")->update(['weapp_openid' => ''], [['weapp_openid', '=', $data['weapp_openid']]]);
            model("member")->update(['weapp_openid' => $data['weapp_openid']], [['member_id', '=', $member_id]]);
        }
        if (!empty($data['wx_unionid'])) {
            model("member")->update(['wx_unionid' => ''], [['wx_unionid', '=', $data['wx_unionid']]]);
            model("member")->update(['wx_unionid' => $data['wx_unionid']], [['member_id', '=', $member_id]]);
        }
        if (!empty($data['ali_openid'])) {
            model("member")->update(['ali_openid' => ''], [['ali_openid', '=', $data['ali_openid']]]);
            model("member")->update(['ali_openid' => $data['ali_openid']], [['member_id', '=', $member_id]]);
        }
        if (!empty($data['baidu_openid'])) {
            model("member")->update(['baidu_openid' => ''], [['baidu_openid', '=', $data['baidu_openid']]]);
            model("member")->update(['baidu_openid' => $data['baidu_openid']], [['member_id', '=', $member_id]]);
        }
        if (!empty($data['toutiao_openid'])) {
            model("member")->update(['toutiao_openid' => ''], [['toutiao_openid', '=', $data['toutiao_openid']]]);
            model("member")->update(['toutiao_openid' => $data['toutiao_openid']], [['member_id', '=', $member_id]]);
        }
        return $this->success();
    }

    /**
     * 检测openid是否存在
     * @param array $data
     * @return array
     */
    public function openidIsExits(array $data)
    {
        $count = model("member")->getCount([[$data['auth_tag'], '=', $data['auth_openid']]]);
        return $this->success($count);
    }

    /**
     * 用户登录
     * @param $data
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function mobileLogin($data)
    {

        //必然传输usern
        $info = model("member")->getInfo([['mobile', '=', $data['mobile']]], 'member_id,
            username, nickname, mobile, email, status,last_login_time');
        if (empty($info)) {
            return $this->error('', 'MEMBER_NOT_EXIST');
        } elseif ($info['status'] == 0) {
            return $this->error('', 'MEMBER_IS_LOCKED');
        } else {
            //更新登录时间
            model("member")->update([
                'login_time'      => time(),
                'last_login_time' => time(),
                'login_ip'        => request()->ip(),
            ], [['member_id', '=', $info['member_id']]]);

            event("MemberLogin", ['member_id' => $info['member_id']], true);

            //用户第三方信息刷新
            $this->refreshAuth($info['member_id'], $data);
            return $this->success($info);
        }
    }

    /**
     * 登录动态码
     * @param $data
     * @return array|mixed|void
     */
    public function loginCode($data)
    {
        //发送短信
        $sms_model           = new Sms();
        $var_parse           = array(
            "code" => $data["code"],
        );
        $data["sms_account"] = $data["mobile"] ?? '';//手机号
        $data["var_parse"]   = $var_parse;
        $sms_result          = $sms_model->sendMessage($data);
        if ($sms_result["code"] < 0) {
            return $sms_result;
        }

        //发送邮箱
        $email_model = new Email();
        //有邮箱才发送
        $data["email_account"] = $data["email"] ?? '';//邮箱号
        $email_result          = $email_model->sendMessage($data);
        if ($email_result["code"] < 0) {
            return $email_result;
        }

        return $this->success();
    }

    /**
     * 登录通知
     * @param $data
     * @return array|mixed|void
     */
    public function loginSuccess($data)
    {
        $member_model       = new Member();
        $member_info_result = $member_model->getMemberInfo(
            [["member_id", "=", $data["member_id"]]],
            "username,mobile,email,reg_time,wx_openid,last_login_type,login_time"
        );
        $member_info        = $member_info_result["data"];

        //发送短信
        $sms_model           = new Sms();
        $var_parse           = array(
            "name" => $member_info["username"],//验证码
        );
        $data["sms_account"] = $member_info["mobile"] ?? '';//手机号
        $data["var_parse"]   = $var_parse;
        $sms_result          = $sms_model->sendMessage($data);
        //        if($sms_result["code"] < 0)
        //            return $sms_result;

        //发送邮箱
        $email_model = new Email();
        //有邮箱才发送
        $data["email_account"] = $member_info["email"] ?? '';//邮箱号
        $email_result          = $email_model->sendMessage($data);
        //        if($email_result["code"] < 0)
        //            return $email_result;


        //发送模板消息
        $wechat_model   = new WechatMessage();
        $data["openid"] = $member_info["wx_openid"];

//         if(!empty($member_info["username"])){
//            $user_account = $member_info["username"];
//         }else{
//            if(!empty($member_info["mobile"])){
//              $user_account = $member_info["mobile"];
//            }else{
//              $user_account = $member_info["email"];
//            }
//         }
        $username = '';
        if (!empty($member_info["username"])) {
            $username = $member_info["username"];
        }
        if (empty($username) && !empty($member_info["mobile"])) {
            $username = $member_info["mobile"];
        }
        if (empty($username)) {
            $username = '访客';
        }

        $data["template_data"] = [
            'keyword1' => $username,
            'keyword2' => '登录成功',
            'keyword3' => time_to_date($member_info["login_time"]),
        ];

        $data["page"] = '';
        $wechat_model->sendMessage($data);

        return $this->success();
    }
}
