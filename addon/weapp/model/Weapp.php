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

namespace addon\weapp\model;

use app\model\BaseModel;
use EasyWeChat\Factory;
use think\facade\Cache;

/**
 * 微信小程序配置
 */
class Weapp extends BaseModel
{
    private $app;//微信模型

    public function __construct()
    {
        //微信支付配置
        $config_model = new Config();
        $config_result = $config_model->getWeappConfig();
        $config = $config_result["data"];
        if (!empty($config)) {
            $config_info = $config["value"];
        }

        $config = [
            'app_id' => $config_info["appid"] ?? '',
            'secret' => $config_info["appsecret"] ?? '',

            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'permission' => 0777,
                'file'       => 'runtime/log/wechat/easywechat.logs',
            ],
        ];
        $this->app = Factory::miniProgram($config);
    }

    /**
     * 根据 jsCode 获取用户 session 信息
     * @param $param
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function authCodeToOpenid($param)
    {
        //正常返回的JSON数据包
        //{"openid": "OPENID", "session_key": "SESSIONKEY", "unionid": "UNIONID"}
        //错误时返回JSON数据包(示例为Code无效)
        //{"errcode": 40029, "errmsg": "invalid code"}
        $result = $this->app->auth->session($param['code']);
        if (isset($result['errcode'])) {
            return $this->error('', $result['errmsg']);
        } else {
            Cache::set('weapp_'.$result['openid'], $result);
            return $this->success($result['openid']);
        }
    }

    /**
     * 生成二维码
     * @param $param
     * @return multitype|array
     */
    public function createQrcode($param)
    {
        try {
            $checkpath_result = $this->checkPath($param['qrcode_path']);
            if ($checkpath_result["code"] != 0) return $checkpath_result;
            
            // scene:场景值最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~
            $scene = '';
            if (!empty($param['data'])) {
                foreach ($param['data'] as $key => $value) {
                    if ($scene == '') $scene .= $key . '-' . $value;
                    else $scene .= '&' . $key . '-' . $value;
                }
            }
            $response = $this->app->app_code->getUnlimit($scene, [
                'page' => substr($param['page'], 1),
                'width' => isset($param['width']) ? $param['width'] : 120
            ]);

            if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
                $filename = $param['qrcode_path'] . '/';
                $filename .= $response->saveAs($param['qrcode_path'], $param['qrcode_name'] . '_' . $param['app_type'] . '.png');
                return $this->success(['path' => $filename]);
            } else {
                if (isset($response['errcode']) && $response['errcode'] > 0) {
                    return $this->error('', $response['errmsg']);
                }
            }
            return $this->error();
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }
    
    /**
     * 校验目录是否可写
     * @param unknown $path
     * @return multitype:number unknown |multitype:unknown
     */
    private function checkPath($path)
    {
        if (is_dir($path) || mkdir($path, intval('0755', 8), true)) {
            return $this->success();
        }
        return $this->error('', "directory {$path} creation failed");
    }
    /*************************************************************  数据统计与分析 start **************************************************************/
    /**
     * 访问日趋势
     * @param $from  格式 20170313
     * @param $to 格式 20170313
     */
    public function dailyVisitTrend($from, $to)
    {
        try {
            $result = $this->app->data_cube->dailyVisitTrend($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 访问周趋势
     * @param $from
     * @param $to
     * @return array|\multitype
     */
    public function weeklyVisitTrend($from, $to)
    {
        try {
            $result = $this->app->data_cube->weeklyVisitTrend($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 访问月趋势
     * @param $from
     * @param $to
     * @return array|\multitype
     */
    public function monthlyVisitTrend($from, $to)
    {
        try {
            $result = $this->app->data_cube->monthlyVisitTrend($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 访问分布
     * @param $from
     * @param $to
     */
    public function visitDistribution($from, $to)
    {
        try {
            $result = $this->app->data_cube->visitDistribution($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error($result, $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    /**
     * 访问页面
     * @param $from
     * @param $to
     */
    public function visitPage($from, $to)
    {
        try {
            $result = $this->app->data_cube->visitPage($from, $to);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result["list"]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
    /*************************************************************  数据统计与分析 end **************************************************************/

    /**
     * 消息解密
     * @param array $param
     * @return array
     */
    public function decryptData($param = [])
    {
        try {
            $cache = Cache::get('weapp_' . $param['weapp_openid']);
            $session_key = $cache['session_key'] ?? $param;
            $result = $this->app->encryptor->decryptData($session_key, $param['iv'], $param['encryptedData']);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->error([], $result["errmsg"]);
            }
            return $this->success($result);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
}