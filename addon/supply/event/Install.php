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

namespace addon\supply\event;

use addon\supply\model\Config;
use addon\supply\model\web\Adv;
use addon\supply\model\web\AdvPosition;
use app\model\system\Cron;
use app\model\system\Menu;

/**
 * 应用安装
 */
class Install
{
    /**
     * 执行安装
     */
    public function handle()
    {
        //自动事件
        $cron_model = new Cron();
        $execute_time = strtotime(date("Y-m-d 00:00:00", strtotime('+1 day')));
        $item_result = $cron_model->addCron(2, 1, '轮询检测可关闭的供应商', 'CronSupplyClose', $execute_time, 0, 1);
        if ($item_result[ 'code' ] < 0)
            return $item_result;

        $execute_time = strtotime(date("Y-m-d 00:00:00", strtotime('next month')));
        $item_result = $cron_model->addCron(2, 1, '供应商周期结算', 'SupplyPeriodCalc', $execute_time, 0, 3);
        if ($item_result[ 'code' ] < 0)
            return $item_result;

        //创建广告位
        $adv_position_data = [
            [
                'ap_name' => '供货市场首页广告位',
                'keyword' => 'NS_SUPPLY_SHOP_INDEX',
                'ap_intro' => '',
                'ap_width' => '763',
                'ap_height' => '430',
                'default_content' => '',
                'ap_background_color' => '#FFFFFF',
                'adv' => [
                    [
                        'adv_title' => '广告1',
                        'adv_url' => '',
                        'adv_image' => 'upload/default/supply/adv/adv2.png',
                        'background' => '#FFF'
                    ],
                    [
                    'adv_title' => '广告2',
                    'adv_url' => '',
                    'adv_image' => 'upload/default/supply/adv/adv1.png',
                    'background' => '#FFF'
                ],
                ]
            ]
        ];
        $adv_position_model = new AdvPosition();
        $adv_model = new Adv();
        foreach ($adv_position_data as $k => $v) {
            $adv_data = $v['adv'];
            unset($v['adv']);
            $res_adv_position = $adv_position_model->addAdvPosition($v);
            $ap_id            = $res_adv_position['data'];
            if (!empty($ap_id) && !empty($adv_data)) {
                foreach ($adv_data as $ck => $cv) {
                    $cv['ap_id'] = $ap_id;
                    $adv_model->addAdv($cv);
                }
            }

        }

        //菜单
        $menu = new Menu();
        //更新supply端菜单数据
        $menu->refreshMenu("supply", "supply");
        //更新shop端菜单数据
        $menu->refreshMenu("shop", "supply");
        //更新admin端菜单数据
        $menu->refreshMenu("admin", "supply");

        //添加入驻协议
        $config_model = new Config();
        $agreement_content = '<p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;"><span style="font-size: 14px;">用户使用</span>“<span style="font-size: 14px;">NIUSHOP</span><span style="font-size: 14px;">多商户入驻系统”前请认真阅读并理解本协议内容，本协议内容中以加粗方式显著标识的条款，请用户着重阅读、慎重考虑。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">1.<span style="font-size: 14px; font-family: 宋体;">本协议的订立</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;"><span style="font-size: 14px; font-family: 宋体;">符合本网站商家入驻标准的用户（以下简称</span>“商家”），在同意本协议全部条款后，方有资格使用“<span style="font-size: 14px; font-family: Calibri;">NIUSHOP</span><span style="font-size: 14px; font-family: 宋体;">多商户入驻系统”（以下简称“入驻系统”） 申请入驻。一经商家点击“我已阅读并且同意以上服务协议”按键，即意味着商家同意与本网站签订本协议并同意受本协议约束。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">2.<span style="font-size: 14px; font-family: 宋体;">入驻系统使用说明</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">商家通过入驻系统提出入驻申请，并按照要求填写商家信息、提供商家资质资料后，由本网站审核并与有合作意向的商家联系协商合作相关事宜。</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">3.<span style="font-size: 14px; font-family: 宋体;">商家权利义务</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">3.1 <span style="font-size: 14px; font-family: 宋体;">商家应查看本网站公示的入驻商家标准，并确保资质符合本网站公示的基本要求，商家知悉并理解本网站将结合自身业务发展情况对商家进行选择。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">3.2 <span style="font-size: 14px; font-family: 宋体;">商家应按照本网站要求诚信提供入驻申请所需资料并如实填写相关信息，商家应确保提供的申请资料及信息真实、准确、完整、合法有效，经本网站审核通过后，商家不得擅自修改替换相应资料及主要信息。如商家提供的申请资料及信息不合法、不真实、不准确的，商家需承担因此引起的相应责任及后果，并且本网站有权立即终止商家使用入驻系统的权利。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">3.3 <span style="font-size: 14px; font-family: 宋体;">商家使用入驻系统提交的所有内容应不含有木马等软件病毒、政治宣传、或其他任何形式的“垃圾信息”、违法信息，且商家应按本网站规则使用入驻系统，不得从事影响或可能影响本网站或入驻系统正常运营的行为，否则，本网站有权清除前述内容，并有权立即终止商家使用入驻系统的权利。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">3.4 <span style="font-size: 14px; font-family: 宋体;">商家应注意查看入驻系统公示的入驻申请结果，如审核通过的商家，则按照本网站工作人员的通知按要求办理入驻的相关手续；如审批未通过的商家，则可自本网站通过入驻系统将审批结果告知商家（需商家登陆入驻系统查看）之日起 </span><span style="font-size: 14px; font-family: Calibri;">15 </span><span style="font-size: 14px; font-family: 宋体;">日内提出异议并提供相应资料，如审批仍未通过的，则商家同意提交的申请资料及信息本网站无需返还，由本网站自行处理。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">3.5 <span style="font-size: 14px; font-family: 宋体;">商家不得以任何形式擅自转让或授权他人使用自己在本网站的用户帐号使用入驻系统，否则由此产生的不利后果均由商家自行承担。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">4.<span style="font-size: 14px; font-family: 宋体;">本网站权利义务</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">4.1 <span style="font-size: 14px; font-family: 宋体;">本网站开发的入驻系统仅为商家申请入驻的平台，商家正式入驻后，将在商家后台系统中进行相关操作。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">4.2 <span style="font-size: 14px; font-family: 宋体;">本网站有权对商家提供的资料及信息进行审核，并有权结合自身业务情况对合作商家进行选择；本网站对商家提交资料及信息的审核均不代表本网站对审核内容的真实、合法、准确、完整性作出的确认，商家应对提供资料及信息承担相应的法律责任。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">4.3 <span style="font-size: 14px; font-family: 宋体;">无论商家是否通过本网站的审核，本网站有权对商家提供的资料及信息予以留存并随时查阅，同时，本网站有义务对商家提供的资料予以保密，但国家行政机关、司法机关等国家机构调取资料的除外。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">4.4 <span style="font-size: 14px; font-family: 宋体;">本网站会尽力维护本系统信息的安全，但法律规定的不可抗力，以及因为黑客入侵、计算机病毒侵入发作等原因造成商家资料泄露、丢失、被盗用、被篡改的，本网站不承担任何责任。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">4.5 <span style="font-size: 14px; font-family: 宋体;">本网站应在现有技术支持的基础上确保入驻系统的正常运营，尽量避免入驻系统服务的中断给商家带来的不便。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">5.<span style="font-size: 14px; font-family: 宋体;">知识产权</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">5.1 <span style="font-size: 14px; font-family: 宋体;">本网站开发的入驻系统及其包含的各类信息的知识产权归本网站所有者所有，受国家法律保护</span><span style="font-size: 14px; font-family: Calibri;">,</span><span style="font-size: 14px; font-family: 宋体;">本网站有权不时地对入驻系统的内容进行修改，并在入驻系统中公示，无须另行通知商家。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">5.2 <span style="font-size: 14px; font-family: 宋体;">在法律允许的最大限度范围内，本网站所有者对本协议及入驻系统涉及的内容拥有解释权。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">5.3 <span style="font-size: 14px; font-family: 宋体;">商家未经本网站所有者书面许可，不得擅自使用、非法全部或部分的复制、转载、抓取入驻系统中的信息，否则由此给本网站造成的损失，商家应予以全部赔偿。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">6.<span style="font-size: 14px; font-family: 宋体;">入驻系统服务的终止</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">6.1 <span style="font-size: 14px; font-family: 宋体;">商家自行终止入驻申请，或商家经本网站审批未通过的，则入驻系统服务自行终止。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">6.2 <span style="font-size: 14px; font-family: 宋体;">商家使用本网站或入驻系统时，如违反相关法律法规或者违反本协议规定的，本网站有权随时终止商家使用入驻系统。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">7.<span style="font-size: 14px; font-family: 宋体;">本协议的修改</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">本协议可由本网站随时修订，并将修订后的协议公告于本网站及入驻系统，修订后的条款内容自公告时起生效，并成为本协议的一部分。</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">8.<span style="font-size: 14px; font-family: 宋体;">法律适用与争议解决</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">8.1 <span style="font-size: 14px; font-family: 宋体;">本协议适用中华人民共和国法律。</span></span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">&nbsp;</span></p><p><span style="font-size: 14px; font-family: 微软雅黑, &quot;Microsoft YaHei&quot;;">8.2 <span style="font-size: 14px; font-family: 宋体;">因本协议产生的任何争议，由双方协商解决，协商不成的，任何一方有权向有管辖权的中华人民共和国大陆地区法院提起诉讼。</span></span></p><p><br/></p>';
        $result = $config_model->setApplyAgreement('供应商入驻协议', $agreement_content);
        if ($result[ 'code' ] < 0)
            return $result;

        return success();
    }
}