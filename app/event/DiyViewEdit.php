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
declare (strict_types=1);

namespace app\event;

use app\Controller;
use app\model\web\DiyView as DiyViewModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 自定义页面编辑
 */
class DiyViewEdit extends Controller
{
    /**
     * 行为扩展的执行入口必须是run
     * @param $data
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function handle($data)
    {
        $diy_view = new DiyViewModel();

        // 自定义模板组件集合
        $utils = $diy_view->getDiyViewUtilList($data['condition']);

        $diy_view_info = [];
        // 推广码
        $qrcode_info = [];
        if (!empty($data['id'])) {
            $diy_view_info = $diy_view->getSiteDiyViewDetail([
                ['sdv.site_id', '=', $data['site_id']],
                ['sdv.id', '=', $data['id']]
            ]);
            $qrcode_info   = $diy_view->qrcode([
                ['site_id', '=', $data['site_id']],
                ['id', '=', $data['id']]
            ]);
        } elseif (!empty($data['name'])) {
            $condition     = [
                ['sdv.site_id', '=', $data['site_id']],
                ['sdv.name', '=', $data['name']]
            ];
            $qrcode_info   = $diy_view->qrcode([
                ['site_id', '=', $data['site_id']],
                ['name', '=', $data['name']]
            ]);
            $diy_view_info = $diy_view->getSiteDiyViewDetail($condition);
        }

        if (!empty($diy_view_info) && !empty($diy_view_info['data'])) {
            $diy_view_info = $diy_view_info['data'];
        }

        if (!empty($qrcode_info)) {
            $qrcode_info = $qrcode_info['data'];
            // 目前只支持H5
            if ($qrcode_info['path']['h5']['status'] != 1) {
                $qrcode_info = [];
            }
        }

        $diy_view_utils = [];
        if (!empty($utils['data'])) {
            // 先遍历，组件分类
            foreach ($utils['data'] as $k => $v) {
                $value              = [];
                $value['type']      = $v['type'];
                $value['type_name'] = $diy_view->getTypeName($v['type']);
                $value['list']      = [];
                if (!in_array($value, $diy_view_utils)) {
                    array_push($diy_view_utils, $value);
                }
            }

            // 遍历每一个组件，将其添加到对应的分类中
            foreach ($utils['data'] as $k => $v) {
                foreach ($diy_view_utils as $diy_k => $diy_v) {
                    if ($diy_v['type'] == $v['type']) {
                        array_push($diy_view_utils[$diy_k]['list'], $v);
                    }
                }
            }
        }

        $this->assign("time", time());
        $this->assign("name", isset($data['name']) ? $data['name'] : '');
        $this->assign("qrcode_info", $qrcode_info);
        $this->assign('diy_view_utils', $diy_view_utils);
        $this->assign("diy_view_info", $diy_view_info);

        $this->assign("app_module", $data['app_module']);
        $request_url = $data['app_module'] . '/diy/edit';
        $this->assign("request_url", $request_url);

        $this->assign("extend_base", 'app/' . $data['app_module'] . '/view/base.html');

        $template = dirname(realpath(__DIR__)) . '/admin/view/diy/edit.html';
        return $this->fetch($template);
    }
}
