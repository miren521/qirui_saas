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

namespace app\event;

use app\model\member\MemberLevel;
/**
 * 会员等级变化（执行会员成长值变化）
 */
class UpdateMemberLevel
{
	// 行为扩展的执行入口必须是run
	public function handle($data)
	{
        if($data['account_type'] == 'growth')
        {
            //成长值变化等级检测变化
            $growth_info = model("member")->getInfo([['member_id', '=', $data['member_id']]], 'growth, member_level');
            //查询会员等级
            $member_level = new MemberLevel();
            $level_list = $member_level->getMemberLevelList([['growth', '<=', $growth_info['growth']]], 'level_id, level_name, sort, growth', 'sort desc');
            if(!empty($level_list['data']))
            {
                //检测升级
                if($growth_info['member_level'] == 0)
                {
                    //将用户设置为最大等级
                    $data_level = [
                        'member_level' => $level_list['data'][0]['level_id'],
                        'member_level_name' => $level_list['data'][0]['level_name']
                    ];
                    model("member")->update($data_level, [['member_id', '=', $data['member_id']]]);
                }else{
                    $level_info = $member_level->getMemberLevelInfo([['level_id', '=', $growth_info['member_level']]]);
                    if(empty($level_info['data']))
                    {
                        //将用户设置为最大等级
                        $data_level = [
                            'member_level' => $level_list['data'][0]['level_id'],
                            'member_level_name' => $level_list['data'][0]['level_name']
                        ];
                        model("member")->update($data_level, [['member_id', '=', $data['member_id']]]);
                    }else{
                        if($level_info['data']['sort'] <  $level_list['data'][0]['sort'])
                        {
                            //将用户设置为最大等级
                            $data_level = [
                                'member_level' => $level_list['data'][0]['level_id'],
                                'member_level_name' => $level_list['data'][0]['level_name']
                            ];
                            model("member")->update($data_level, [['member_id', '=', $data['member_id']]]);
                        }
                    }
                }
            }
        }
	}
	
}