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


namespace addon\live\shop\controller;

use app\model\upload\Upload;
use app\shop\controller\BaseShop;
use addon\live\model\Room as RoomModel;
use addon\live\model\Goods as GoodsModel;

/**
 * 直播间
 */
class Room extends BaseShop
{
    protected $replace = [];    //视图输出字符串内容替换    相当于配置文件中的'view_replace_str'

    public function __construct()
    {
        parent::__construct();
        $this->replace = [
            'LIVE_IMG' => __ROOT__ . '/addon/live/shop/view/public/img',
        ];
    }
    /**
     * 直播间列表
     * @return array|mixed
     */
    public function index()
    {
        $room = new RoomModel();
        if (request()->isAjax()) {
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $status = input('status', '');
            $condition = [
                ['site_id', '=', $this->site_id]
            ];
            if(!empty($status)){
                $condition[] = ['live_status', '=', $status];
            }
            $data = $room->getRoomPageList($condition, '*', 'id desc', $page, $page_size);
            return $data;
        } else {
            $this->assign('status_list', $room->liveStatus);
            return $this->fetch("room/index", [], $this->replace);
        }
    }

    /**
     * 同步直播间
     * @return array
     */
    public function sync(){
        if (request()->isAjax()) {
            $room = new RoomModel();
            $start = input('start', 0);
            $res = $room->syncLiveRoom($start, 20, $this->site_id);
            return $res;
        }
    }

    /**
     * 添加直播间
     */
    public function add(){
        if (request()->isAjax()) {
            $room = new RoomModel();
            $data = [
                'name' => input('name', ''),
                'coverImg' => input('coverImg', ''),
                'startTime' => strtotime(input('startTime', '')),
                'endTime' => strtotime(input('endTime', '')),
                'anchorName' => input('anchorName', ''),
                'anchorWechat' => input('anchorWechat', ''),
                'shareImg' => input('shareImg', ''),
                'type' => input('type', 0),
                'screenType' => 0,
                'closeLike' => input('closeLike', 1),
                'closeGoods' => input('closeGoods', 1),
                'closeComment' => input('closeComment', 1),
                'site_id' => $this->site_id,
                'site_name' => $this->shop_info['site_name']
            ];
            $res = $room->addRoom($data);
            return $res;
        }
        return $this->fetch("room/add");
    }


    /**
     * 编辑直播间
     */
    public function edit(){
        $id = input('id', 0);
        $condition = array(
            ['site_id', '=', $this->site_id],
            ['id', '=', $id]
        );
        $room = new RoomModel();
        if (request()->isAjax()) {
            $data = array(
                'name' => input('name'),
                'cover_img' => input('coverImg'),
                'share_img' => input('shareImg'),
                'start_time' => strtotime(input('startTime')),
                'end_time' => strtotime(input('endTime')),
                'anchor_name' => input('anchorName'),
//                'live_status' => '102',
                'audit_status' => 0,
                'goods' => '{}',
                'type' => input('type'),
                'close_like' => input('closeLike', 1),
                'close_goods' => input('closeGoods', 1),
                'close_comment' => input('closeComment', 1),
                'anchor_wechat' => input('anchorWechat'),
                'audit_status' => 0,
                'site_name' => $this->shop_info['site_name']
            );

            $res = $room->updateRoomInfo($data, $condition);
            return $res;
        }else{
            $info_result = $room->getRoomInfo($condition);
            $this->assign('info', $info_result['data']);
            return $this->fetch("room/edit");
        }

    }

    /**
     * 添加图片素材
     */
    public function addImageMedia(){
        if (request()->isAjax()) {
            $upload_model = new Upload($this->site_id);
            $thumb_type = input("thumb", "");
            $name = input("name", "");
            $param = array(
                "thumb_type" => "",
                "name" => "file"
            );
            $path = $this->site_id > 0 ? "common/images/" . date("Ymd") . '/' : "common/images/" . date("Ymd") . '/';
            $result = $upload_model->setPath($path)->image($param);
            return $result;
        }
    }

    /**
     * 运营
     */
    public function operate(){
        $room = new RoomModel();
        if (request()->isAjax()) {
            $id = input('id', '');
            $anchor_img = input('anchor_img', '');
            $banner = input('banner', '');
            $data = [];
            if (!empty($anchor_img)) $data = ['anchor_img' => $anchor_img];
            if (!empty($banner)) $data = ['banner' => $banner];
            $res = $room->updateRoomInfo($data, [ ['site_id', '=', $this->site_id ], ['id', '=', $id ] ]);
            return $res;
        }else{
            $id = input('id', '');
            $room_info = $room->getRoomInfo([ ['site_id', '=', $this->site_id ], ['id', '=', $id ] ]);
            if (empty($room_info['data'])) return $this->error('未获取到直播间信息');
            $this->assign('room_info', $room_info['data']);
            return $this->fetch("room/operate");
        }
    }

    /**
     * 查询商品
     */
    public function getGoodsPageList(){
        if (request()->isAjax()) {
            $goods = new GoodsModel();
            $page = input('page', 1);
            $page_size = input('page_size', PAGE_LIST_ROWS);
            $sku_id = input('sku_id', '');
            $condition = [
                ['site_id', '=', $this->site_id],
                ['status', '=', 2]
            ];
            if (!empty($sku_id)) $condition[] = ['sku_id', 'not in', explode(',', $sku_id) ];
            $data = $goods->getGoodsPageList($condition, '*', 'id desc', $page, $page_size);
            return $data;
        }
        $ids = input('ids', '');
        $this->assign('ids', $ids);
        return $this->fetch("room/goods_select");
    }

    /**
     * 添加商品到直播间
     * @return array
     */
    public function addGoods(){
        if (request()->isAjax()) {
            $room = new RoomModel();
            $room_id = input('room_id', '');
            $data = input('data', '');
            $res = $room->addGoods($this->site_id, $room_id, $data);
            return $res;
        }
    }
}