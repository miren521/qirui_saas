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
use app\model\system\Cron;
use app\model\upload\Upload;

class Room extends BaseModel
{
    public $liveStatus = [
        '101' => '直播中',
        '102' => '未开始',
        '103' => '已结束',
        '104' => '禁播',
        '105' => '暂停中',
        '106' => '异常',
        '107' => '已过期',
    ];

    public $audit_status = [
        0 => '待审核',
        -1 => '已拒绝',
        1 => '已审核',
    ];

    /**
     * 创建直播间
     * @param $data
     * @param $site_id
     */
    public function createRoom($data, $site_id){
        $live = new Live();
        $res = $live->createRoom($data);
        if($res['code'] > 0){
            $data['site_id'] = $site_id;
            $data['roomid'] = $res['data']['roomId'];

            $this->addRoom($data);//本地增加直播间
        }
        return $res;
    }

    /**
     * 添加直播间
     * @param $data
     */
    public function addRoom($data){
        $liver_data = array(
            'site_id' => $data['site_id'],
            'name' => $data['name'],
            'cover_img' => $data['coverImg'],
            'share_img' => $data['shareImg'],
            'start_time' => $data['startTime'],
            'end_time' => $data['endTime'],
            'anchor_name' => $data['anchorName'],
            'live_status' => '102',
            'audit_status' => 0,
            'goods' => '{}',
            'type' => $data['type'],
            'close_like' => $data['closeLike'],
            'close_goods' => $data['closeGoods'],
            'close_comment' => $data['closeComment'],
            'anchor_wechat' => $data['anchorWechat'],
            'site_name' => $data['site_name']
        );
        $res = model('weapp_live_room')->add($liver_data);
        return $this->success($res);
    }
    /**
     * 编辑直播间信息
     * @param array $data
     * @param array $where
     */
    public function updateRoomInfo($data = [], $where = []){
        $res = model('weapp_live_room')->update($data, $where);
        return $this->success($res);
    }

    /**
     * 获取直播间列表
     * @param array $condition
     * @param bool $field
     * @param string $order
     * @param int $page
     * @param int $list_rows
     * @param string $alias
     * @param array $join
     * @return array
     */
    public function getRoomPageList($condition = [], $field = true, $order = '', $page = 1, $list_rows = PAGE_LIST_ROWS, $alias = 'a', $join = []){
        $data = model('weapp_live_room')->pageList($condition, $field, $order, $page, $list_rows, $alias, $join);
        if (!empty($data['list'])) {
            foreach ($data['list'] as $k => $item) {
                $data['list'][$k]['status_name'] = $this->liveStatus[ $item['live_status'] ] ??  '';
                if (isset($item['goods'])) $data['list'][$k]['goods'] = json_decode($item['goods'], true);
            }
        }
        return $this->success($data);
    }

    /**
     * 同步直播间列表
     */
    public function syncLiveRoom($start, $limit){
        $live = new Live();
        $result = $live->getRoomList($start, $limit);
        if ($result['code'] < 0) return $result;

        $room_list = $result['data']['list'] ?? [];
        if (!empty($room_list)) {
            foreach ($room_list as $item) {
                $room_info = model('weapp_live_room')->getInfo([ ['roomid', '=', $item['roomid'] ]], '*');
                if (!empty($room_info)) {
                    $data = [
                        'name' => $item['name'],
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                        'anchor_name' => $item['anchor_name'],
                        'goods' => json_encode($item['goods'], JSON_UNESCAPED_UNICODE),
                        'live_status' => $item['live_status'],
                    ];

                    $upload = new Upload($room_info['site_id']);
                    $upload->setPath('upload/live/room/');
                    //分享图片
                    if (is_url($item['share_img'])) {
                        $pull_result = $upload->remotePull($item['share_img']);
                        $pull_result = $pull_result['data'];
                        if (isset($pull_result['pic_path']) && !empty($pull_result['pic_path'])) {
                            $data['share_img'] = $pull_result['pic_path'];
                        }
                    }
                    //背景图片
                    if (is_url($item['cover_img'])) {
                        $pull_result = $upload->remotePull($item['cover_img']);
                        $pull_result = $pull_result['data'];
                        if (isset($pull_result['pic_path']) && !empty($pull_result['pic_path'])) {
                            $data['cover_img'] = $pull_result['pic_path'];
                        }
                    }


                    model('weapp_live_room')->update($data, [ ['id', '=', $room_info['id'] ] ]);
                }
            }
            $total_page = ceil($result['data']['total'] / $limit);
            return $this->success(['page' => $start, 'total_page' => $total_page ]);
        } else {
            return $this->success(['page' => $start, 'total_page' => 1 ]);
        }
    }

    /**
     * 获取直播间信息
     */
    public function getRoomInfo($condition = [], $field = '*'){
        $data = model('weapp_live_room')->getInfo($condition, $field);
        if (!empty($data)) {
            $data['status_name'] = $this->liveStatus[ $data['live_status'] ] ??  '';
            if (isset($data['goods']) && !empty($data['goods'])) $data['goods'] = json_decode($data['goods'], true);
        }
        return $this->success($data);
    }

    /**
     * 添加商品到直播间
     * @param $site_id
     * @param $room_id
     * @param $data
     */
    public function addGoods($site_id, $room_id, $data){
        if (empty($data)) return $this->error('', '请先选择要添加的商品');
        $room_info = model('weapp_live_room')->getInfo([ ['site_id', '=', $site_id ], ['roomid', '=', $room_id ] ], 'goods');
        if (empty($room_info)) return $this->error('', '未查找到直播间信息');

        $data = json_decode($data, true);

        $goods_ids = [];
        $goods_data = [];

        foreach ($data as $item) {
            array_push($goods_ids, $item['goods_id']);
            array_push($goods_data, [
                'name' => $item['name'],
                'cover_img' => $item['cover_img'],
                'url' => $item['url'],
                'price' => $item['price']
            ]);
        }

        $live = new Live();
        $result = $live->roomAddGoods($room_id, $goods_ids);
        if ($result['code'] < 0) return $result;

        if (!empty($room_info['goods'])) {
            $room_goods = json_decode($room_info['goods'], true);
            $goods_data = array_merge($room_goods, $goods_data);
        }

        $res = model('weapp_live_room')->update([ 'goods' => json_encode($goods_data, JSON_UNESCAPED_UNICODE) ], [ ['site_id', '=', $site_id ], ['roomid', '=', $room_id ] ]);
        return $this->success($res);
    }

    /**
     * 轮询更新直播间状态
     */
    public function updateRoomStatus(){
        $condition = array(
            ['live_status', 'in', ['101', '102', '105'] ],
        );

        $count = model('weapp_live_room')->getCount($condition);
        if ($count) {
            $start = 0;
            $result = $this->syncLiveRoom($start, 20);
            if (isset($result['code']) && $result['code'] == 0 && $result['total_page'] > 1) {
                for ($i = 1; $i < $result['data']; $i++) {
                    $this->syncLiveRoom($i, 20);
                }
            }
        }
    }




    /******************************************************************** 审核start **************************************************************/

    /**
     * 通过直播间审核
     * @param $id
     */
    public function agree($id){
        $condition = array(
            'id' => $id
        );
        $info = model('weapp_live_room')->getInfo($condition);
        if(!empty($info)){
            $live = new Live();

            //分享素材id

            $upload_model = new Upload($info['site_id']);
            $share_img = $upload_model->setPath("common/temp/" . date("Ymd") . '/')->remotePullToLocal($info['share_img'])['data']['path'] ?? '';
            $share_media_result = $live->addImageMedia($share_img);
            if ($share_media_result['code'] < 0)
                return $share_media_result;

            $share_media_id = $share_media_result['data']['media_id'] ?? '';
            //背景素材id
            $cover_img = $upload_model->setPath("common/temp/" . date("Ymd") . '/')->remotePullToLocal($info['cover_img'])['data']['path'] ?? '';
            $cover_media_result = $live->addImageMedia($cover_img);
            if ($cover_media_result['code'] < 0)
                return $cover_media_result;

            $cover_media_id = $cover_media_result['data']['media_id'] ?? '';
            $create_data = [
                'name' => $info['name'],
                'coverImg' => $cover_media_id,
                'startTime' => $info['start_time'],
                'endTime' => $info['end_time'],
                'anchorName' => $info['anchor_name'],
                'anchorWechat' => $info['anchor_wechat'],
                'shareImg' => $share_media_id,
                'type' => $info['type'],
                'screenType' => 0,
                'closeLike' => $info['close_like'],
                'closeGoods' => $info['close_goods'],
                'closeComment' => $info['close_comment'],
            ];
            $res = $live->createRoom($create_data);
            if($res['code'] < 0){
                return $res;
            }
            $data = array(
                'audit_status' => 1
            );
            $data['roomid'] = $res['data']['roomId'];
            $res = model('weapp_live_room')->update($data, $condition);
            return $this->success($res);
        }else{
            return $this->error();
        }
    }


    /**
     * 拒绝直播间审核
     * @param $id
     * @param $reason
     */
    public function refuse($id, $reason){
        $data = array(
            'audit_status' => -1,
            'refuse_reason' => $reason
        );
        $condition = array(
            'id' => $id
        );
        $res = model('weapp_live_room')->update($data, $condition);
        return $this->success($res);
    }
    /******************************************************************** 审核end **************************************************************/
}