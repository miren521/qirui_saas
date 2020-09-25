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

declare (strict_types = 1);

namespace addon\coupon\event;

use app\model\web\Pc;

/**
 * PC端导航
 * @author Administrator
 *
 */
class InitPcNav
{
    public function handle($data)
    {
        $link = [
            [
                'title' => '领券中心',
                'url' => '/coupon',
                'sort' => 4,
            ]
        ];
        $pc_model = new Pc();
        foreach ($link as $k => $v) {
            $pc_model->deleteNav([ ['nav_title','=',$v['title']] ]);
            $sort = $v['sort'];
            unset($v['sort']);
            $add_data = [
                'nav_title' => $v['title'],
                'nav_url' => json_encode($v, 320),
                'sort' => $sort,
                'is_blank' => 0,
                'create_time' => time(),
                'is_show' => 1
            ];
            $pc_model->addNav($add_data);
        }

        return 1;
    }
}
