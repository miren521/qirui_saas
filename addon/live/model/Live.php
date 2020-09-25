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


namespace addon\live\model;

use app\model\BaseModel;
use addon\weapp\model\Config as WeappConfigModel;
use EasyWeChat\Factory;
use think\Exception;
use think\facade\Log;

/**
 *  好物圈
 */
class Live extends BaseModel
{
    private $app;

    /**
     * 微信直播间接口错误码
     * @var array
     */
    private $room_error = [
        1003 => '商品id不存在',
        47001 => '入参格式不符合规范',
        200002 => '入参错误',
        300001 => '禁止创建/更新商品 或 禁止编辑&更新房间',
        300002 => '名称长度不符合规则',
        300006 => '图片上传失败',
        300022 => '此房间号不存在',
        300023 => '房间状态 拦截（当前房间状态不允许此操作）',
        300024 => '商品不存在',
        300025 => '商品审核未通过',
        300026 => '房间商品数量已经满额',
        300027 => '导入商品失败',
        300028 => '房间名称违规',
        300029 => '主播昵称违规',
        300030 => '主播微信号不合法',
        300031 => '直播间封面图不合规',
        300032 => '直播间分享图违规',
        300033 => '添加商品超过直播间上限',
        300034 => '主播微信昵称长度不符合要求',
        300035 => '主播微信号不存在',
        300036 => '主播微信号未实名认证',
    ];

    /**
     * 微信直播商品接口错误码
     * @var array
     */
    private $goods_error = [
        300001 => '商品创建功能被封禁',
        300002 => '名称长度不符合规则',
        300003 => '价格输入不合规',
        300004 => '商品名称存在违规违法内容',
        300005 => '商品图片存在违规违法内容',
        300006 => '图片上传失败',
        300007 => '线上小程序版本不存在该链接',
        300008 => '添加商品失败',
        300009 => '商品审核撤回失败',
        300010 => '商品审核状态不对',
        300011 => 'API不允许操作非API创建的商品',
        300012 => '没有提审额度（每天500次提审额度）',
        300013 => '提审失败',
        300014 => '审核中，无法删除',
        300017 => '商品未提审',
        300021 => '商品添加成功，审核失败',
    ];


    public function __construct()
    {
        //微信小程序配置
        $weapp_config_model = new WeappConfigModel();
        $weapp_config = $weapp_config_model->getWeappConfig();
        $weapp_config = $weapp_config["data"]["value"];

        $config = [
            'app_id' => $weapp_config["appid"] ?? '',
            'secret' => $weapp_config["appsecret"] ?? '',
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
     * 添加永久图片素材
     * @param $path
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addImageMedia($path){
        try {
            $result = $this->app->media->uploadImage($path);
            if (isset($result['errcode'])) {
                return $this->error('', '图片上传失败');
            } else {
                return $this->success($result);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取直播间列表
     * @param int $start
     * @param int $limit
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRoomList($start = 0, $limit = PAGE_LIST_ROWS){
        try {
            $result = $this->app->live->room->getLiveInfo(['start' => $start, 'limit' => $limit]);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $this->success(['list' => $result['room_info'], 'total' => $result['total'] ]);
            } else {
                return $this->error('', $this->room_error[ abs($result['errcode']) ] ?? $result['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取回放视频
     * @param $room_id
     * @param int $start
     * @param int $limit
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlaybackInfo($room_id, $start = 0, $limit = PAGE_LIST_ROWS){
        try {
            $data = [
                'action' => 'get_replay',
                'room_id' => $room_id,
                'start' => $start,
                'limit' => $limit
            ];
            $result = $this->app->live->room->getLiveInfo($data);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $this->success(['list' => $result['live_replay'], 'total' => $result['total'] ]);
            } else {
                return $this->error('', $this->room_error[ abs($result['errcode']) ] ?? $result['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 创建直播间
     * @param $param
     */
    public function createRoom($param){
        try {
            $result = $this->app->live->room->create($param);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $this->success($result);
            } else {
                return $this->error('', $this->room_error[ abs($result['errcode']) ] ?? $result['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 给直播间添加商品
     * @param int $room_id
     * @param array $goods_ids
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function roomAddGoods(int $room_id, array $goods_ids){
        try {
            $result = $this->app->live->room->addGoods(['roomId' => $room_id, 'ids' => $goods_ids ]);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $this->success($result);
            } else {
                return $this->error('', $this->room_error[ abs($result['errcode']) ] ?? $result['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取商品列表
     * @param int $start
     * @param int $limit
     * @param int $status
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGoodsList($start = 0, $limit = PAGE_LIST_ROWS, $status = 2){
        try {
            $result = $this->app->live->goods->getGoodsList(['offset' => $start, 'limit' => $limit, 'status' => $status]);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $this->success($result);
            } else {
                return $this->error('', $this->goods_error[ abs($result['errcode']) ] ?? $result['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 添加商品审核
     * @param $param
     * @return array
     */
    public function addGoodsAudit($param){
        try {
            $result = $this->app->live->goods->add($param);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $this->success($result);
            } else {
                return $this->error('', $this->goods_error[ abs($result['errcode']) ] ?? $result['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 删除商品
     */
    public function deleteGoods($goods_id){
        try {
            $result = $this->app->live->goods->delete(['goodsId' => $goods_id]);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $this->success($result);
            } else {
                return $this->error('', $this->goods_error[ abs($result['errcode']) ] ?? $result['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }

    /**
     * 获取商品状态
     * @param array $goods_ids
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGoodsStatus(array $goods_ids){
        try {
            $result = $this->app->live->goods->getStatus(['goods_ids' => $goods_ids]);
            if (isset($result['errcode']) && $result['errcode'] == 0) {
                return $this->success($result['goods']);
            } else {
                return $this->error('', $this->goods_error[ abs($result['errcode']) ] ?? $result['errmsg']);
            }
        } catch (\Exception $e) {
            return $this->error('', $e->getMessage());
        }
    }
}