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


namespace addon\supply\supply\controller;

use app\model\upload\Album as AlbumModel;

/**
 * 相册
 * Class Order
 * @package addon\supply\supply\controller
 */
class Album extends BaseSupply
{


    public function __construct()
    {
        //执行父类构造函数
        parent::__construct();
    }

    /**
     * 图像
     */
    public function lists()
    {
        $album_model = new AlbumModel();
        if (request()->isAjax()) {
            $page      = input('page', 1);
            $limit     = input('limit', PAGE_LIST_ROWS);
            $album_id  = input('album_id', '');
            $pic_name  = input("pic_name", "");
            $order     = input("order", "update_time desc");
            $condition = array(
                ['site_id', "=", $this->supply_id],
                ['album_id', "=", $album_id],
            );
            if (!empty($pic_name)) {
                $condition[] = ['pic_name', 'like', '%' . $pic_name . '%'];
            }
            $list = $album_model->getAlbumPicPageList($condition, $page, $limit, $order);
            return $list;
        } else {
            $album_list = $album_model->getAlbumList([['site_id', "=", $this->supply_id]]);
            $this->assign("album_list", $album_list['data']);
            return $this->fetch('album/lists');
        }
    }

    /**
     * 获取相册分组
     * @return array|void
     */
    function getAlbumList()
    {
        if (request()->isAjax()) {
            $album_model = new AlbumModel();
            $album_list  = $album_model->getAlbumList([['site_id', "=", $this->supply_id]]);
            return $album_list;
        }
    }

    /**
     * 添加分组
     */
    public function addAlbum()
    {
        if (request()->isAjax()) {
            $album_name  = input('album_name', '');
            $data        = array(
                'site_id'    => $this->supply_id,
                'album_name' => $album_name
            );
            $album_model = new AlbumModel();
            $res         = $album_model->addAlbum($data);
            return $res;
        }
    }

    /**
     * 修改分组
     */
    public function editAlbum()
    {
        if (request()->isAjax()) {
            $album_name  = input('album_name');
            $album_id    = input('album_id');
            $data        = array(
                'album_name' => $album_name
            );
            $condition   = array(
                ['site_id', "=", $this->supply_id],
                ['album_id', "=", $album_id]
            );
            $album_model = new AlbumModel();
            $res         = $album_model->editAlbum($data, $condition);
            return $res;
        }
    }

    /**
     * 删除分组
     */
    public function deleteAlbum()
    {
        if (request()->isAjax()) {
            $album_id    = input('album_id');
            $album_model = new AlbumModel();
            $condition   = array(
                ["album_id", "=", $album_id],
                ["site_id", "=", $this->supply_id]
            );
            $res         = $album_model->deleteAlbum($condition);
            return $res;
        }
    }

    /**
     * 修改文件名
     */
    public function modifyPicName()
    {
        if (request()->isAjax()) {
            $pic_id   = input('pic_id', 0);
            $pic_name = input('pic_name', '');
            $album_id = input('album_id', 0);

            $album_model = new AlbumModel();
            $condition   = array(
                ["pic_id", "=", $pic_id],
                ["site_id", "=", $this->supply_id],
                ['album_id', '=', $album_id]
            );
            $data        = array(
                "pic_name" => $pic_name
            );
            $res         = $album_model->editAlbumPic($data, $condition);
            return $res;
        }
    }

    /**
     * 修改图片分组
     */
    public function modifyFileAlbum()
    {
        if (request()->isAjax()) {
            $pic_id      = input('pic_id', 0);//图片id
            $album_id    = input('album_id', 0);//相册id
            $album_model = new AlbumModel();
            $condition   = array(
                ["pic_id", "in", $pic_id],
                ["site_id", "=", $this->supply_id]
            );
            $res         = $album_model->modifyAlbumPicAlbum($album_id, $condition);
            return $res;
        }
    }

    /**
     * 删除图片
     */
    public function deleteFile()
    {
        if (request()->isAjax()) {
            $pic_id      = input('pic_id', 0);//图片id
            $album_id    = input('album_id', 0);
            $album_model = new AlbumModel();
            $condition   = array(
                ["pic_id", "in", $pic_id],
                ["site_id", "=", $this->supply_id],
                ['album_id', '=', $album_id]
            );
            $res         = $album_model->deleteAlbumPic($condition);
            return $res;
        }
    }

    /**
     * 相册管理界面
     * @return mixed
     */
    public function album()
    {
        $album_model = new AlbumModel();
        if (request()->isAjax()) {
            $page_index = input('page', 1);
            $list_rows  = input('limit', PAGE_LIST_ROWS);
            $album_id   = input('album_id', '');
            $pic_name   = input("pic_name", "");
            $condition  = array(
                ['site_id', "=", $this->supply_id],
                ['album_id', "=", $album_id],
            );
            if (!empty($pic_name)) {
                $condition[] = ['pic_name', 'like', '%' . $pic_name . '%'];
            }
            $list = $album_model->getAlbumPicPageList($condition, $page_index, $list_rows, 'update_time desc');
            return $list;
        } else {
            $album_list = $album_model->getAlbumList([['site_id', "=", $this->supply_id]]);
            $this->assign("album_list", $album_list['data']);
            return $this->fetch('album/album');

        }
    }
}
