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

namespace addon\wechat\model;


use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Article;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use app\model\BaseModel;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 微信公众号
 */
class Wechat extends BaseModel
{

    private $app;//微信公众对象

    public function __construct()
    {
        $config_model  = new Config();
        $config_result = $config_model->getWechatConfig();
        $config        = $config_result["data"];
        if (!empty($config)) {
            $config_info = $config["value"];
        }

        $config    = [
            'app_id'        => $config_info["appid"] ?? '',
            'secret'        => $config_info["appsecret"] ?? '',
            'token'         => $config_info["token"] ?? '',          // Token
            'aes_key'       => $config_info['encodingaeskey'] ?? '',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            /**
             * 日志配置
             *
             * level: 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
             * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
             * file：日志文件位置(绝对路径!!!)，要求可写权限
             */
            'log'           => [
                'level'      => 'debug',
                'permission' => 0777,
                'file'       => 'runtime/log/wechat/easywechat.logs',
            ],
        ];
        $this->app = Factory::officialAccount($config);

//        $response = $this->app->server->serve();
        // 将响应输出
//        $response->send();exit; // Laravel 里请使用：return $response;
    }

    /**
     * 创建微信菜单
     * @param $buttons
     */
    public function menu($buttons)
    {
        try {
            $result = $this->app->menu->create($buttons);
            if ($result['errcode'] == 0) {
                return $this->success();
            } else {
                return $this->error($result, $result["errmsg"]);
            }
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 拉取粉丝列表
     * @param $nextOpenId
     * @return \multitype
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function user($nextOpenId = null)
    {
        try {
            $result = $this->app->user->list($nextOpenId);  // $nextOpenId 可选

            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error($result, $result["errmsg"]);
            }
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 查询多个粉丝信息
     * @param $user_list  数组 [$openId1, $openId2, ...]
     */
    public function selectUser($user_list)
    {
        try {
            $result = $this->app->user->select($user_list);  // $nextOpenId 可选
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error($result, $result["errmsg"]);
            }
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 得到粉丝信息
     */
    public function getUser($openId)
    {
        try {
            $result = $this->app->user->get($openId);  // $nextOpenId 可选
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error($result, $result["errmsg"]);
            }
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * TODO
     * 根据 Code 获取用户 session 信息
     * @param $param
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function authCodeToOpenid($params = [])
    {
        try {
            if (empty($_REQUEST["code"])) {
                $auth_result = $this->getAuthCode(request()->url(true));
                if ($auth_result["code"] >= 0) {
                    Header("Location: " . $auth_result["data"]);
                } else {
                    return $auth_result;
                }

            }
            $user = $this->app->oauth->user();//获取授权用户
            $data = [
                'openid'   => $user->getId(),
                'userinfo' => [
                    'avatarUrl' => $user->getAvatar(),
                    'nickName'  => $user->getNickname()
                ]
            ];
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 根据code获取授权信息
     * @param $code
     * @return array
     */
    public function getAuthByCode($params)
    {
        try {
            $user = $this->app->oauth->userByCode($params['code']);//获取授权用户
            $data = [
                'openid'   => $user->getId(),
                'userinfo' => [
                    'avatarUrl' => $user->getAvatar(),
                    'nickName'  => $user->getNickname()
                ]
            ];
            return $this->success($data);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 获取公众号网页授权code
     * @param $redirect
     * @return array
     */
    public function getAuthCode($redirect)
    {
        try {
            $response = $this->app->oauth->scopes(['snsapi_userinfo'])->redirect($redirect);
            return $this->success($response->getTargetUrl());
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 获取永久二维码
     * @param $uid
     * @return array
     */
    public function getQrcode($uid)
    {
        $result = $this->app->qrcode->forever($uid);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }

        $url = $this->app->qrcode->url($result['data']['ticket']);
        if (isset($url['errcode']) && $url['errcode'] != 0) {
            return $this->error($url, $url["errmsg"]);
        }

        return $this->success($url);
    }

    /**
     * 获取jssdk配置
     * @param $url
     * @return array
     * @throws InvalidArgumentException
     */
    public function getJssdkConfig($url)
    {
        try {
            $this->app->jssdk->setUrl($url);
            $res = $this->app->jssdk->buildConfig([], false, false, false);
            return $this->success($res);
        } catch (\Exception $e) {
            return $this->error([], '公众号配置错误');
        }
    }

    /********************************************************** 数据统计与分析start *******************************************************************/


    /********************************************************** 数据统计与分析end *******************************************************************/


    /*******************************************************************************微信接口连接开始*****************************************************/


    /*******************************************************************************微信接口连接结束*****************************************************/


    /*****************************************************************  微信公众号 统计 start *****************************************************************************************/


    /*****************************************************************  微信公众号 统计 end *****************************************************************************************/


    /****************************************************************************** 数据统计与分析***********************************************************/
    /**
     * 获取用户增减数据, 最大时间跨度：7;
     * @param $from
     * @param $to
     */
    public function userSummary($from, $to)
    {
        try {
            $result = $this->app->data_cube->userSummary($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 获取累计用户数据, 最大时间跨度：7
     * @param $from
     * @param $to
     */
    public function userCumulate($from, $to)
    {
        try {
            $result = $this->app->data_cube->userCumulate($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 获取接口分析分时数据, 最大时间跨度：1;
     * @param $from
     * @param $to
     * @return array|\multitype
     */
    public function interfaceSummaryHourly($from, $to)
    {
        try {
            $result = $this->app->data_cube->interfaceSummaryHourly($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 获取接口分析数据, 最大时间跨度：30;
     * @param $from
     * @param $to
     * @return array|\multitype
     */
    public function interfaceSummary($from, $to)
    {
        try {
            $result = $this->app->data_cube->interfaceSummary($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
    /****************************************************************************** 数据统计与分析***********************************************************/

    /****************************************************************************** 素材start***********************************************************/

    /**
     * 上传图片
     * @param $path
     * @return array
     */
    function uploadImage($path)
    {
        $result = $this->app->material->uploadImage($path);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);
    }

    /**
     * 上传语音
     * @param $path
     * @return array
     */
    function uploadVoice($path)
    {
        $result = $this->app->material->uploadVoice($path);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);
    }

    /**
     * 上传视频
     * @param $path
     * @return array
     */
    function uploadVideo($path)
    {
        $result = $this->app->material->uploadVideo($path);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);
    }

    /**
     * 上传缩略图
     * @param $path
     * @return array
     */
    function uploadThumb($path)
    {
        $result = $this->app->material->uploadThumb($path);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);
    }

    /**
     * 上传图文
     * @param $data
     */
    public function uploadArticle($data)
    {
        $article_data = [];
        foreach ($data as $k => $v) {
            $article_data[] = new Article($v);
        }
        $result = $this->app->material->uploadArticle($article_data);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);
    }

    /**
     * 修改图文
     * @param $mediaId
     * @param $data 文章详情
     * @param int $index 多图文中的第几篇
     * @return array
     */
    public function updateArticle($mediaId, $data, $index = 0)
    {
        $result = $this->app->material->updateArticle($mediaId, $data, $index);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);
    }

    /**
     * 上传图文消息图片
     * @param $path
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function uploadArticleImage($path)
    {
        $result = $this->app->material->uploadArticleImage($path);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);

    }

    /**
     * 获取永久素材
     * @param $mediaId
     * @return array
     */
    public function getMaterial($mediaId)
    {
        $result = $this->app->material->get($mediaId);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);
    }

    /**
     * 删除永久素材
     * @param $mediaId
     * @return array
     */
    public function deleteMaterial($mediaId)
    {
        $result = $this->app->material->delete($mediaId);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->error($result, $result["errmsg"]);
        }
        return $this->success($result);
    }

    /****************************************************************************** 素材end***********************************************************/


    /****************************************************************************** 回复start***********************************************************/

    /**
     * 用户事件
     * @return array
     */
    public function relateWeixin()
    {
        $server  = $this->app->server;
        $message = $server->getMessage();
        if (isset($message['MsgType'])) {
            switch ($message['MsgType']) {
                case 'event':
                    $this->app->server->push(function ($res) {
                        if ($res['Event'] == 'subscribe') {
                            // 关注公众号
                            $Userstr = $this->getUser($res['FromUserName']);
                            if (preg_match("/^qrscene_/", $res['EventKey'])) {
                                $source_uid             = substr($res['EventKey'], 8);
                                $_SESSION['source_uid'] = $source_uid;
                            } elseif (!empty($_SESSION['source_uid'])) {
                                $source_uid = $_SESSION['source_uid'];

                            } else {
                                $source_uid = 0;
                            }
                            $wechat_user = $this->app->user->get($res['FromUserName']);

                            $nickname_decode = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $wechat_user['nickname']);
                            $headimgurl      = $wechat_user['headimgurl'];
                            $sex             = $wechat_user['sex'];
                            $language        = $wechat_user['language'];
                            $country         = $wechat_user['country'];
                            $province        = $wechat_user['province'];
                            $city            = $wechat_user['city'];
                            $district        = "无";
                            $openid          = $wechat_user['openid'];
                            $nickname        = $wechat_user['nickname'];
                            if (!empty($wechat_user['unionid'])) {
                                $unionid = $wechat_user['unionid'];
                            } else {
                                $unionid = '';
                            }
                            $memo = $wechat_user['remark'];
                            $data = array(
                                'nickname'         => $nickname,
                                'nickname_decode'  => $nickname_decode,
                                'headimgurl'       => $headimgurl,
                                'sex'              => $sex,
                                'language'         => $language,
                                'country'          => $country,
                                'province'         => $province,
                                'city'             => $city,
                                'district'         => $district,
                                'openid'           => $openid,
                                'unionid'          => $unionid,
                                'groupid'          => '',
                                'is_subscribe'     => 1,
                                'remark'           => $memo,
                                'subscribe_time'   => $wechat_user['subscribe_time'] ?? 0,
                                'subscribe_scene'  => $wechat_user['subscribe_scene'] ?? 0,
                                'unsubscribe_time' => $wechat_user['unsubscribe_time'] ?? 0,
                                'update_date'      => time()
                            );

                            $fans      = new Fans();
                            $fans_info = $fans->getFansInfo([['openid', '=', $openid]]);
                            if (empty($fans_info['data'])) {
                                $fans->addFans($data);
                            } else {
                                $fans->editFans($data, [['openid', '=', $openid]]);
                            }

                            //获取关注发送消息内容
                            $replay         = new Replay();
                            $replay_content = $replay->getWechatFollowReplay();
                            return new Text($replay_content['data']);
                        } else if ($res['Event'] == 'unsubscribe') {
                            //取消关注
                            $fans   = new Fans();
                            $openid = $res['FromUserName'];
                            $fans->unfollowWechat((string)$openid);
                        } else if ($res['Event'] == 'unsubscribe') {
                            //取消关注
                            $fans   = new Fans();
                            $openid = $res['FromUserName'];
                            $fans->unfollowWechat((string)$openid);
                        } else if ($res['Event'] == 'SCAN') {
                            // SCAN事件 - 用户已关注时的事件推送 - 扫描带参数二维码事件
                            $openid = $res['FromUserName'];
                            $data   = $res['EventKey'];

                        } else if ($res['Event'] == 'CLICK') {
                            // CLICK事件 - 自定义菜单事件
                            $openid = $res['FromUserName'];
                            $data   = $res['EventKey'];

                            if (strpos($res['EventKey'], 'MATERIAL_GRAPHIC_MESSAGE_') === 0) {
                                $material_id   = substr($res['EventKey'], 25);
                                $material_type = 1;
                            } else if (strpos($res['EventKey'], 'MATERIAL_PICTURE_') === 0) {
                                $material_id   = substr($res['EventKey'], 17);
                                $material_type = 2;
                            } else if (strpos($res['EventKey'], 'MATERIAL_TEXT_MESSAGE_') === 0) {
                                $material_id   = substr($res['EventKey'], 22);
                                $material_type = 5;
                            }
                            $material   = new Material();
                            $media_info = $material->getMaterialInfo([['id', '=', $material_id, 'type', '=', $material_type]]);
                            $media_info = $media_info['data'];
                            if ($media_info) {
                                $value = json_decode($media_info['value'], true);
                                if ($material_type == 1) {
                                    //图文
                                    $url   = __ROOT__;
                                    $url   = $url . '/index.php/wechat/api/auth/wechatArticle?id=' . $media_info['id'];
                                    $items = [
                                        new NewsItem([
                                            'title'       => $value[0]['title'],
                                            'description' => strip_tags($value[0]['content']),
                                            'url'         => $url,
                                            'image'       => $value[0]['cover']['path'],
                                        ]),
                                    ];
                                    return new News($items);

                                } else if ($material_type == 2) {
                                    //图片

                                } else if ($material_type == 5) {
                                    //文字
                                    return new Text($value['content']);
                                }
                            }

                        }
                    });
                    $response = $this->app->server->serve();
                    // 将响应输出
                    return $response->send();

                    break;
                case 'text':

                    $this->app->server->push(function ($res) {
                        $replay = new Replay();
                        $rule   = $replay->getSiteWechatKeywordsReplay($res['Content']);

                        if ($rule['data']) {
                            if ($rule['data']['type'] == 'text') {
                                //文字
                                return new Text($rule['data']['reply_content']);
                            } else {

                                $material   = new Material();
                                $media_info = $material->getMaterialInfo([['media_id', '=', $rule['data']['media_id']]]);
                                $media_info = $media_info['data'];

                                if ($media_info) {
                                    $material_type = $media_info['type'];
                                    $value         = json_decode($media_info['value'], true);

                                    if ($material_type == 1) {
                                        $url   = __ROOT__;
                                        $url   = $url . '/index.php/wechat/api/auth/wechatArticle?id=' . $media_info['id'];
                                        $items = [
                                            new NewsItem([
                                                'title'       => $value[0]['title'],
                                                'description' => strip_tags($value[0]['content']),
                                                'url'         => $url,
                                                'image'       => $value[0]['cover']['path'],
                                            ]),
                                        ];
                                        return new News($items);
                                    } else if ($material_type == 2) {
                                        //图片

                                    }
                                }
                            }
                        }

                    });
                    $response = $this->app->server->serve();
                    // 将响应输出
                    return $response->send();

                    break;
                case 'image':
                    //                    return '收到图片消息';
                    break;
                case 'voice':
                    //                    return '收到语音消息';
                    break;
                case 'video':
                    //                    return '收到视频消息';
                    break;
                case 'location':
                    //return '收到坐标消息';
                    break;
                case 'link':
                    //return '收到链接消息';
                    break;
                case 'file':
                    ///return '收到文件消息';
                    // ... 其它消息
                default:
                    //return '收到其它消息';
                    break;
            }
        }
        $response = $this->app->server->serve();
        return $response->send();
    }

    /****************************************************************************** 回复end***********************************************************/

    /****************************************************************************** 模板消息start ***********************************************************/

    /**
     * 添加模板消息
     * @param unknown $shortId
     */
    public function getTemplateId($shortId)
    {
        try {
            $res = $this->app->template_message->addTemplate($shortId);
            return $res;
        } catch (\Exception $e) {
            return ['errcode' => -1, 'errmsg' => $e->getMessage()];
        }
    }

    /**
     * 发送模板消息
     * @param array $param
     * @return Ambigous <\Psr\Http\Message\ResponseInterface, \EasyWeChat\Kernel\Support\Collection, multitype:, object, string>
     */
    public function sendTemplateMessage(array $param)
    {
        try {
            $result = $this->app->template_message->send([
                'touser'      => $param['openid'], // openid
                'template_id' => $param['template_id'],// 模板id
                'url'         => $param['url'],// 跳转链接
                'miniprogram' => $param['miniprogram'], // 跳转小程序  ['appid' => 'xxxxxxx','pagepath' => 'pages/xxx',]
                'data'        => $param['data'] // 模板变量
            ]);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error($result, $result["errmsg"]);
            }
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /****************************************************************************** 模板消息end***********************************************************/
}